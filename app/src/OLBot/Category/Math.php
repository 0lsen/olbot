<?php

namespace OLBot\Category;


use OLBot\Service\MessageService;
use PHPythagoras\Api\DefaultApi;
use PHPythagoras\Model\FormulaRequestBody;
use Telegram\Model\ParseMode;

class Math extends AbstractCategory
{
    private $phpythagorasSettings;
    private $results;

    public function __construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryHits)
    {
        $this->phpythagorasSettings = $settings['phpythagorasSettings'];
        parent::__construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryHits);
        if ($this->requirementsMet) $this->requirementsMet = $this->evaluateText();
    }

    private function evaluateText()
    {
        $api = new DefaultApi();
        $api->getConfig()
            ->setApiKey('Authorization', $this->phpythagorasSettings['apiKey'])
            ->setApiKeyPrefix('Authorization', 'Bearer');
        $request = new FormulaRequestBody();
        $request->setFormula(self::$storageService->textCopy);
        $request->setGroupSeparator($this->phpythagorasSettings['groupSeparator'] ?? null);
        $request->setDecimalPoint($this->phpythagorasSettings['decimalPoint'] ?? null);
        $response = $api->formulaEvaluateFulltextPost($request);

        foreach (explode("\n", $response->getResultString()) as $entry) {
            $this->results[] = strpos($entry, "Division by") === false ? $entry : $this->phpythagorasSettings['divisionByZeroResponse'];
        }
        return $response->getOk() && $response->getResultString();
    }

    public function generateResponse()
    {
        MessageService::$parseMode = ParseMode::HTML;
        foreach ($this->results as $result) {
            self::$storageService->response->text[] = '<code>' . $result . '</code>';
        }
    }
}