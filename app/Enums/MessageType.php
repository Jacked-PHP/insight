<?php

namespace App\Enums;

enum MessageType: string
{
    case REQUEST = 'request';
    case RESPONSE = 'response';
}
