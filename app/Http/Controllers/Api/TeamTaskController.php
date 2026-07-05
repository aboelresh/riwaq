<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\AddCommentRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Team;
use App\Models\TeamTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamTaskController extends Controller
{
    public function index(Request $request, $teamId): JsonResponse
    {
        Team::findOrFail($teamId);

        $query = TeamTask::where('team_id', $teamId)
            ->with(['assignees', 'section', 'comments']);

        if ($request->filled('status'))     $query->where('status', $request->status);
        if ($request->filled('priority'))   $query->where('priority', $request->priority);
        if ($request->filled('label'))      $query->where('label', $request->label);
        if ($request->filled('section_id')) $query->where('section_id', $request->section_id);
        if ($request->filled('assigned_to')) {
            $query->whereHas('assignees', fn($q) => $q->where('user_id', $request->assigned_to));
        }

        $tasks = $query->orderByRaw("CASE status
            WHEN 'todo' THEN 1 WHEN 'in_progress' THEN 2
            WHEN 'review' THEN 3 WHEN 'done' THEN 4 ELSE 5 END")
            ->orderByRaw("CASE priority
                WHEN 'urgent' THEN 1 WHEN 'high' THEN 2
                WHEN 'medium' THEN 3 WHEN 'low' THEN 4 ELSE 5 END")
            ->get();

        return response()->json(['success' => true, 'data' => $tasks]);
    }

    public function myTasks(): JsonResponse
    {
        $user  = auth()->user();
        $tasks = TeamTask::whereHas('assignees', fn($q) => $q->where('user_id', $user->id))
            ->with(['team', 'section'])
            ->get();

        return response()->json(['success' => true, 'data' => $tasks]);
    }

    public function store(StoreTaskRequest $request, $teamId): JsonResponse
    {
        $team = Team::findOrFail($teamId);
        $user = auth()->user();

        $isLeader = $team->created_by === $user->id;
        if (!$isLeader && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Only the team leader can create tasks.',
            ], 403);
        }

        $task = TeamTask::create([
    'team_id'     => $teamId,
    'section_id'  => $request->section_id,
    'title'       => $request->title,
    'description' => $request->description,
    'status'      => $request->status ?? 'todo',
    'priority'    => $request->priority ?? 'medium',
    'label'       => $request->label,
    'due_date'    => $request->due_date,
    'created_by'  => $user->id,
    'assigned_by' => $user->id,
]);

        if ($request->filled('assignee_ids')) {
            $task->assignees()->sync($request->assignee_ids);
        }

        return response()->json([
            'success' => true,
            'message' => 'Task created.',
            'data'    => $task->load('assignees', 'section'),
        ], 201);
    }

    public function update(UpdateTaskRequest $request, $teamId, $taskId): JsonResponse
    {
        $team = Team::findOrFail($teamId);
        $task = TeamTask::where('team_id', $teamId)->findOrFail($taskId);
        $user = auth()->user();

        $isLeader   = $team->created_by === $user->id || $user->isAdmin();
        $isAssignee = $task->assignees()->where('user_id', $user->id)->exists();

        if (!$isLeader && !$isAssignee) {
            return response()->json(['success' => false, 'message' => 'Not authorized to update this task.'], 403);
        }

        $fields = $isLeader
            ? $request->only(['title', 'description', 'status', 'priority', 'label', 'section_id', 'due_date'])
            : $request->only(['status']); // assignees can only change status

        $task->update($fields);

        if ($isLeader && $request->filled('assignee_ids')) {
            $task->assignees()->sync($request->assignee_ids);
        }

        return response()->json(['success' => true, 'message' => 'Task updated.', 'data' => $task->load('assignees')]);
    }

    public function destroy($teamId, $taskId): JsonResponse
    {
        $team = Team::findOrFail($teamId);
        $task = TeamTask::where('team_id', $teamId)->findOrFail($taskId);
        $user = auth()->user();

        if ($team->created_by !== $user->id && !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Not authorized.'], 403);
        }

        $task->delete();

        return response()->json(['success' => true, 'message' => 'Task deleted.']);
    }

    public function comments($teamId, $taskId): JsonResponse
    {
        TeamTask::where('team_id', $teamId)->findOrFail($taskId);

        $comments = \App\Models\TaskComment::where('task_id', $taskId)
            ->with('user')
            ->latest()
            ->get();

        return response()->json(['success' => true, 'data' => $comments]);
    }

    public function addComment(AddCommentRequest $request, $teamId, $taskId): JsonResponse
    {
        TeamTask::where('team_id', $teamId)->findOrFail($taskId);

        $comment = \App\Models\TaskComment::create([
            'task_id' => $taskId,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment added.',
            'data'    => $comment->load('user'),
        ], 201);
    }
}