<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentResult extends Model
{
    protected $fillable = [
        'user_id',
        'recommended_track_id',
        'answers',
        'scores',
        'analysis',
    ];

    protected $casts = [
        'answers' => 'array',
        'scores' => 'array',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recommendedTrack()
    {
        return $this->belongsTo(Track::class, 'recommended_track_id');
    }
}