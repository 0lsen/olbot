<?php


use OLBot\Category\AbstractCategory;
use OLBot\Model\SubjectCandidate;
use OLBot\Service\StorageService;

require_once 'IntegrationSettingsMock.php';

class MathIntegrationsTest extends \PHPUnit\Framework\TestCase
{
    function testasd() {
        if (!defined('PROJECT_ROOT')) {
            define('PROJECT_ROOT', __DIR__ . '/../..');
        }

        $storageService = new StorageService(new IntegrationSettingsMock());
        $storageService->subjectCandidates[] = new SubjectCandidate(SubjectCandidate::DELIMITER, ':', '1 + 1');
        $storageService->textCopy = '1.2 + 2.3';
        AbstractCategory::setStorageService($storageService);

        $settingsArray = require PROJECT_ROOT . '/app/config/olbot_test.php';
        /** @var \OLBotSettings\Model\Settings $testSettings */
        $testSettings = \OLBotSettings\ObjectSerializer::deserialize(json_decode(json_encode($settingsArray)), 'OLBotSettings\Model\Settings');

        $math = new \OLBot\Category\Math(
            1,
            0,
            $testSettings->getParser()->getCategories()[0],
            1
        );

        $math->generateResponse();

        $this->assertEquals(
            "<code>1.2 + 2.3 = 3.5</code>",
            $storageService->response->text[0]
        );
    }
}