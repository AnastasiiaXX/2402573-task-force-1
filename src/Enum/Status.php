<?php

namespace TaskForce\Enum;

enum Status: string
{
    case New = 'New';
    case Canceled = 'Canceled';
    case InProgress = 'In progress';
    case Completed = 'Completed';
    case Failed = 'Failed';
}
