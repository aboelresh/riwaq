<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLevelAnalysis extends Model
{
    protected $fillable = ['user_id', 'stats', 'analysis', 'model_used', 'analyzed_at'];

    protected $casts = [
        'stats' => 'array',
        'analysis' => 'array',
        'analyzed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
