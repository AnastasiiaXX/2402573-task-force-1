<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Task;
use app\models\TaskFilter;
use app\models\Category;
use app\models\Response;
use app\models\Review;
use app\models\User;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;

class TasksController extends Controller
{ 
  public function behaviors()
  {
    return [
      'access' => [
        'class' => AccessControl::class,
        'only' => ['index', 'view', 'complete', 'decline', 'cancel'],
        'rules' => [
          [
            'actions' => ['index', 'view'],
            'allow' => true,
            'roles' => ['@'],
          ],
          [
            'actions' => ['complete', 'cancel'],
            'allow' => true,
            'roles' => ['customer'],
          ],
          [
            'actions' => ['decline'],
            'allow' => true,
            'roles' => ['worker'],
          ],
        ],
      ],
    ];
  }

  public function actionIndex()
  {

    if (Yii::$app->user->isGuest) {
      return $this->redirect(['/']);
    }

    $query = Task::find()
      ->where(['status' => Task::STATUS_NEW])
      ->orderBy(['date_add' => SORT_DESC]);

    $filters = new TaskFilter();

    if ($filters->load(Yii::$app->request->get(), '')) {

      if (!empty($filters->categories)) {
        $query->andWhere(['category_id' => $filters->categories]);
      }

      if ($filters->notTaken) {
        $query->andWhere(['worker_id' => null]);
      }

      if ($filters->timePeriod !== '') {
        $fromDate = date(
          'Y-m-d H:i:s',
          time() - $filters->timePeriod * 3600
        );

        $query->andWhere(['>=', 'date_add', $fromDate]);
      }
    }

    $countQuery = clone $query;
    $pages = new Pagination([
      'totalCount' => $countQuery->count(),
      'pageSize' => 5
    ]);
    $pages->params = Yii::$app->request->get();
    $tasks = $query->offset($pages->offset)
      ->limit($pages->limit)
      ->all();

    $categories = Category::find()
      ->innerJoinWith('tasks')
      ->where(['tasks.status' => Task::STATUS_NEW])
      ->groupBy(Category::tableName() . '.id')
      ->all();

    return $this->render('index', [
      'tasks' => $tasks,
      'categories' => $categories,
      'filters' => $filters,
      'pages' => $pages
    ]);
  }

  public function actionView($id)
  {
    $task = Task::findOne($id);

    if (!$task) {
      throw new NotFoundHttpException('Задача не найдена');
    }

    $userId = Yii::$app->user->id;
    $isGuest = Yii::$app->user->isGuest;
    $isCustomer = !$isGuest && Yii::$app->user->can('customer');
    $isWorker = !$isGuest && Yii::$app->user->can('worker');

    $alreadyResponded = !$isGuest && Response::find()
      ->where(['task_id' => $task->id, 'worker_id' => $userId])
      ->exists();

    $query = Response::find()->where(['task_id' => $task->id]);

    if ($isGuest) {
      $responses = [];
    } elseif ($isCustomer) {
      $responses = $query->all();
    } elseif ($isWorker) {
      $responses = $query
        ->andWhere(['worker_id' => $userId])
        ->all();
    } else {
      $responses = [];
    }

    $review = new Review();
    $responseForm = new Response();

    return $this->render('view', [
      'task' => $task,
      'responses' => $responses,
      'alreadyResponded' => $alreadyResponded,
      'isWorker' => $isWorker,
      'isCustomer' => $isCustomer,
      'isGuest' => $isGuest,
      'review' => $review,
      'response' => $responseForm
    ]);
  }

  public function actionComplete($id)
  {
    $task = Task::findOne($id);

    if (!$task) {
      throw new NotFoundHttpException('Задача не найдена');
    }

    if ((int)$task->employer_id !== (int)Yii::$app->user->id) {
      throw new ForbiddenHttpException();
    }

    if ($task->status !== Task::STATUS_IN_PROGRESS) {
      throw new ForbiddenHttpException();
    }

    $review = new Review();

    if ($review->load(Yii::$app->request->post()) && $review->validate()) {

      $review->task_id = $task->id;
      $review->worker_id = $task->worker_id;
      $review->employer_id = Yii::$app->user->id;

      $task->status = Task::STATUS_COMPLETED;

      $task->save(false);
      $review->save(false);

      return $this->redirect((['tasks/view', 'id' => $task->id]));
    }

    return $this->render('complete', [
      'task' => $task,
      'review' => $review
    ]);
  }

  public function actionDecline($id)
  {
    $task = Task::findOne($id);

    if (!$task) {
      throw new NotFoundHttpException('Задача не найдена');
    }

    $userId = Yii::$app->user->id;

    if ((int)$task->worker_id !== (int)$userId) {
      throw new ForbiddenHttpException();
    }

    if ($task->status !== Task::STATUS_IN_PROGRESS) {
      throw new ForbiddenHttpException();
    }

    $task->status = Task::STATUS_FAILED;
    $task->save(false);

    $user = User::findOne($userId);
    $user->failed_tasks += 1;
    $user->save(false);

    return $this->redirect((['tasks/view', 'id' => $task->id]));
  }

  // в верстке отсутствует кнопка "отменить"
  public function actionCancel($id)
  {
    $task = Task::findOne($id);

    if (!$task) {
      throw new NotFoundHttpException();
    }

    $userId = Yii::$app->user->id;

    if ((int)$task->employer_id !== (int)$userId) {
      throw new ForbiddenHttpException();
    }

    if ($task->status !== Task::STATUS_NEW) {
      throw new ForbiddenHttpException();
    }

    $task->status = Task::STATUS_CANCELED;
    $task->save(false);

    return $this->redirect((['tasks/view', 'id' => $task->id]));
  }
}
