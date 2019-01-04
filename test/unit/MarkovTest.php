<?php

include_once 'SettingsMock.php';

use OLBot\Category\AbstractCategory;
use OLBot\Category\Markov;
use OLBot\Service\StorageService;

class MarkovTest extends \PHPUnit\Framework\TestCase
{
    function testMe()
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
}

