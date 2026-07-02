<?php

namespace App\Enums;

enum TaskPriority: string
{
    case Low    = 'low';
    case Medium = 'medium';
    case High   = 'high';
    case Urgent = 'urgent';

    public static function validationValues(): string
    {
        return implode(',', array_column(self::cases(), 'value'));
    }
}