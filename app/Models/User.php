<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'profile_photo',
        'bio',
        'goals',
        'profile_completed',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Always return full URL for profile_photo in JSON responses
     */
    public function getProfilePhotoAttribute($value): ?string
    {
        if (!$value) return null;
        if (str_starts_with($value, 'http')) return $value;
        return asset('storage/' . $value);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isLearner(): bool
    {
        return $this->role === 'learner';
    }

    public function tracks()
    {
        return $this->belongsToMany(Track::class, 'user_tracks')
                    ->withTimestamps();
    }

    public function courseProgress()
    {
        return $this->hasMany(UserCourseProgress::class);
    }

    public function topicProgress()
    {
        return $this->hasMany(UserTopicProgress::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(UserQuizAttempt::class);
    }

    public function createdTracks()
    {
        return $this->hasMany(Track::class, 'created_by');
    }

    public function createdCourses()
    {
        return $this->hasMany(Course::class, 'created_by');
    }

    public function createdTopics()
    {
        return $this->hasMany(Topic::class, 'created_by');
    }

    public function createdQuizzes()
    {
        return $this->hasMany(Quiz::class, 'created_by');
    }

    public function uploadedVideos()
    {
        return $this->hasMany(Video::class, 'uploaded_by');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_members')
                    ->withPivot('track_id', 'joined_at')
                    ->withTimestamps();
    }

    public function createdTeams()
    {
        return $this->hasMany(Team::class, 'created_by');
    }
}