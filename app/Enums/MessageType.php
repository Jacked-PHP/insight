<?php

namespace App\Enums;

enum MessageType: string
{
    case SYSTEM = 'system';
    case REQUEST = 'request';
    case RESPONSE = 'response';
}
