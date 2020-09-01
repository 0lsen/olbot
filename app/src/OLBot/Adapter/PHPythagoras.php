<?php


namespace OLBot\Adapter;


use PHPythagoras\Api\DefaultApi;
use PHPythagoras\Model\FormulaRequestBody;

class PHPythagoras
{
    /**
     * @param string $formula
     * @param string $groupSeparator
     * @param string $decimalPoint
     * @param string $apiKey
     * @return \PHPythagoras\Model\FormulaResponseBody
     * @throws \PHPythagoras\ApiException
     */
    public function send(string $formula, ?string $groupSeparator, ?string $decimalPoint, string $apiKey) {
        $api = new DefaultApi();
        $api->getConfig()
            ->setApiKey('Authorization', $apiKey)
            ->setApiKeyPrefix('Authorization', 'Bearer');
        $request = new FormulaRequestBody();
        $request->setFormula($formula);
        $request->setGroupSeparator($groupSeparator);
        $request->setDecimalPoint($decimalPoint);
        return $api->formulaEvaluateFulltextPost($request);
    }
}