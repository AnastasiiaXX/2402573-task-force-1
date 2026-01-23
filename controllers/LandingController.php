<?php

namespace app\controllers;

use Yii;
use app\models\LoginForm;
use app\models\User;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\authclient\ClientInterface;

/**
 * Controller for handling authorization either regular or via the third party
 */
class LandingController extends Controller
{
    public $layout = 'landing';

    /**
     * {@inheritdoc}
     * @return array
     */
    public function behaviors(): array
    {
        return [
        'access' => [
        'class' => AccessControl::class,
        'rules' => [
          [
            'allow' => true,
            'roles' => ['?'],
          ],
        ],
        ],
        ];
    }
    /**
     * Displays the login form
     * @return string|Response
     */
    public function actionIndex(): string|Response
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
    public function actions(): array
    {

        return [
        'auth' => [
        'class' => 'yii\authclient\AuthAction',
        'successCallback' => [$this, 'onAuthSuccess'],
        ],
        ];
    }
    /**
     * Handles successful authorization via third-party services.
     * @param ClientInterface $client
     * @return array
     */
    public function onAuthSuccess(ClientInterface $client): Response
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
