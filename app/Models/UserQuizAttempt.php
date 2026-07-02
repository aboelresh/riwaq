<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'max_score',
        'passed',
        'can_retry_at',
        'attempted_at',
    ];

    protected $casts = [
        'score' => 'integer',
        'max_score' => 'integer',
        'passed' => 'boolean',
        'can_retry_at' => 'datetime',
        'attempted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers()
    {
        return $this->hasMany(UserQuizAnswer::class, 'attempt_id');
    }

    public function getPercentageAttribute(): float
    {
        if ($this->max_score == 0) {
            return 0;
        }
        return round(($this->score / $this->max_score) * 100, 2);
    }

    public function canRetryNow(): bool
    {
        if (is_null($this->can_retry_at)) {
            return true;
        }
        return now()->greaterThanOrEqualTo($this->can_retry_at);
    }
}