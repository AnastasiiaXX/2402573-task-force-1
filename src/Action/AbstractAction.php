<?php

namespace TaskForce\Action;

use TaskForce\Enum\Status;

abstract class AbstractAction
{
    /**
     * Название действия для пользователя
     * @return string
     */
    abstract public function getName(): string;

    /**
     * Внутреннее название действия
     * @return string
     */
    abstract public function getInternalName(): string;

    /**
     * Проверка прав на разрешение выполнения действия
     *
     * @param int $workerId
     * @param int $clientId
     * @param int $userId
     * @return bool
     */
    abstract public function checkRights(int $workerId, int $clientId, int $userId): bool;

     /**
     * Следующий статус задачи после выполнения действия
     *
     * @return Status
     */
    abstract public function getNextStatus(): Status;
}
