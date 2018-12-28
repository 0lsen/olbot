<?php

namespace OLBot\Category;


use OpenAPI\Client\Api\CurrentWeatherDataApi;
use OpenAPI\Client\OpenWeather\Model200;

class Weather extends AbstractCategory
{
    private $openWeatherSettings;
    private $subjectPlace;

    public function __construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryhits)
    {
        $this->openWeatherSettings = $settings['openWeatherSettings'];
        $this->subjectPlace = self::$storageService->subjectCandidates[$subjectCandidateIndex] ?? null;
        parent::__construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryhits);
    }

    public function generateResponse()
    {
        $answer = $this->getAnswer();
        $api = new CurrentWeatherDataApi();
        $api->getConfig()->setApiKey('appid', $this->openWeatherSettings['apiKey']);
        $data = $api->currentWeatherData(
            $this->subjectPlace ?
                preg_replace('#[?!\.]+$#', '', $this->subjectPlace->text) :
                $this->openWeatherSettings['fallbackPlace'],
            null, null, null, null,
            $this->openWeatherSettings['units'] ?? 'metric',
            $this->openWeatherSettings['lang'] ?? 'en'
        );
        $text = $this->mapData($answer->text, $data);
        self::$storageService->response->text[] = $text;
    }

    private function mapData($string, Model200 $data)
    {
        $string = preg_replace_callback(
            '/#(\w+)#/',
            function($match) use($data) {
                switch ($match[1]) {
                    case 'place':
                        return $data->getName() . ', ' . $data->getSys()->getCountry();
                    case 'temp':
                        return $data->getMain()->getTemp();
                    case 'wind':
                        return $data->getWind()->getSpeed();
                    case 'situation':
                        return $data->getWeather()[0]->getDescription();
                    default:
                        return $match[1];
                }
            },
            $string
        );
        return $string;
    }
}