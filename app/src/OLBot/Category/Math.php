<?php

namespace OLBot\Category;


use OLBot\Adapter\PHPythagoras as PHPythagorasAdapter;
use OLBot\Service\MessageService;
use OLBotSettings\Model\Math as MathSettings;
use Telegram\Model\ParseMode;

class Math extends AbstractCategory
{
    private $phpythagorasSettings;
    private $results;

    /**
     * Math constructor.
     * @param int $categoryNumber
     * @param int|null $subjectCandidateIndex
     * @param MathSettings $settings
     * @param array $categoryHits
     * @throws \PHPythagoras\ApiException
     */
    public function __construct(int $categoryNumber, ?int $subjectCandidateIndex, MathSettings $settings, $categoryHits = [])
    {
        $this->phpythagorasSettings = $settings->getPhpythagorasSettings();
        parent::__construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryHits);
        if ($this->requirementsMet) $this->requirementsMet = $this->evaluateText();
    }

    /**
     * @return bool
     * @throws \PHPythagoras\ApiException
     */
    private function evaluateText()
    {
        $adapter = new PHPythagorasAdapter();
        $response = $adapter->send(
            self::$storageService->textCopy,
            $this->phpythagorasSettings->getGroupSeparator() ?? null,
            $this->phpythagorasSettings->getDecimalpoint() ?? null,
            $this->phpythagorasSettings->getApiKey()
        );

        foreach (explode("\n", $response->getResultString()) as $entry) {
            $this->results[] = strpos($entry, "Division by") === false ? $entry : $this->phpythagorasSettings->getDivisionByZeroResponse();
        }
        return $response->getOk() && $response->getResultString();
    }

    public function generateResponse() : void
    {
        MessageService::$parseMode = ParseMode::HTML;
        foreach ($this->results as $result) {
            self::$storageService->response->text[] = '<code>' . $result . '</code>';
        }
    }
}