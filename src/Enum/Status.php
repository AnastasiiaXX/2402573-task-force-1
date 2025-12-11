<?php

namespace TaskForce\Enum;

enum Status: string
{
    case NEW = 'New';
    case CANCELED = 'Canceled';
    case INPROGRESS = 'In progress';
    case COMPLETED = 'Completed';
    case FAILED = 'Failed';

    public function label(): string
    {
        return match($this) {
            self::NEW->value => 'Новое',
            self::CANCELED->value => 'Отменено',
            self::INPROGRESS->value => 'В работе',
            self::COMPLETED->value => 'Выполнено',
            self::FAILED->value => 'Провалено',
        };
    }
}
