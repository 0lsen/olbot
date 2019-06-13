<?php

include_once 'SettingsMock.php';

use OLBot\Category\AbstractCategory;
use OLBot\Category\Markov;
use OLBot\Service\StorageService;

class MarkovTest extends \PHPUnit\Framework\TestCase
{
    function testOneElementLength()
    {
        if (!defined('PROJECT_ROOT')) {
            define('PROJECT_ROOT', __DIR__ . '/../..');
        }

        AbstractCategory::$storageService = new StorageService(new SettingsMock());

        $markov = new Markov(
            1,
            null,
            ['markovSettings' => [
                'ignoreCache' => true,
                'resources' => ['test.txt'],
                'sentenceThreshold' => 2
            ]],
            1
        );

        srand(1337);
        $markov->generateResponse();

        $this->assertEquals(
            'Our knowledge has poisoned men\'s happiness. The way of democracy, let us all men\'s souls, has barricaded the way of men with national barriers!',
            AbstractCategory::$storageService->response->text[0]
        );
    }

    function testTwoElementLength() {
        if (!defined('PROJECT_ROOT')) {
            define('PROJECT_ROOT', __DIR__ . '/../..');
        }

        AbstractCategory::$storageService = new StorageService(new SettingsMock());

        $markov = new Markov(
            1,
            null,
            ['markovSettings' => [
                'ignoreCache' => true,
                'resources' => ['test.txt'],
                'sentenceThreshold' => 3,
                'elementLength' => 2
            ]],
            1
        );

        srand(123);
        $markov->generateResponse();

        $this->assertEquals(
            'We think too much and feel too little. I\'m sorry, but I don\'t want to rule or conquer anyone. Fight for liberty!',
            AbstractCategory::$storageService->response->text[0]
        );
    }
}
