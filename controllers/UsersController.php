<?php

namespace app\controllers;

use yii\web\Controller;
use app\models\User;

use yii\web\NotFoundHttpException;

class UsersController extends Controller
{

  public function actionView($id)
  {
    $user = User::findOne($id);
    if (!$user) {
      throw new NotFoundHttpException('Пользователь не найден');
    }

    return $this->render('view', ['user' => $user]);
  }

  public function actionLogout() {
        \Yii::$app->user->logout();

        return $this->redirect(['/']);
  }
}