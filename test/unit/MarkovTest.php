<?php

include_once 'SettingsMock.php';

use OLBot\Category\AbstractCategory;
use OLBot\Category\Markov;
use OLBot\Service\CacheService;
use OLBot\Service\StorageService;
use OLBotSettings\Model\CacheSettings;
use OLBotSettings\Model\MarkovSettings;

class MarkovTest extends \PHPUnit\Framework\TestCase
{
    function testOneElementLength()
    {
        if (!defined('PROJECT_ROOT')) {
            define('PROJECT_ROOT', __DIR__ . '/../..');
        }

        $storageService = new StorageService(new SettingsMock());
        AbstractCategory::setStorageService($storageService);
        AbstractCategory::setCacheService(new CacheService(new CacheSettings()));

        $markov = new Markov(
            1,
            null,
            $this->createMarkovSettings(2, null),
            1
        );

        srand(1337);
        $markov->generateResponse();

        $this->assertEquals(
            'Our knowledge has poisoned men\'s happiness. The way of democracy, let us all men\'s souls, has barricaded the way of men with national barriers!',
            $storageService->response->text[0]
        );
    }

    function testTwoElementLength() {
        if (!defined('PROJECT_ROOT')) {
            define('PROJECT_ROOT', __DIR__ . '/../..');
        }

        $storageService = new StorageService(new SettingsMock());
        AbstractCategory::setStorageService($storageService);
        AbstractCategory::setCacheService(new CacheService(new CacheSettings()));

        $markov = new Markov(
            1,
            null,
            $this->createMarkovSettings(3, 2),
            1
        );

        srand(123);
        $markov->generateResponse();

        $this->assertEquals(
            'We think too much and feel too little. I\'m sorry, but I don\'t want to rule or conquer anyone. Fight for liberty!',
            $storageService->response->text[0]
        );
    }

    private function createMarkovSettings(int $sentenceThreshold, ?int $elementLength) {
        $settings = new \OLBotSettings\Model\Markov();
        $markovSettings = new MarkovSettings();
        $markovSettings->setResources(['test.txt']);
        $markovSettings->setSentenceThreshold($sentenceThreshold);
        $markovSettings->setElementLength($elementLength);
        $settings->setMarkovSettings($markovSettings);
        return $settings;
    }
}
