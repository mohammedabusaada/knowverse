<?php

namespace App\Enums;

enum ReportTargetType: string
{
    case POST    = 'post';
    case COMMENT = 'comment';
    case USER    = 'user';
}
