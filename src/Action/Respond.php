<?php

namespace TaskForce\Action;

use TaskForce\Enum\Status;

class Respond extends AbstractAction
{
    public function getName(): string
    {
        return 'Respond';
    }

    public function getInternalName(): string
    {
        return 'Откликнуться';
    }

    public function checkRights(int $workerId, int $clientId, int $userId): bool
    {
        return $userId === $workerId;
    }

    public function getNextStatus(): Status
    {
        return Status::INPROGRESS;
    }
}
