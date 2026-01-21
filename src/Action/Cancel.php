<?php

namespace TaskForce\Action;

use TaskForce\Enum\Status;

class Cancel extends AbstractAction
{
    public function getName(): string
    {
        return 'Cancel';
    }

    public function getInternalName(): string
    {
        return 'Отменить';
    }

    public function checkRights(int $workerId, int $clientId, int $userId): bool
    {
        return $userId === $clientId;
    }

    public function getNextStatus(): Status
    {
        return Status::CANCELED;
    }
}
