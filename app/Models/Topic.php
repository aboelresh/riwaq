<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\SaaS\BelongsToTenant;

class Topic extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'title',
        'type',
        'content',
        'video_url',
        'video_duration',
        'created_by',
    ];

    protected $casts = [
        'video_duration' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_topics')
                    ->withPivot('order')
                    ->orderBy('order');
    }

    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }

    public function userProgress()
    {
        return $this->hasMany(UserTopicProgress::class);
    }

    public function isArticle(): bool
    {
        return $this->type === 'article';
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }
}