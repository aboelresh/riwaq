<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiUsageLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'user_id',
        'feature',
        'tokens_used',
        'provider',
        'was_fallback',
        'used_at',
    ];

    protected $casts = [
        'was_fallback' => 'boolean',
        'used_at'      => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}