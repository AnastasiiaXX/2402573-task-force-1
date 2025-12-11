<?php

declare(strict_types=1);

namespace TaskForce\Model;

use TaskForce\Enum\Status;
use TaskForce\Enum\Action;

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
     * @return Action[]
     */
    public function getAllActions(): array
    {
        return Action::cases();
    }

    /**
     * Возвращает следующий доступный статус
     *
     * @param Action $action действие с заданием
     *
     * @return Status|null доступный статус или null если его нет
     */
    public function getNextStatus(Action $action): ?Status
    {
        switch ($action) {
            case Action::CANCEL:
                return Status::CANCELED;
            case Action::RESPOND:
                return Status::INPROGRESS;
            case Action::COMPLETE:
                return Status::COMPLETED;
            case Action::DECLINE:
                return Status::FAILED;
            default:
                return null;
        }
    }

    /**
     * Возвращает доступное действие в зависимости от статуса
     *
     * @param Status $status статус задания
     *
     * @return Action[] массив доступных действий
     */
    public function getAvailableAction(Status $status): array
    {
        switch ($status) {
            case Status::NEW:
                return [Action::CANCEL, Action::RESPOND];
            case Status::INPROGRESS:
                return [Action::COMPLETE, Action::DECLINE];
            default:
                return [];
        }
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
