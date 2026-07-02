<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin   = 'admin';
    case Learner = 'learner';

    /**
     * Used directly in validation rules:
     *   'role' => ['required', 'in:' . UserRole::validationValues()]
     */
    public static function validationValues(): string
    {
        return implode(',', array_column(self::cases(), 'value'));
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}