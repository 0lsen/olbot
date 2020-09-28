<?php

namespace OLBot\Category;


use OLBot\Adapter\OpenWeather as OpenWeatherAdapter;
use OLBotSettings\Model\Weather as WeatherSettings;
use OpenWeather\Model\Model200;

class Weather extends AbstractCategory
{
    private $openWeatherSettings;
    private $subjectPlace;

    public function __construct(int $categoryNumber, ?int $subjectCandidateIndex, WeatherSettings $settings, $categoryhits = [])
    {
        $this->openWeatherSettings = $settings->getOpenWeatherSettings();
        $this->subjectPlace = self::$storageService->subjectCandidates[$subjectCandidateIndex] ?? null;
        parent::__construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryhits);
    }

    /**
     * @throws \Exception
     * @throws \OpenWeather\ApiException
     */
    public function generateResponse() : void
    {
        $answerTemplate = $this->getAnswer();
        $adapter = new OpenWeatherAdapter();

        $data = $adapter->send(
            $this->subjectPlace ?
                preg_replace('#[?!.]+$#', '', $this->subjectPlace->text) :
                $this->openWeatherSettings->getFallbackPlace(),
            $this->openWeatherSettings->getUnits() ?? 'metric',
            $this->openWeatherSettings->getLanguage() ?? 'en',
            $this->openWeatherSettings->getApiKey()
        );

        $text = $this->mapData($answerTemplate->text, $data);
        self::$storageService->response->text[] = $text;
    }

    private function mapData($string, Model200 $data) : string
    {
        $string = preg_replace_callback(
            '/#(\w+)#/',
            function($match) use($data) {
                switch ($match[1]) {
                    case 'place':
                        return $data->getName() . ' (' . $data->getSys()->getCountry() . ')';
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