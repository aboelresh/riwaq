<?php

namespace App\Enums;

enum TeamType: string
{
    case Learning = 'learning';
    case Project  = 'project';

    public static function validationValues(): string
    {
        return implode(',', array_column(self::cases(), 'value'));
    }
}