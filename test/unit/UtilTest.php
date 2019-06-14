<?php


class UtilTest extends \PHPUnit\Framework\TestCase
{
    function testTextSimilarityNegative()
    {
        $this->assertFalse(\OLBot\Util::textIsSimilar(
            'this is not the same',
            'as this very different text'
        ));

        $this->assertFalse(\OLBot\Util::textIsSimilar(
            'totally similar',
            'totally similar but much longer'
        ));

        $this->assertFalse(\OLBot\Util::textIsSimilar(
            'totally similar but much longer',
            'totally similar'
        ));
    }

    function testTextSimilarityPositive()
    {
        $this->assertTrue(\OLBot\Util::textIsSimilar(
            'the wheels on the bus go round and round',
            'round and round go the wheels on the bus'
        ));
    }
}