<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\User;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use app\services\GeoService;

/**
 * Gets the location coordinates
 */
class GeocoderController extends Controller
{
    /**
     * Searches a location by a query
     * @param string $query
     * @return array {label: string, city: string, latitude: string, longitude: string}[]
     * @throws BadRequestHttpException if the query is empty
     * @throws ForbiddenHttpException if user is not a client
     */
    public function actionSearch(string $query): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->user->can('customer')) {
            throw new ForbiddenHttpException();
        }

        $query = trim($query);
        if ($query === '') {
            throw new BadRequestHttpException('Пустой запрос');
        }

        $userId = Yii::$app->user->id;
        $user = User::findOne($userId);
        $cityName = $user->location->name;

        if (!$cityName) {
            return [];
        }

        $geoService = new GeoService();

        return $geoService->getCoordinates($cityName, $query);
    }
}
