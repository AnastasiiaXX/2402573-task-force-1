<?php

namespace app\controllers;

use yii;
use yii\web\Controller;
use app\models\User;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

class UsersController extends Controller
{
    public function behaviors()
    {
        return [
        'access' => [
        'class' => AccessControl::class,
        'only' => ['view', 'logout'],
        'rules' => [
          [
            'actions' => ['view'],
            'allow' => true,
            'roles' => ['@'],
          ],
          [
            'actions' => ['logout'],
            'allow' => true,
            'roles' => ['@'],
          ],
        ],
        ],
        ];
    }

    public function actionView($id)
    {
        $user = User::findOne($id);
        if (!$user) {
            throw new NotFoundHttpException('Пользователь не найден');
        }

        $isWorker = Yii::$app->authManager->getAssignment('worker', $user->id);

        if (!$isWorker) {
            throw new NotFoundHttpException('Профиль заказчика не существует');
        }

        return $this->render('view', ['user' => $user]);
    }

    public function actionLogout()
    {
        \Yii::$app->user->logout();

        return $this->redirect(['/']);
    }
}
