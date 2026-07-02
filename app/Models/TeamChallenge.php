<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamChallenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id', 'title', 'description', 'target_type',
        'target_value', 'current_value', 'week_start', 'week_end',
        'is_completed', 'created_by',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'week_start' => 'date',
        'week_end' => 'date',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeCurrentWeek($query)
    {
        $now = now()->toDateString();
        return $query->where('week_start', '<=', $now)->where('week_end', '>=', $now);
    }
}
