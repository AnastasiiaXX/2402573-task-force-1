<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\web\Response;
use yii\web\ForbiddenHttpException;
use yii\helpers\ArrayHelper;
use app\models\User;
use app\models\Category;
use app\models\ChangePasswordForm;
use yii\filters\AccessControl;

class MyProfileController extends Controller
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
            'roles' => ['worker'],
          ],
        ],
        ],
        ];
    }

    /**
     * Displays the form for editing a worker's profile
     * @return string|Response
     * @throws ForbiddenHttpException if user is not a worker
     */
    public function actionIndex(): string|Response
    {
      /** @var User $model */
        $model = Yii::$app->user->identity;

        if (!Yii::$app->user->can('worker')) {
            throw new ForbiddenHttpException('Профиль недоступен');
        }

        $categories = Category::find()->all();

        $model->category_ids = ArrayHelper::getColumn($model->categories, 'id');

        if ($model->load(Yii::$app->request->post())) {
            $oldAvatar = $model->avatar;
            $model->avatarFile = UploadedFile::getInstance($model, 'avatarFile');

            if ($model->avatarFile) {
                $path = 'img/avatars/' . $model->id . '_n.' . $model->avatarFile->extension;
                $model->avatarFile->saveAs($path);
                $model->avatar = $path;
            } else {
                $model->avatar = $oldAvatar;
            }

            if ($model->save(false)) {
                $model->unlinkAll('categories', true);
                if (!empty($model->category_ids)) {
                    foreach ($model->category_ids as $categoryId) {
                        $category = Category::findOne($categoryId);
                        if ($category) {
                            $model->link('categories', $category);
                        }
                    }
                }

                return $this->refresh();
            }
        }

        $age = null;
        if ($model->birthday) {
            $birth = new \DateTime($model->birthday);
            $today = new \DateTime();
            $age = $today->diff($birth)->y;
        }
        return $this->render('index', [
        'model' => $model,
        'categories' => $categories,
        'age' => $age,
        ]);
    }

    /**
     * Displays the form for changing worker's password
     * @return string|Response
     */
    public function actionSecurity(): string|Response
    {
        $passwordForm = new ChangePasswordForm();

        if ($passwordForm->load(Yii::$app->request->post()) && $passwordForm->changePassword()) {
            Yii::$app->session->setFlash('success', 'Пароль успешно изменён');
            return $this->redirect(['my-profile/index']);
        }

        return $this->render('security', [
        'passwordForm' => $passwordForm,
        ]);
    }
}
