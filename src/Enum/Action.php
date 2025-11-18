<?php

namespace TaskForce\Enum;

enum Action: string
{
    case Cancel = 'Cancel';
    case Respond = 'Respond';
    case Decline = 'Decline';
    case Complete = 'Complete';
}
