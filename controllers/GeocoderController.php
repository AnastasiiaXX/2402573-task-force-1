<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\User;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

class GeocoderController extends Controller
{
    public function actionSearch($query)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->user->can('customer')) {
            throw new ForbiddenHttpException();
        }

        $query = trim($query);
        if ($query === '') {
            throw new BadRequestHttpException('empty query');
        }

        $userId = Yii::$app->user->id;
        $user = User::findOne($userId);

        $cityName = $user->location->name;

        if (!$cityName) {
            return [];
        }

        $apiKey = Yii::$app->params['yandexGeocoderApiKey'];

        $params = [
        'apikey' => $apiKey,
        'geocode' => $cityName . ', ' . $query,
        'format' => 'json',
        'results' => 5,
        'lang' => 'ru_RU'
        ];

        $client = new \yii\httpclient\Client();

        $response = $client->get('https://geocode-maps.yandex.ru/1.x/', $params)
        ->addHeaders([
        'User-Agent' => 'Mozilla/5.0',
        ])->send();
        ;

        if (!$response->isOk) {
            Yii::error([
            'status' => $response->statusCode,
            'body' => $response->content,
            ], 'geocoder');

            throw new BadRequestHttpException(
                'Geocoder API error: ' . $response->content
            );
        }

        $data = $response->data;

        $jsonRes = $data['response']['GeoObjectCollection']['featureMember'] ?? [];
        $result = [];

        foreach ($jsonRes as $field) {
            $geoObject = $field['GeoObject'];

            $text = $geoObject['metaDataProperty']['GeocoderMetaData']['text'] ?? null;
            if (!$text) {
                continue;
            }

            $positions = $geoObject['Point']['pos'] ?? null;
            if (!$positions) {
                continue;
            }

            [$lng, $lat] = explode(' ', $positions);

            $addressComponents = $geoObject['metaDataProperty']['GeocoderMetaData']['Address']['Components'] ?? [];
            $city = null;

            foreach ($addressComponents as $component) {
                if ($component['kind'] === 'locality') {
                    $city = $component['name'];
                    break;
                }
            }

            if (!$city) {
                $city = $cityName;
            }

            $result[] = [
            'label' => $text,
            'city' => $city,
            'latitude' => (float)$lat,
            'longitude' => (float)$lng,
            ];
        }

        return $result;
    }
}
