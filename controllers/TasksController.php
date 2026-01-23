<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\db\Expression;
use app\models\Task;
use app\models\TaskFilter;
use app\models\Category;
use app\models\Response;
use app\models\Review;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response as WebResponse;
use yii\filters\AccessControl;
use app\services\TaskService;

/**
 * Controller for handling actions with tasks
 */
class TasksController extends Controller
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
        'only' => ['complete', 'decline', 'cancel'],
        'rules' => [
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

  /**
   * Displays all new tasks and the filter form
   * @return string|WebResponse
   */
  public function actionIndex(): string|WebResponse
  {

    if (Yii::$app->user->isGuest) {
      return $this->redirect(['/']);
    }

    $query = Task::find()
      ->where(['status' => Task::STATUS_NEW])
      ->andWhere([
        'or',
        ['location_id' => null],
        ['location_id' => Yii::$app->user->identity->location_id],
      ])
      ->orderBy(['date_add' => SORT_DESC]);

    $filters = new TaskFilter();

    if ($filters->load(Yii::$app->request->get())) {
      if (!empty($filters->categories)) {
        $query->andWhere(['category_id' => $filters->categories]);
      }

      if ($filters->notTaken) {
        $query->andWhere(['worker_id' => null]);
      }

      if ($filters->timePeriod) {
        $query->andWhere([
          '>=',
          'date_add',
          new Expression(
            'DATE_SUB(NOW(), INTERVAL :hours HOUR)',
            [':hours' => (int)$filters->timePeriod]
          )
        ]);
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
  /**
   * Displays the chosen task card
   * @param int $id
   *  @return string|WebResponse
   * @throws NotFoundHttpException if the task is not found
   */
  public function actionView(int $id): string|WebResponse
  {
    $task = Task::findOne($id);

    if (!$task) {
      throw new NotFoundHttpException('Задача не найдена');
    }

    $userId = Yii::$app->user->id;
    $isGuest = Yii::$app->user->isGuest;

    $isCustomer = !$isGuest && Yii::$app->user->can('customer');
    $isWorker = !$isGuest && Yii::$app->user->can('worker');

    if ($isGuest) {
      $responses = [];
    } elseif ($task->employer_id === $userId) {
      $responses = $task->getResponses()
        ->where(['status' => Response::STATUS_NEW])
        ->orWhere(['status' => Response::STATUS_ACCEPTED])
        ->all();
    } else {
      $responses = $task->getResponses()->where(['worker_id' => $userId])->all();
    }

    $alreadyResponded = !$isGuest && Response::find()
      ->where(['task_id' => $task->id, 'worker_id' => $userId])
      ->exists();

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
  /**
   * Action when the worker declines (fails) the task in progress
   * @param int $id task id
   * @throws ForbiddenHttpException if the user is not the assigned worker
   * @return WebResponse
   */
  public function actionDecline(int $id): WebResponse
  {
    $task = Task::findOne($id);

    if (!$task || (int)$task->worker_id !== (int)Yii::$app->user->id) {
      throw new ForbiddenHttpException();
    }

    (new TaskService())->failTask($task);

    return $this->redirect(['tasks/view', 'id' => $id]);
  }

  /**
   * Action when the customer cancels their new task
   * @param int $id
   * @throws NotFoundHttpException if the task is not found
   * @throws ForbiddenHttpException if the user is not a task author
   * @return WebResponse
   */
  public function actionCancel(int $id): WebResponse
  {
    $task = Task::findOne($id);
    if (!$task) {
      throw new NotFoundHttpException();
    }

    if ((int)$task->employer_id !== (int)Yii::$app->user->id) {
      throw new ForbiddenHttpException('Вы не можете отменить чужое задание');
    }

    $service = new TaskService();
    $service->cancel($task);

    return $this->redirect(['tasks/view', 'id' => $task->id]);
  }

  /**
   * Action when the customer marks the task completed and leaves a review
   * @param int $id
   * @return WebResponse
   * @throws ForbiddenHttpException if the user is not a task author or the task is not found
   */
  public function actionComplete(int $id): WebResponse
  {
    $task = Task::findOne($id);
    if (!$task || (int)$task->employer_id !== (int)Yii::$app->user->id) {
      throw new ForbiddenHttpException();
    }

    $review = new Review();

    if (Yii::$app->request->isPost) {
      if ($review->load(Yii::$app->request->post()) && $review->validate()) {
        $service = new TaskService();
        $service->complete($task, $review);
      }
    }

    return $this->redirect(['tasks/view', 'id' => $task->id]);
  }
}
