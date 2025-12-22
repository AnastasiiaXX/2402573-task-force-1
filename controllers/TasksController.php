<?php

namespace app\controllers;

use app\models\Task;
use yii\web\Controller;

class TasksController extends Controller
{
  public function actionIndex()
  {
    $tasks = Task::find()
    ->where(['status' => Task::STATUS_NEW])
    ->orderBy(['date_add' => SORT_DESC])
    ->all();

    return $this->render('index', [
      'tasks' => $tasks,
    ]);
  }
}
