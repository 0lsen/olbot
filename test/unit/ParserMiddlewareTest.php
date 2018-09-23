<?php

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
            [1 => ['class' => 'Math',]],
            [],
            ['decimalPoint' => '', 'divisionByZeroResponse' => ''],
            ['fallbackLanguage' => '', 'typicalLanguageEnding' => ''],
            '"\'',
            ':'
        ));

        $storage = new \OLBot\Service\StorageService($settings);
        $storage->textCopy = '{123}foo 1+1 "abc \'def" ghi\' jkl: mno "pqr stu"';
        $detector = new \OLBot\Middleware\ParserMiddleware($storage);

        $this->mockKeywords();

        $detector(new RequestMock(), new \Slim\Http\Response(), function ($a, $b) {return $b;});

        $this->assertEncapsulatedText('"abc.*def"', $storage->textCopy);
        $this->assertEncapsulatedText('\'def.*ghi\'', $storage->textCopy);
        $this->assertEncapsulatedText('"pqr.*stu"', $storage->textCopy);
        $this->assertEncapsulatedText(' mno.*stu"', $storage->textCopy);

        $this->assertEquals('1+1 = 2', $storage->response->math[0]);
        $this->assertEquals('abc \'def', $storage->subjectCandidates[0]->text);
        $this->assertEquals('pqr stu', $storage->subjectCandidates[1]->text);
        $this->assertEquals('def" ghi', $storage->subjectCandidates[2]->text);
        $this->assertEquals(' mno "pqr stu"', $storage->subjectCandidates[3]->text);

        $this->assertEquals('<code>1+1 = 2</code>', $storage->response->text[0]);
    }

    private function mockKeywords()
    {
        $keywordMock = Mockery::mock('alias:OLBot\Model\DB\Keyword');
        $keywordMock
            ->shouldReceive('find')
            ->with(md5('foo'))
            ->andReturn(new EloquentMock(['category' => 1]));
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

class SettingsMock extends \OLBot\Settings {
    public function __construct($parser)
    {
        $this->parser = $parser;
    }
}