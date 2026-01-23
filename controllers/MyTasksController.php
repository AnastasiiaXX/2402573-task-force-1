<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;
use app\models\Task;

/**
 * Controller for managing user's own tasks
 */
class MyTasksController extends Controller
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
            'roles' => ['customer', 'worker'],
          ],
        ],
        ],
        ];
    }
    /**
     * Displays the list of tasks depending on their status and user role
     * @param string|null @status status filter
     * @throws ForbiddenHttpException if user is not authorized
     * @return string
     */
    public function actionIndex($status = null): string
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
