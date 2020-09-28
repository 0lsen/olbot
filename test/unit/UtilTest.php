<?php


use OLBot\Util;
use OLBotSettings\Model\StringTuple;

class UtilTest extends \PHPUnit\Framework\TestCase
{
    function testGetWords()
    {
        $this->assertEquals(2, sizeof(Util::getWords("ab cde fghi")));
        $this->assertEquals(0, sizeof(Util::getWords("a")));
        $this->assertEquals(0, sizeof(Util::getWords("")));
    }

    function testReplace()
    {
        $replace = [
            new StringTuple(["key" => "search1", "value" => 'replace1']),
            new StringTuple(["key" => "search2", "value" => 'replace2']),
        ];

        $this->assertEquals("asd replace1 replace1 qwereplace2qwe replace1", Util::replace("asd search1 replace1 qwesearch2qwe replace1", $replace));
        $this->assertEquals("", Util::replace("", $replace));
    }

    function testTextSimilarityNegative()
    {
        $this->assertFalse(Util::textIsSimilar(
            'this is not the same',
            'as this very different text'
        ));

        $this->assertFalse(Util::textIsSimilar(
            'totally similar',
            'totally similar but much longer'
        ));

        $this->assertFalse(Util::textIsSimilar(
            'totally similar but much longer',
            'totally similar'
        ));
    }

    function testTextSimilarityPositive()
    {
        $this->assertTrue(Util::textIsSimilar(
            'the wheels on the bus go round and round',
            'round and round go the wheels on the bus'
        ));
    }
}