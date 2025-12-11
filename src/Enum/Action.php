<?php

namespace TaskForce\Enum;

enum Action: string
{
    case CANCEL = 'Cancel';
    case RESPOND = 'Respond';
    case DECLINE = 'Decline';
    case COMPLETE = 'Complete';

    public function label(): string
    {
        return match($this) {
            self::CANCEL->value => 'Отменить',
            self::RESPOND->value => 'Откликнуться',
            self::DECLINE->value => 'Отказаться',
            self::COMPLETE->value => 'Завершить',
        };
    }
}
