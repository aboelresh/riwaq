<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCourseProgress extends Model
{
    use HasFactory;

    protected $table = 'user_course_progress';

    protected $fillable = [
        'user_id',
        'course_id',
        'is_unlocked',
        'total_score',
        'max_possible_score',
        'is_completed',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'is_unlocked' => 'boolean',
        'is_completed' => 'boolean',
        'total_score' => 'integer',
        'max_possible_score' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->max_possible_score == 0) {
            return 0;
        }
        return round(($this->total_score / $this->max_possible_score) * 100, 2);
    }

    public function hasPassed(): bool
    {
        return $this->progress_percentage >= 50;
    }
}