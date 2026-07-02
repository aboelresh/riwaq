<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id', 'section_id', 'assigned_to', 'assigned_by',
        'title', 'description', 'priority', 'status', 'label', 'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function section()
    {
        return $this->belongsTo(TeamSection::class, 'section_id');
    }

    /** Legacy single assignee (kept for backward compat) */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /** Multiple assignees via pivot */
    public function assignees()
    {
        return $this->belongsToMany(User::class, 'team_task_assignees', 'task_id', 'user_id')
            ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class, 'task_id');
    }

    public function checklist()
    {
        return $this->hasMany(TaskChecklistItem::class, 'task_id')->orderBy('order');
    }

    public function checklistProgress(): array
    {
        $total = $this->checklist()->count();
        if ($total === 0) return ['total' => 0, 'done' => 0, 'percent' => 0];
        $done = $this->checklist()->where('is_completed', true)->count();
        return ['total' => $total, 'done' => $done, 'percent' => round(($done / $total) * 100)];
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'done';
    }
}
