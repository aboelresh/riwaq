<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\SaaS\BelongsToTenant;


class Course extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'title',
        'description',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tracks()
    {
        return $this->belongsToMany(Track::class, 'track_courses')
                    ->withPivot('order')
                    ->orderBy('order');
    }

    public function topics()
    {
        return $this->belongsToMany(Topic::class, 'course_topics')
                    ->withPivot('order')
                    ->orderBy('order');
    }
    public function quizzes()
{
    return $this->hasMany(Quiz::class);
}


    public function userProgress()
    {
        return $this->hasMany(UserCourseProgress::class);
    }
}