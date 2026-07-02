<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamTask;
use App\Models\TaskComment;
use App\Models\TeamMember;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeamTaskController extends Controller
{
    /**
     * GET /teams/{teamId}/tasks
     */
    public function index(Request $request, $teamId)
    {
        $team = Team::findOrFail($teamId);
        $this->ensureMember($team);

        $query = TeamTask::where('team_id', $teamId)
            ->with([
                'assignees:id,name,email,profile_photo',
                'creator:id,name',
                'section:id,name,color,icon,is_general',
                'comments' => fn($q) => $q->select('id', 'task_id'),
                'checklist' => fn($q) => $q->select('id', 'task_id', 'is_completed'),
            ]);

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('label')) $query->where('label', $request->label);
        if ($request->filled('section_id')) $query->where('section_id', $request->section_id);

        // Filter by assignee — check pivot table
        if ($request->filled('assigned_to')) {
            $query->whereHas('assignees', fn($q) => $q->where('users.id', $request->assigned_to));
        }

        $tasks = $query->orderByRaw("CASE status WHEN 'todo' THEN 1 WHEN 'in_progress' THEN 2 WHEN 'review' THEN 3 WHEN 'done' THEN 4 END")
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 WHEN 'low' THEN 4 END")
            ->orderBy('created_at', 'desc')
            ->get();

        $tasks->each(function ($task) {
            $task->comment_count = $task->comments->count();
            $task->is_overdue = $task->isOverdue();
            $cl = $task->checklist;
            $clTotal = $cl->count();
            $clDone = $cl->where('is_completed', true)->count();
            $task->checklist_total = $clTotal;
            $task->checklist_done = $clDone;
            $task->checklist_percent = $clTotal > 0 ? round(($clDone / $clTotal) * 100) : 0;
            unset($task->comments, $task->checklist);
        });

        // Member stats from pivot table
        $memberStats = \DB::table('team_task_assignees')
            ->join('team_tasks', 'team_tasks.id', '=', 'team_task_assignees.task_id')
            ->where('team_tasks.team_id', $teamId)
            ->selectRaw('team_task_assignees.user_id, team_tasks.status, COUNT(*) as count')
            ->groupBy('team_task_assignees.user_id', 'team_tasks.status')
            ->get()
            ->groupBy('user_id')
            ->map(function ($statuses) {
                $total = $statuses->sum('count');
                $done = $statuses->where('status', 'done')->sum('count');
                return ['total' => $total, 'done' => $done, 'progress' => $total > 0 ? round(($done / $total) * 100) : 0];
            });

        return response()->json([
            'success' => true,
            'data' => ['tasks' => $tasks, 'member_stats' => $memberStats],
        ]);
    }

    /**
     * POST /teams/{teamId}/tasks
     */
    public function store(Request $request, $teamId)
    {
        $team = Team::findOrFail($teamId);
        $user = auth()->user();

        if ($team->created_by !== $user->id && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Only the team leader can create tasks'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'priority' => 'in:low,medium,high,urgent',
            'label' => 'nullable|string|max:30',
            'assignee_ids' => 'nullable|array',
            'assignee_ids.*' => 'integer|exists:users,id',
            'section_id' => 'nullable|integer|exists:team_sections,id',
            'due_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Default to general section if not specified
        $sectionId = $request->section_id;
        if (!$sectionId) {
            $generalSection = $team->generalSection;
            $sectionId = $generalSection?->id;
        }

        $task = TeamTask::create([
            'team_id' => $teamId,
            'section_id' => $sectionId,
            'assigned_by' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority ?? 'medium',
            'status' => 'todo',
            'label' => $request->label,
            'due_date' => $request->due_date,
        ]);

        // Attach assignees
        $assigneeIds = $request->assignee_ids ?? [];
        if (!empty($assigneeIds)) {
            $task->assignees()->attach($assigneeIds);

            // Notify assignees
            $notifyIds = array_filter($assigneeIds, fn($id) => $id !== $user->id);
            if (!empty($notifyIds)) {
                NotificationService::sendToMany(
                    $notifyIds, 'task_assigned',
                    'New task: ' . $request->title,
                    $user->name . ' assigned you a task in "' . $team->name . '"',
                    ['team_id' => $teamId, 'task_id' => $task->id, 'url' => '/teams/' . $teamId]
                );
            }
        }

        $task->load('assignees:id,name,email,profile_photo', 'creator:id,name');

        return response()->json(['success' => true, 'message' => 'Task created', 'data' => $task], 201);
    }

    /**
     * PUT /teams/{teamId}/tasks/{taskId}
     */
    public function update(Request $request, $teamId, $taskId)
    {
        $team = Team::findOrFail($teamId);
        $user = auth()->user();
        $task = TeamTask::where('team_id', $teamId)->findOrFail($taskId);

        $isLeader = $team->created_by === $user->id || $user->role === 'admin';
        $isAssignee = $task->assignees()->where('users.id', $user->id)->exists();

        if (!$isLeader && !$isAssignee) {
            return response()->json(['success' => false, 'message' => 'Not authorized'], 403);
        }

        // Assignee can only change status
        if (!$isLeader) {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:todo,in_progress,review,done',
            ]);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $oldStatus = $task->status;
            $task->update(['status' => $request->status]);

            if ($request->status !== $oldStatus) {
                NotificationService::send(
                    $team->created_by, 'task_status_changed',
                    $user->name . ' updated "' . $task->title . '"',
                    'Status: ' . $oldStatus . ' → ' . $request->status,
                    ['team_id' => $teamId, 'task_id' => $taskId, 'url' => '/teams/' . $teamId]
                );
            }

            $task->load('assignees:id,name,email,profile_photo', 'creator:id,name');
            return response()->json(['success' => true, 'data' => $task]);
        }

        // Leader can update everything
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:2000',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'status' => 'sometimes|in:todo,in_progress,review,done',
            'label' => 'nullable|string|max:30',
            'assignee_ids' => 'nullable|array',
            'assignee_ids.*' => 'integer|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $oldStatus = $task->status;
        $task->update($request->only(['title', 'description', 'priority', 'status', 'label', 'due_date']));

        // Sync assignees if provided
        if ($request->has('assignee_ids')) {
            $oldIds = $task->assignees()->pluck('users.id')->toArray();
            $newIds = $request->assignee_ids ?? [];
            $task->assignees()->sync($newIds);

            // Notify newly added assignees
            $addedIds = array_diff($newIds, $oldIds);
            $addedIds = array_filter($addedIds, fn($id) => $id !== $user->id);
            if (!empty($addedIds)) {
                NotificationService::sendToMany(
                    array_values($addedIds), 'task_assigned',
                    'New task: ' . $task->title,
                    $user->name . ' assigned you a task in "' . $team->name . '"',
                    ['team_id' => $teamId, 'task_id' => $taskId, 'url' => '/teams/' . $teamId]
                );
            }
        }

        // Notify assignees if status changed
        if ($request->has('status') && $request->status !== $oldStatus) {
            $assigneeIds = $task->assignees()->pluck('users.id')->reject(fn($id) => $id === $user->id)->toArray();
            if (!empty($assigneeIds)) {
                NotificationService::sendToMany(
                    array_values($assigneeIds), 'task_status_changed',
                    'Task "' . $task->title . '" updated',
                    'Status changed to ' . $request->status,
                    ['team_id' => $teamId, 'task_id' => $taskId, 'url' => '/teams/' . $teamId]
                );
            }
        }

        $task->load('assignees:id,name,email,profile_photo', 'creator:id,name');
        return response()->json(['success' => true, 'data' => $task]);
    }

    /**
     * DELETE /teams/{teamId}/tasks/{taskId}
     */
    public function destroy($teamId, $taskId)
    {
        $team = Team::findOrFail($teamId);
        $user = auth()->user();
        if ($team->created_by !== $user->id && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Not authorized'], 403);
        }

        $task = TeamTask::where('team_id', $teamId)->findOrFail($taskId);
        $task->delete();

        return response()->json(['success' => true, 'message' => 'Task deleted']);
    }

    /**
     * GET /teams/{teamId}/tasks/{taskId}/comments
     */
    public function comments($teamId, $taskId)
    {
        $this->ensureMember(Team::findOrFail($teamId));
        $task = TeamTask::where('team_id', $teamId)->findOrFail($taskId);

        return response()->json([
            'success' => true,
            'data' => $task->comments()->with('user:id,name,email,profile_photo')->orderBy('created_at', 'asc')->get(),
        ]);
    }

    /**
     * POST /teams/{teamId}/tasks/{taskId}/comments
     */
    public function addComment(Request $request, $teamId, $taskId)
    {
        $team = Team::findOrFail($teamId);
        $this->ensureMember($team);
        $user = auth()->user();
        $task = TeamTask::where('team_id', $teamId)->findOrFail($taskId);

        $validator = Validator::make($request->all(), ['content' => 'required|string|max:1000']);
        if ($validator->fails()) return response()->json(['success' => false, 'errors' => $validator->errors()], 422);

        $comment = TaskComment::create(['task_id' => $taskId, 'user_id' => $user->id, 'content' => $request->content]);
        $comment->load('user:id,name,email,profile_photo');

        // Notify assignees + leader
        $notifyIds = $task->assignees()->pluck('users.id')
            ->merge([$team->created_by])
            ->unique()
            ->reject(fn($id) => $id === $user->id)
            ->values()
            ->toArray();

        if (!empty($notifyIds)) {
            NotificationService::sendToMany($notifyIds, 'task_comment',
                $user->name . ' commented on "' . $task->title . '"',
                mb_substr($request->content, 0, 80),
                ['team_id' => $teamId, 'task_id' => $taskId, 'url' => '/teams/' . $teamId]
            );
        }

        return response()->json(['success' => true, 'data' => $comment], 201);
    }

    /**
     * GET /teams/my-tasks — All tasks assigned to current user across all teams
     */
    public function myTasks()
    {
        $user = auth()->user();

        $tasks = TeamTask::whereHas('assignees', fn($q) => $q->where('users.id', $user->id))
            ->with([
                'team:id,name,code',
                'assignees:id,name,profile_photo',
                'section:id,name,color',
                'checklist' => fn($q) => $q->select('id', 'task_id', 'is_completed'),
            ])
            ->orderByRaw("CASE status WHEN 'in_progress' THEN 1 WHEN 'todo' THEN 2 WHEN 'review' THEN 3 WHEN 'done' THEN 4 END")
            ->orderBy('due_date', 'asc')
            ->get();

        $tasks->each(function ($task) {
            $cl = $task->checklist;
            $clTotal = $cl->count();
            $clDone = $cl->where('is_completed', true)->count();
            $task->checklist_total = $clTotal;
            $task->checklist_done = $clDone;
            $task->checklist_percent = $clTotal > 0 ? round(($clDone / $clTotal) * 100) : 0;
            $task->is_overdue = $task->isOverdue();
            unset($task->checklist);
        });

        return response()->json(['success' => true, 'data' => $tasks]);
    }

    private function ensureMember(Team $team)
    {
        if (auth()->user()->role === 'admin') return;
        $isMember = TeamMember::where('team_id', $team->id)->where('user_id', auth()->id())->exists();
        if (!$isMember) abort(403, 'Not a team member');
    }
}
