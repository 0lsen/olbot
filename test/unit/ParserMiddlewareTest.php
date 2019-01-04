<?php

include_once 'SettingsMock.php';

class ParserMiddlewareTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        if (file_exists('../TestMocks.php'))
            include_once '../TestMocks.php';
        parent::setUp();
    }

    function testFindSubjectCandidatesAndMathExpression()
    {
        $settings = new SettingsMock(new \OLBot\Settings\ParserSettings(
            [1 => ['class' => 'Math', 'settings' => ['phpythagorasSettings' => []]]],
            [],
            ['fallbackLanguage' => '', 'typicalLanguageEnding' => ''],
            ['“' => '”', '\'' => '\''],
            [':', 'in ']
        ));

        $storage = new \OLBot\Service\StorageService($settings);
        $storage->textCopy = '{123}foo “abc \'def” ghi\' jkl: mno “pqr in stu”';
        $detector = new \OLBot\Middleware\ParserMiddleware($storage);

        $this->mockKeywords();

        $detector(new RequestMock(), new \Slim\Http\Response(), function ($a, $b) {return $b;});

        $this->assertEncapsulatedText('“abc.*def”', $storage->textCopy);
        $this->assertEncapsulatedText('\'def.*ghi\'', $storage->textCopy);
        $this->assertEncapsulatedText('“pqr.*stu”', $storage->textCopy);
        $this->assertEncapsulatedText(' mno.*stu”', $storage->textCopy);

        $this->assertEquals('abc \'def', $storage->subjectCandidates[0]->text);
        $this->assertEquals('pqr in stu', $storage->subjectCandidates[1]->text);
        $this->assertEquals('def” ghi', $storage->subjectCandidates[2]->text);
        $this->assertEquals(' mno “pqr in stu”', $storage->subjectCandidates[3]->text);
        $this->assertEquals('stu”', $storage->subjectCandidates[4]->text);
    }

    private function mockKeywords()
    {
        $keywordMock = Mockery::mock('alias:OLBot\Model\DB\Keyword');
        $keywordMock
            ->shouldReceive('find')
            ->with(md5('foo'))
            ->andReturnNull();
        $keywordMock
            ->shouldReceive('find')
            ->with(md5('ghi'))
            ->andReturnNull();
        $keywordMock
            ->shouldReceive('find')
            ->with(md5('jkl'))
            ->andReturnNull();
        $keywordMock
            ->shouldReceive('find')
            ->with(md5('mno'))
            ->andReturnNull();
        $keywordMock
            ->shouldReceive('find')
            ->with(md5('pqr'))
            ->andReturnNull();
        $keywordMock
            ->shouldReceive('find')
            ->with(md5('stu'))
            ->andReturnNull();
    }

    private function assertEncapsulatedText($needle, $text) {
        $this->assertRegExp('#{(?<i>\d)}(|{.+})'.$needle.'(|{.+}){/(?P=i)}#', $text);
    }
}

class RequestMock extends \Slim\Http\Request {
    public function __construct()
    {
    }
}