<?php

namespace TaskForce\Action;

use TaskForce\Enum\Status;

class Complete extends AbstractAction
{
    public function getName(): string
    {
        return 'Complete';
    }

    public function getInternalName(): string
    {
        return 'Завершить';
    }

    public function checkRights(int $workerId, int $clientId, int $userId): bool
    {
        return $userId === $clientId || $userId === $workerId;
    }

    public function getNextStatus(): Status
    {
        return Status::COMPLETED;
    }
}
