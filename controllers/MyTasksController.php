<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;
use app\models\Task;

class MyTasksController extends Controller
{
  public function behaviors()
  {
    return [
      'access' => [
        'class' => AccessControl::class,
        'rules' => [
          [
            'allow' => true,
            'roles' => ['customer', 'worker'],
          ],
        ],
      ],
    ];
  }
  
  public function actionIndex($status = null)
  {
    if (Yii::$app->user->isGuest) {
      throw new ForbiddenHttpException('Доступ запрещён');
    }

    $userId = Yii::$app->user->id;
    $query = Task::find();

    if (Yii::$app->user->can('customer')) {
      $query->where(['employer_id' => $userId]);

      if ($status) {
        switch ($status) {
          case 'new':
            $query->andWhere(['status' => Task::STATUS_NEW]);
            break;
          case 'in_progress':
            $query->andWhere(['status' => Task::STATUS_IN_PROGRESS]);
            break;
          case 'closed':
            $query->andWhere(['status' => [Task::STATUS_COMPLETED, Task::STATUS_CANCELED, Task::STATUS_FAILED]]);
            break;
        }
      }
    } elseif (Yii::$app->user->can('worker')) {
      $query->where(['worker_id' => $userId]);

      if ($status) {
        switch ($status) {
          case 'in_progress':
            $query->andWhere(['status' => Task::STATUS_IN_PROGRESS])
              ->andWhere(['>', 'date_end', date('Y-m-d')]);
            break;
          case 'overdue':
            $query->andWhere(['status' => Task::STATUS_IN_PROGRESS])
              ->andWhere(['<', 'date_end', date('Y-m-d')]);
            break;
          case 'closed':
            $query->andWhere(['status' => [Task::STATUS_COMPLETED, Task::STATUS_FAILED]]);
            break;
        }
      }
    } else {
      throw new ForbiddenHttpException('Доступ запрещён');
    }

    $tasks = $query->orderBy(['date_end' => SORT_DESC])->all();

    return $this->render('index', [
      'tasks' => $tasks,
      'role' => Yii::$app->user->can('customer') ? 'customer' : 'worker',
      'status' => $status
    ]);
  }
}
