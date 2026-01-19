<?php

namespace app\controllers;

use app\models\Category;
use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\web\ForbiddenHttpException;
use yii\helpers\ArrayHelper;
use app\models\User;
use app\models\Specialty;
use app\models\ChangePasswordForm;
use yii\filters\AccessControl;

class MyProfileController extends Controller
{
  public function behaviors()
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

  public function actionIndex()
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

  public function actionSecurity()
{
    $passwordForm = new ChangePasswordForm();

    if ($passwordForm->load(Yii::$app->request->post()) && $passwordForm->changePassword()) {
        Yii::$app->session->setFlash('success', 'Пароль успешно изменён');
        return $this->redirect(['security']);
    }

    return $this->render('security', [
        'passwordForm' => $passwordForm,
    ]);
}
}
