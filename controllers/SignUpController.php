<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use app\models\User;
use app\models\Location;
use app\models\SignUpForm;

/**
 * Controller for user registration
 */
class SignUpController extends Controller
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
     * Handles user registration
     * @return string|Response
     */
    public function actionIndex(): string|Response
    {

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

            $user->password = Yii::$app->security->generatePasswordHash($form->password);
            $user->auth_key = Yii::$app->security->generateRandomString();

            if ($user->save(false)) {
                $auth = Yii::$app->authManager;
                $roleName = $form->willRespond ? 'worker' : 'customer';
                $role = $auth->getRole($roleName);
                if ($role) {
                    $auth->assign($role, $user->id);
                }

                Yii::$app->user->login($user);

                return $this->redirect(['tasks/index']);
            }
        }

        return $this->render('index', [
        'model' => $form,
        'citiesList' => $citiesList
        ]);
    }
}
