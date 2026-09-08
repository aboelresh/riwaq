<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'organization_id', 'plan_id', 'status',
        'billing_interval', 'trial_ends_at',
        'current_period_start', 'current_period_end',
        'canceled_at', 'provider', 'provider_id',
        'provider_status', 'provider_data',
    ];

    protected $casts = [
        'trial_ends_at'          => 'datetime',
        'current_period_start'   => 'datetime',
        'current_period_end'     => 'datetime',
        'canceled_at'            => 'datetime',
        'provider_data'          => 'array',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'trialing']);
    }

    public function isTrialing(): bool
    {
        return $this->status === 'trialing'
            && $this->trial_ends_at
            && $this->trial_ends_at->isFuture();
    }
}