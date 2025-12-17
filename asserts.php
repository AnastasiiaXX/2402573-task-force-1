<?php

require __DIR__ . '/vendor/autoload.php';

use TaskForce\Model\Task;
use TaskForce\Enum\Status;

$task = new Task(1, 10);

$tests = [
    [Status::NEW, 10, ['Cancel']],
    [Status::NEW, 1, ['Respond']],
    [Status::INPROGRESS, 1, ['Decline']],
    [Status::INPROGRESS, 10, ['Complete']],
    [Status::INPROGRESS, 2, []],
];

foreach ($tests as [$status, $userId, $expected]) {
    $available = $task->getAvailableAction($status, $userId);
    $names = array_map(fn($a) => $a->getName(), $available);
    var_dump($names === $expected);
}
