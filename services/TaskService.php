<?php

namespace app\services;

use app\models\Task;
use app\models\Review;
use app\models\Response;

/**
 * Service for handling task actions and worker responses.
 */
class TaskService
{
  /**
   * Handles an active task declining (failing) by a worker.
   * @param Task $task The task being failed
   * @return bool True if the task status and worker stats were updated successfully.
   */
  public function failTask(Task $task): bool
  {
    $task->status = Task::STATUS_FAILED;

    $worker = $task->worker;

    if ($worker) {
      $worker->failed_tasks = $worker->failed_tasks + 1;
      $worker->save();
    }

    return $task->save(false);
  }

  /**
   * Handles task cancellation by the customer before an assignment of a worker
   * @param Task $task The task to cancel
   * @return bool True if the task was new
   */
  public function cancel(Task $task): bool
  {
    if ($task->status === Task::STATUS_NEW) {
      $task->status = Task::STATUS_CANCELED;
      return $task->save(false);
    }
    return false;
  }

  /**
   * Handles task completion by the customer and addition of the worker's review.
   * @param Task $task
   * @param Review $reviewData
   * @return bool
   */
  public function complete(Task $task, Review $reviewData): bool
  {
    $task->status = Task::STATUS_COMPLETED;
    $task->save(false);

    $reviewData->task_id = $task->id;
    $reviewData->worker_id = $task->worker_id;
    $reviewData->employer_id = $task->employer_id;

    return $reviewData->save();
  }

  /**
   * Response creation by the worker
   * @param Task $task
   * @param Response $response
   * @param int $workerId
   * @return bool False if the task is not new or response already exists
   */
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

  /**
   * Accepts a worker's response, assigns the worker to the task and starts the work.
   * @param Response $response
   * @return bool true if the task is new
   */
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

  /**
   * Handles customer's rejection of a response to the task
   * @param Response $response
   * @return bool true if the task is new
   */
  public function rejectResponse(Response $response): bool
  {
    $response->status = Response::STATUS_REJECTED;
    return $response->save(false);
  }
}
