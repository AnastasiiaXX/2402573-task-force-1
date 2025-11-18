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
    private array $statuses;
    private array $actions;


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
        $this->currentStatus = Status::New;

        $this->statuses = [
            Status::New->value => 'Новое',
            Status::Canceled->value => 'Отменено',
            Status::InProgress->value => 'В работе',
            Status::Completed->value => 'Выполнено',
            Status::Failed->value => 'Провалено',
        ];

        $this->actions = [
            Action::Cancel->value => 'Отменить',
            Action::Respond->value => 'Откликнуться',
            Action::Decline->value => 'Отказаться',
            Action::Complete->value => 'Завершить',
        ];
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
     * Возвращает карту статусов (для отображения)
     *
     * @return array
     */
    public function getStatusesMap(): array
    {
        return $this->statuses;
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
     * Возвращает карту всех действий
     *
     * @return array
     */
    public function getActionsMap(): array
    {
        return $this->actions;
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
            case Action::Cancel:
                return Status::Canceled;
            case Action::Respond:
                return Status::InProgress;
            case Action::Complete:
                return Status::Completed;
            case Action::Decline:
                return Status::Failed;
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
            case Status::New:
                return [Action::Cancel, Action::Respond];
            case Status::InProgress:
                return [Action::Complete, Action::Decline];
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
