<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\User;
use app\models\Location;
use app\models\SignUpForm;

class SignUpController extends Controller
{
  public function actionIndex() {

     if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/']);
    }

    $form = new SignUpForm();
    $citiesList = Location::find()
                  ->select(['name', 'id'])
                  ->indexBy('id')
                  ->column();

    if ($form->load(Yii::$app->request->post()) && $form->validate()) {
      $user = new User();
      $user->name = $form->name;
      $user->email = $form->email;
      $user->location_id = $form->location_id;
      $user->role = $form->willRespond ? 'worker' : 'employer';

      $user->password = Yii::$app->security->generatePasswordHash($form->password);

      if ($user->save(false)) {
        return $this->redirect(['tasks/index']);
      }
    }

    return $this->render('index', [
      'model'=> $form,
      'citiesList' => $citiesList
    ]);
  }
}


