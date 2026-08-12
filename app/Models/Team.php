<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Team extends Model
{
    use HasFactory, \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'code',
        'project_type',
        'max_members',
        'created_by',
    ];

    protected $casts = [
        'max_members' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($team) {
            if (empty($team->code)) {
                $team->code = strtoupper(Str::random(8));
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->hasMany(TeamMember::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'team_members')
                    ->withPivot('track_id', 'joined_at')
                    ->withTimestamps();
    }

    public function sections()
    {
        return $this->hasMany(TeamSection::class);
    }

    public function generalSection()
    {
        return $this->hasOne(TeamSection::class)->where('is_general', true);
    }

    public function isFull(): bool
    {
        return $this->members()->count() >= $this->max_members;
    }
}