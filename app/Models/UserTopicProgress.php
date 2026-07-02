<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTopicProgress extends Model
{
    use HasFactory;

    protected $table = 'user_topic_progress';

    protected $fillable = [
        'user_id',
        'topic_id',
        'is_unlocked',
        'is_viewed',
        'viewed_at',
        'video_watched_seconds',
        'video_total_seconds',
        'video_last_position',
    ];

    protected $casts = [
        'is_unlocked' => 'boolean',
        'is_viewed' => 'boolean',
        'viewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}