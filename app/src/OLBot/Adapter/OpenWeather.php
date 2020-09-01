<?php


namespace OLBot\Adapter;


use OpenWeather\Api\CurrentWeatherDataApi;

class OpenWeather
{
    /**
     * @param string $place
     * @param string $units
     * @param string $language
     * @param string $apiKey
     * @return \OpenWeather\Model\Model200|string
     * @throws \OpenWeather\ApiException
     */
    public function send(string $place, string $units, string $language, string $apiKey) {
        $api = new CurrentWeatherDataApi();
        $api->getConfig()->setApiKey('appid', $apiKey);
        return $api->currentWeatherData(
            $place,
            null, null, null, null,
            $units,
            $language
        );
    }
}