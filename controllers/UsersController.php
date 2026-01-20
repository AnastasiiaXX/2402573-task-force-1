<?php

namespace app\controllers;

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
            'roles' => ['worker'],
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

    if (!$user->is_worker) {
        throw new NotFoundHttpException();
    }

    return $this->render('view', ['user' => $user]);
  }

  public function actionLogout()
  {
    \Yii::$app->user->logout();

    return $this->redirect(['/']);
  }
}
