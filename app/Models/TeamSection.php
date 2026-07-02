<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamSection extends Model
{
    use HasFactory;

    protected $fillable = ['team_id', 'name', 'description', 'is_general', 'color', 'icon', 'created_by'];

    protected $casts = ['is_general' => 'boolean'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'team_section_members', 'section_id', 'user_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(TeamTask::class, 'section_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
