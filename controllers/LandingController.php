<?php

namespace app\controllers;

use Yii;
use app\models\LoginForm;
use app\models\User;
use yii\web\Controller;


class LandingController extends Controller
{
  public $layout = 'landing';
  
 public function actionIndex()
  {
    $loginForm = new LoginForm();


    if (!Yii::$app->user->isGuest) {
      return $this->redirect(['/tasks']);
    }

    $showModal = false;
    
    if ($loginForm->load(Yii::$app->request->post())) {
        if ($loginForm->validate()) {
            Yii::$app->user->login($loginForm->getUser());
            return $this->redirect(['/tasks']);
        } else {
            $showModal = true;
        }
    }

    return $this->render('index', [
        'model' => $loginForm,
        'showModal' => $showModal
    ]);
  }

      /**
   * {@inheritdoc}
   */
  public function actions()
  {
    
    return [
      'auth' => [
        'class' => 'yii\authclient\AuthAction',
        'successCallback' => [$this, 'onAuthSuccess'],
      ],
    ];
  }

  public function onAuthSuccess($client)
  {
    $attributes = $client->getUserAttributes();

    $githubId = $attributes['id'];
    $email = $attributes['email'] ?? null;
    $username = $attributes['login'];

    $user = $email ? User::find()->where(['email' => $email])->one() : null;

    if (!$user) {
        $user = User::find()->where(['github_id' => $githubId])->one();
    }

    if (!$user) {
        $user = new User();
        $user->github_id = $githubId;
        $user->name = $username;
        $user->email = $email;
        $user->auth_key = Yii::$app->security->generateRandomString();
        $user->save(false);
    } else {
        if (!$user->github_id) {
            $user->github_id = $githubId;
            $user->save(false);
        }
    }
  /** @var \app\models\User $user */
    Yii::$app->user->login($user);

    return $this->redirect(['/tasks']);
  }
}
