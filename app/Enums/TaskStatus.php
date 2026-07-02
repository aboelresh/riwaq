<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Todo       = 'todo';
    case InProgress = 'in_progress';
    case Review     = 'review';
    case Done       = 'done';

    public static function validationValues(): string
    {
        return implode(',', array_column(self::cases(), 'value'));
    }
}