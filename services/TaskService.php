<?php

namespace app\services;

use app\models\Task;
use app\models\User;
use app\models\Review;
use app\models\Response;

class TaskService
{
  /**
   * Логика отказа от задания
   */
    public function decline(Task $task)
    {
        $task->status = Task::STATUS_FAILED;
        $task->save();

        $worker = User::findOne($task->worker_id);
        if ($worker) {
            $worker->failed_tasks = $worker->failed_tasks + 1;
            $worker->save();
        }
    }

  /**
   * Отмена задания
   */
    public function cancel(Task $task)
    {

        if ($task->status === Task::STATUS_NEW) {
            $task->status = Task::STATUS_CANCELED;
            $task->save();
            return true;
        }
        return false;
    }

  /**
 * Завершение задания
 */
    public function complete(Task $task, Review $reviewData)
    {

        $task->status = Task::STATUS_COMPLETED;
        $task->save(false);

        $reviewData->task_id = $task->id;
        $reviewData->worker_id = $task->worker_id;
        $reviewData->employer_id = $task->employer_id;

        return $reviewData->save();
    }

    public function createResponse(Task $task, Response $response, int $workerId): bool
    {
        if ($task->status !== Task::STATUS_NEW) {
            return false;
        }

        $exists = Response::find()->where([
        'task_id' => $task->id,
        'worker_id' => $workerId
        ])->exists();

        if ($exists) {
            return false;
        }

        $response->task_id = $task->id;
        $response->worker_id = $workerId;
        $response->status = Response::STATUS_NEW;
        $response->date_add = date('Y-m-d H:i:s');

        return $response->save();
    }

    public function acceptResponse(Response $response): bool
    {
        $task = $response->task;

        if ($task->status !== Task::STATUS_NEW) {
            return false;
        }

        $response->status = Response::STATUS_ACCEPTED;
        $task->worker_id = $response->worker_id;
        $task->status = Task::STATUS_IN_PROGRESS;

        return $response->save(false) && $task->save(false);
    }

    public function declineTask(Task $task): bool
    {
        $task->status = Task::STATUS_FAILED;

        $worker = $task->worker;
        if ($worker) {
            $worker->failed_tasks = (int)$worker->failed_tasks + 1;
            $worker->save(false);
        }

        return $task->save(false);
    }
}
