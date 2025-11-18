<?php

require __DIR__ . '/vendor/autoload.php';

use TaskForce\Model\Task;
use TaskForce\Enum\Status;

$task = new Task(1, 10);

var_dump($task->getCurrentStatus());
