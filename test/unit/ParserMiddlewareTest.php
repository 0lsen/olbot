<?php

use OLBot\Middleware\ParserMiddleware;
use OLBot\Service\CacheService;
use OLBot\Service\StorageService;
use OLBotSettings\Model\CacheSettings;
use OLBotSettings\Model\Math;
use OLBotSettings\Model\ParserSettings;
use OLBotSettings\Model\Settings;
use OLBotSettings\Model\StringTuple;

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
        $settings = $this->createSettings();

        $storage = new StorageService($settings);
        $storage->textCopy = '{123}foo “abc \'def” ghi\' jkl: mno “pqr in stu”';
        $cache = new CacheService(new CacheSettings());
        $detector = new ParserMiddleware($storage, $cache);

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

    private function createSettings() {
        $settings = new Settings();
        $parserSettings = new ParserSettings();
        $parserSettings->setCategories([new Math()]);
        $parserSettings->setQuotationMarks([
            $this->createStringTuple('“', '”'),
            $this->createStringTuple('\'', '\'')
        ]);
        $parserSettings->setSubjectDelimiters([':', 'in ']);
        $settings->setParser($parserSettings);
        return $settings;
    }

    private function createStringTuple(string $key, string $value) {
        $tuple = new StringTuple();
        $tuple->setKey($key);
        $tuple->setValue($value);
        return $tuple;
    }

    private function mockKeywords()
    {
        $keywordMock = Mockery::mock('alias:OLBot\Model\DB\Keyword');
        $keywordMock
            ->shouldReceive('find')
            ->with(md5('abc'))
            ->andReturnNull();
        $keywordMock
            ->shouldReceive('find')
            ->with(md5('def'))
            ->andReturnNull();
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