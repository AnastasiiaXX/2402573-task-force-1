<?php

namespace app\services;

use yii\httpclient\Client;
use Yii;

/**
 * Service for working with external geo data.
 */
class GeoService
{
    /**
     * Gets a list of coordinates and adresses through search query.
     * @param string $cityName User's city name
     * @param string $query search query
     * @return array
     */
    public function getCoordinates(string $cityName, string $query): array
    {
        $apiKey = Yii::$app->params['yandexGeocoderApiKey'];
        $client = new Client();

        $response = $client->get('https://geocode-maps.yandex.ru/1.x/', [
            'apikey' => $apiKey,
            'geocode' => $cityName . ', ' . $query,
            'format' => 'json',
            'results' => 5,
            'lang' => 'ru_RU'
        ])->addHeaders(['User-Agent' => 'Mozilla/5.0'])->send();

        if (!$response->isOk) {
            return [];
        }

        $jsonRes = $response->data['response']['GeoObjectCollection']['featureMember'] ?? [];
        $result = [];

        foreach ($jsonRes as $field) {
            $geoObject = $field['GeoObject'];
            $positions = $geoObject['Point']['pos'] ?? null;
            $text = $geoObject['metaDataProperty']['GeocoderMetaData']['text'] ?? null;

            if (!$positions || !$text) {
                continue;
            }

            [$lng, $lat] = explode(' ', $positions);
            
            $city = $cityName;
            $addressComponents = $geoObject['metaDataProperty']['GeocoderMetaData']['Address']['Components'] ?? [];
            foreach ($addressComponents as $component) {
                if ($component['kind'] === 'locality') {
                    $city = $component['name'];
                    break;
                }
            }

            $result[] = [
                'label' => $text,
                'city' => $city,
                'latitude' => $lat,
                'longitude' => $lng,
            ];
        }

        return $result;
    }
}