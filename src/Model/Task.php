<?php

declare(strict_types=1);

namespace TaskForce\Model;

use TaskForce\Action\AbstractAction;
use TaskForce\Enum\Status;
use TaskForce\Action\Cancel;
use TaskForce\Action\Complete;
use TaskForce\Action\Decline;
use TaskForce\Action\Respond;
use TaskForce\Exception\TaskException;

class Task
{
    public int $workerId = 0;
    public int $clientId = 0;
    private Status $currentStatus;

    /**
     * Task constructor.
     *
     * @param int $workerId ID исполнителя
     * @param int $clientId ID клиента
     */
    public function __construct(int $workerId, int $clientId)
    {
        if ($workerId <= 0 || $clientId <= 0) {
            throw new TaskException('User ID must be a positive integer');
        }

        $this->workerId = $workerId;
        $this->clientId = $clientId;
        $this->currentStatus = Status::NEW;
    }

    /**
     * Возвращает массив всех статусов
     *
     * @return Status[]
     */
    public function getAllStatuses(): array
    {
        return Status::cases();
    }

    /**
     * Возвращает массив всех действий
     *
     * @return AbstractAction[]
     */
    public function getAllActions(): array
    {
        return [new Cancel(), new Respond(), new Complete(), new Decline()];
    }

    /**
     * Возвращает следующий статус для данного действия
     *
     * @param AbstractAction $action действие с заданием
     *
     * @return Status|null доступный статус или null если его нет
     */
    public function getNextStatus(AbstractAction $action): ?Status
    {
        return $action->getNextStatus();
    }

    /**
     * Возвращает доступное действие в зависимости от статуса
     *
     * @param Status $status статус задания
     *
     * @return AbstractAction[] массив доступных действий
     */
    public function getAvailableAction(Status $status, int $userId): array
    {
        if($userId <= 0) {
            throw new TaskException('User ID must be a positive integer');
        }
        switch ($status) {
            case Status::NEW:
                $actions = [new Cancel(), new Respond()];
                break;
            case Status::INPROGRESS:
                $actions = [new Complete(), new Decline()];
                break;
            default:
                $actions = [];
        }

        return array_filter($actions, fn($action) => $action->checkRights($userId, $this->workerId, $this->clientId));
    }
    /**
     * Получение текущего статуса
     *
     * @return Status текущий статус задачи
     */
    public function getCurrentStatus(): Status
    {
        return $this->currentStatus;
    }
}
