<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response as WebResponse;
use yii\filters\AccessControl;
use app\models\Task;
use app\models\Response;
use app\services\TaskService;

/**
 * Handles workers' responses to tasks.
 */
class ResponseController extends Controller
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
        'only' => ['create', 'accept', 'reject'],
        'rules' => [
          [
            'actions' => ['create'],
            'allow' => true,
            'roles' => ['worker'],
          ],
          [
            'actions' => ['accept', 'reject'],
            'allow' => true,
            'roles' => ['customer'],
          ],
        ],
      ],
    ];
  }
  /**
   * Action for creating a worker's response to the task
   * @param int $taskId 
   * @return WebResponse
   * @throws NotFoundHttpException
   * @throws ForbiddenHttpException if the task is not new
   */
  public function actionCreate(int $taskId): WebResponse
  {
    $task = Task::findOne($taskId);

    if (!$task || $task->status !== Task::STATUS_NEW) {
      throw new ForbiddenHttpException();
    }

    $response = new Response();

    if (Yii::$app->request->isPost && $response->load(Yii::$app->request->post())) {
      $service = new TaskService;
      $service->createResponse($task, $response, Yii::$app->user->id);
    }
    return $this->redirect(['tasks/view', 'id' => $taskId]);
  }

  /**
   * Action when the customer accepts the worker's response
   * @param int $id response id
   * @return WebResponse
   * @throws NotFoundHttpException if the response was not found
   * @throws ForbiddenHttpException if the user is not a customer
   */
  public function actionAccept(int $id): WebResponse
  {
    $response = Response::findOne($id);
    if (!$response) {
      throw new NotFoundHttpException();
    }

    if ((int)$response->task->employer_id !== (int)Yii::$app->user->id) {
      throw new ForbiddenHttpException();
    }

    $service = new TaskService();
    $service->acceptResponse($response);

    return $this->redirect(['tasks/view', 'id' => $response->task_id]);
  }

  /**
   * Action when the customer rejects the worker's response
   * @param int $id response id
   * @return WebResponse
   * @throws NotFoundHttpException if the response was not found
   * @throws ForbiddenHttpException if the user is not a customer
   */
  public function actionReject($id): WebResponse
  {
    $response = Response::findOne($id);
    if (!$response) {
      throw new NotFoundHttpException();
    }

    if ((int)$response->task->employer_id !== (int)Yii::$app->user->id) {
      throw new ForbiddenHttpException();
      }
      $service = new TaskService;
      $service->rejectResponse($response);

      return $this->redirect(['tasks/view', 'id' => $response->task_id]);
  }
}
