<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\web\ForbiddenHttpException;
use app\models\AddTaskForm;
use app\models\Task;
use app\models\File;
use app\models\Category;

class AddTaskController extends Controller
{
  public function actionIndex()
  {
    if (Yii::$app->user->isGuest || Yii::$app->user->identity->role !== 'customer') {
      throw new ForbiddenHttpException();
    }

    $form = new AddTaskForm();

    if ($form->load(Yii::$app->request->post())) {
      $form->files = UploadedFile::getInstances($form, 'files');

      if ($form->validate()) {
        $task = new Task();
        $task->title = $form->title;
        $task->description = $form->description;
        $task->category_id = $form->category_id;
        $task->cost = $form->cost;
        $task->date_end = $form->date_end;
        $task->status = Task::STATUS_NEW;
        $task->employer_id = Yii::$app->user->getId();

        $task->save(false);

        if (!empty($form->files)) {
          foreach ($form->files as $file) {
            $fileName = uniqid() . '.' . $file->extension;
            $file->saveAs(Yii::getAlias('@webroot/uploads/') . $fileName);

            $taskFile = new File();
            $taskFile->task_id = $task->id;
            $taskFile->path = $fileName;
            $taskFile->save(false);
          }
        }

        return $this->redirect(['task/view', 'id' => $task->id]);
      }
    }

    $categories = Category::find()->all();

    return $this->render('index', [
      'model' => $form,
      'categories' => $categories
    ]);
  }
}
