<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'topic_id',
        'course_id',
        'total_points',
        'pass_percentage',
        'created_by',
    ];

    protected $casts = [
        'total_points' => 'integer',
        'pass_percentage' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function attempts()
    {
        return $this->hasMany(UserQuizAttempt::class);
    }

    public function isAssessment(): bool
    {
        return $this->type === 'assessment';
    }

    public function isTopicQuiz(): bool
    {
        return $this->type === 'topic';
    }

    public function isCourseQuiz(): bool
    {
        return $this->type === 'course';
    }
}