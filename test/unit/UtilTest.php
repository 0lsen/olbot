<?php


use OLBot\Util;

class UtilTest extends \PHPUnit\Framework\TestCase
{
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