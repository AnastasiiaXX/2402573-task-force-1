<?php

namespace TaskForce\Action;

use TaskForce\Enum\Status;

class Decline extends AbstractAction
{
    public function getName(): string
    {
        return 'Decline';
    }

    public function getInternalName(): string
    {
        return 'Отказаться';
    }

    public function checkRights(int $workerId, int $clientId, int $userId): bool
    {
        return $userId === $workerId;
    }

    public function getNextStatus(): Status
    {
        return Status::FAILED;
    }
}
