<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamNote extends Model
{
    use HasFactory;

    protected $fillable = ['team_id', 'user_id', 'author_id', 'content', 'is_private'];

    protected $casts = ['is_private' => 'boolean'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /** The member this note is about */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** The leader/admin who wrote this note */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
