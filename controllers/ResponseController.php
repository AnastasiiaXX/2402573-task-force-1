<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use app\models\Task;
use app\models\Response;
use TaskForce\Action\Respond;

class ResponseController extends Controller
{
  function actionReject($id)
  {
    $response = Response::findOne($id);

    if (!$response) {
      throw new NotFoundHttpException();
    }

    $task = $response->task;
    $currentUserId = Yii::$app->user->getId();

    if ((int)$task->employer_id !== (int)$currentUserId) {
      throw new ForbiddenHttpException();
    }

    $response->status = Response::STATUS_REJECTED;
    $response->save(false);

    return $this->redirect(['tasks/view', 'id' => $task->id]);
  }

  function actionAccept($id)
  {
    $response = Response::findOne($id);

    if (!$response) {
      throw new NotFoundHttpException();
    }

    $task = $response->task;
    $currentUserId = Yii::$app->user->getId();

    if ((int)$task->employer_id !== (int)$currentUserId) {
      throw new ForbiddenHttpException();
    }

    if ($task->status !== Task::STATUS_NEW) {
    throw new ForbiddenHttpException();
    }

    $response->status = Response::STATUS_ACCEPTED;
    $task->worker_id = $response->worker_id;
    $task->status = Task::STATUS_IN_PROGRESS;

    $response->save(false);
    $task->save(false);

    return $this->redirect(['tasks/view', 'id' => $task->id]);
  }

  public function actionCreate($taskId)
  {
    if (Yii::$app->user->isGuest || !Yii::$app->user->can('worker')) {
      throw new ForbiddenHttpException();
    }

    $task = Task::findOne($taskId);

    if (!$task || $task->status !== Task::STATUS_NEW) {
      throw new ForbiddenHttpException();
    }

    $workerId = Yii::$app->user->id;

    $alreadyExists = Response::find()
        ->where([
          'task_id' => $taskId,
          'worker_id' => $workerId
        ])
        ->exists();
    
    if ($alreadyExists) {
      return $this->redirect(['tasks/view', 'id' => $taskId]);
    }

    $response = new Response();

    if ($response->load(Yii::$app->request->post())) {
      $response->task_id = $taskId;
      $response->worker_id = $workerId;
      $response->status = Response::STATUS_NEW;
      $response->date_add = date('Y-m-d H:i:s');

      if ($response->cost !== null && $response->cost <= 0) {
      return $this->redirect(['tasks/view', 'id' => $taskId]);
      }

      $response->save(false);
    }

    return $this->redirect(['tasks/view', 'id' => $taskId]);    
  }
}