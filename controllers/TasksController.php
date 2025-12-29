<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Task;
use app\models\TaskFilter;
use app\models\Category;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;

class TasksController extends Controller
{
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
      $pages = new Pagination(['totalCount' => $countQuery->count(),
                                'pageSize' => 5]);
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

    return $this->render('view', ['task' => $task]);
  }
}
