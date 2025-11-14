<?php

declare(strict_types=1);

enum Status: string
{
    case New = 'New';
    case Canceled = 'Canceled';
    case InProgress = 'In progress';
    case Completed = 'Completed';
    case Failed = 'Failed';
}

enum Action: string
{
    case Cancel = 'Cancel';
    case Respond = 'Respond';
    case Decline = 'Decline';
    case Complete = 'Complete';
}

class Task
{
    public int $workerId = 0;
    public int $clientId = 0;
    private Status $currentStatus;
    private array $statuses;
    private array $actions;

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

    public function getAllStatuses(): array
    {
        return Status::cases();
    }

    public function getStatusesMap(): array
    {
        return $this->statuses;
    }

    public function getAllActions(): array
    {
        return Action::cases();
    }

    public function getActionsMap(): array
    {
        return $this->actions;
    }

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

    public function getCurrentStatus()
    {
        return $this->currentStatus;
    }
}
