<?php

require_once('Task.php');

$task = new Task(workerId: 1, clientId: 1);

assert($task->getCurrentStatus() === Status::New, 'status by default');
assert(in_array(Action::Complete, $task->getAvailableAction(Status::InProgress)), 'action for task in progress');
assert($task->getNextStatus(Action::Cancel) === Status::Canceled, 'action for canceled task');

echo 'Tests passed';
