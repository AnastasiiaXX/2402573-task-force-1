<?php

namespace app\controllers;

use yii;
use app\models\User;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
/**
 * Controller for displaying user profiles.
 */
class UsersController extends Controller
{   
  /**
     * {@inheritdoc}
     * @return array
     */
    public function behaviors(): array
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
    /**
     * Displays a worker's profile
     * @param int $id the user id
     * @return string
     * 
     * @throws NotFoundHttpException if the user does not exist or not a worker
     */
    public function actionView(int $id): string
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
    /**
     * Logs out of the profile
     * * @return Response
     */
    public function actionLogout(): Response
    {
        \Yii::$app->user->logout();

        return $this->redirect(['/']);
    }
}
