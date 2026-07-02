<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'icon',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'track_courses')
                    ->withPivot('order')
                    ->orderBy('order');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_tracks')
                    ->withTimestamps();
    }

    public function teamMembers()
    {
        return $this->hasMany(TeamMember::class);
    }
}