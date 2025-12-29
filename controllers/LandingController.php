<?php

namespace app\controllers;

use Yii;
use app\models\LoginForm;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;

class LandingController extends Controller
{
  public $layout = 'landing';
  
 public function actionIndex()
  {
    $loginForm = new LoginForm();


    if (!Yii::$app->user->isGuest) {
      return $this->redirect(['/tasks']);
    }

    if (Yii::$app->request->isAjax && $loginForm->load(Yii::$app->request->post())) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return ActiveForm::validate($loginForm);
    }

    if ($loginForm->load(Yii::$app->request->post()) && $loginForm->validate()) {
      Yii::$app->user->login($loginForm->getUser());
      return $this->redirect(['/task']);
    }

    return $this->render('index', ['model' => $loginForm]);
  }
}
