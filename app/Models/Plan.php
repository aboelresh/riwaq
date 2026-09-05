<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name', 'slug', 'description',
        'price_monthly', 'price_yearly',
        'is_public', 'is_active',
        'max_students', 'max_instructors',
        'max_courses', 'max_tracks',
        'max_storage_gb', 'max_ai_calls_per_month',
        'allow_custom_domain', 'allow_white_label', 'allow_api_access',
    ];

    protected $casts = [
        'is_public'            => 'boolean',
        'is_active'            => 'boolean',
        'allow_custom_domain'  => 'boolean',
        'allow_white_label'    => 'boolean',
        'allow_api_access'     => 'boolean',
        'price_monthly'        => 'decimal:2',
        'price_yearly'         => 'decimal:2',
    ];
}