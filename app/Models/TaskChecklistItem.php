<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskChecklistItem extends Model
{
    use HasFactory;

    protected $fillable = ['task_id', 'content', 'is_completed', 'order', 'completed_by', 'completed_at'];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'order' => 'integer',
    ];

    public function task()
    {
        return $this->belongsTo(TeamTask::class, 'task_id');
    }

    public function completedByUser()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
