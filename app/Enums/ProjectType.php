<?php

namespace App\Enums;

enum ProjectType: string
{
    case Web    = 'web';
    case Mobile = 'mobile';
    case Data   = 'data';
    case Game   = 'game';

    public static function validationValues(): string
    {
        return implode(',', array_column(self::cases(), 'value'));
    }
}