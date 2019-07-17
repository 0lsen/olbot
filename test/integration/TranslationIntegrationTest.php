<?php

use OLBot\Category\AbstractCategory;
use OLBot\Category\Translation;
use OLBot\Model\SubjectCandidate;
use OLBot\Service\StorageService;

include_once 'IntegrationSettingsMock.php';

/**
 * @te
 */
class TranslationIntegrationTest extends \PHPUnit\Framework\TestCase
{
    function testWithNonsenseSecondSubjectCandidate()
    {
        if (!defined('PROJECT_ROOT')) {
            define('PROJECT_ROOT', __DIR__ . '/../..');
        }

        AbstractCategory::$storageService = new StorageService(new IntegrationSettingsMock());
        AbstractCategory::$storageService->subjectCandidates[] = new SubjectCandidate(SubjectCandidate::DELIMITER, ':', 'Bonjour');
        AbstractCategory::$storageService->subjectCandidates[] = new SubjectCandidate(SubjectCandidate::QUOTATION, '"', 'foo bar');

        /** @var \OLBot\Settings $testSettings */
        $testSettings = require PROJECT_ROOT . '/app/config/olbot_test.php';

        $translation = new Translation(
            1,
            0,
            $testSettings->parser->categories[7]['settings'],
            1
        );

        $translation->generateResponse();

        $this->assertEquals(
            "`fr-en`\nHello",
            AbstractCategory::$storageService->response->text[0]
        );
    }

}