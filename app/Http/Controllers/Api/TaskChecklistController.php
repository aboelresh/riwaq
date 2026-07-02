<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamTask;
use App\Models\TaskChecklistItem;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskChecklistController extends Controller
{
    /**
     * GET /teams/{teamId}/tasks/{taskId}/checklist
     */
    public function index($teamId, $taskId)
    {
        $this->ensureMember($teamId);
        $task = TeamTask::where('team_id', $teamId)->findOrFail($taskId);

        $items = $task->checklist()->with('completedByUser:id,name,profile_photo')->get();
        $progress = $task->checklistProgress();

        return response()->json(['success' => true, 'data' => ['items' => $items, 'progress' => $progress]]);
    }

    /**
     * POST /teams/{teamId}/tasks/{taskId}/checklist
     * Body: { content } or { items: ["item1", "item2"] } for bulk
     */
    public function store(Request $request, $teamId, $taskId)
    {
        $team = Team::findOrFail($teamId);
        $user = auth()->user();
        $task = TeamTask::where('team_id', $teamId)->findOrFail($taskId);

        // Leader or assignee can add checklist items
        $isLeader = $team->created_by === $user->id || $user->role === 'admin';
        $isAssignee = $task->assignees()->where('users.id', $user->id)->exists();
        if (!$isLeader && !$isAssignee) {
            return response()->json(['success' => false, 'message' => 'Not authorized'], 403);
        }

        // Bulk add
        if ($request->has('items') && is_array($request->items)) {
            $validator = Validator::make($request->all(), [
                'items' => 'required|array|min:1|max:20',
                'items.*' => 'required|string|max:500',
            ]);
            if ($validator->fails()) return response()->json(['success' => false, 'errors' => $validator->errors()], 422);

            $maxOrder = $task->checklist()->max('order') ?? 0;
            $created = [];
            foreach ($request->items as $i => $content) {
                $created[] = TaskChecklistItem::create([
                    'task_id' => $taskId,
                    'content' => $content,
                    'order' => $maxOrder + $i + 1,
                ]);
            }
            return response()->json(['success' => true, 'data' => $created], 201);
        }

        // Single add
        $validator = Validator::make($request->all(), ['content' => 'required|string|max:500']);
        if ($validator->fails()) return response()->json(['success' => false, 'errors' => $validator->errors()], 422);

        $maxOrder = $task->checklist()->max('order') ?? 0;
        $item = TaskChecklistItem::create([
            'task_id' => $taskId,
            'content' => $request->content,
            'order' => $maxOrder + 1,
        ]);

        return response()->json(['success' => true, 'data' => $item], 201);
    }

    /**
     * PUT /teams/{teamId}/tasks/{taskId}/checklist/{itemId}/toggle
     */
    public function toggle($teamId, $taskId, $itemId)
    {
        $this->ensureMember($teamId);
        $task = TeamTask::where('team_id', $teamId)->findOrFail($taskId);
        $item = TaskChecklistItem::where('task_id', $taskId)->findOrFail($itemId);

        $user = auth()->user();
        $item->update([
            'is_completed' => !$item->is_completed,
            'completed_by' => !$item->is_completed ? $user->id : null,
            'completed_at' => !$item->is_completed ? now() : null,
        ]);

        $item->load('completedByUser:id,name,profile_photo');
        $progress = $task->checklistProgress();

        return response()->json(['success' => true, 'data' => ['item' => $item, 'progress' => $progress]]);
    }

    /**
     * PUT /teams/{teamId}/tasks/{taskId}/checklist/{itemId}
     */
    public function update(Request $request, $teamId, $taskId, $itemId)
    {
        $team = Team::findOrFail($teamId);
        $user = auth()->user();
        $isLeader = $team->created_by === $user->id || $user->role === 'admin';
        if (!$isLeader) return response()->json(['success' => false, 'message' => 'Not authorized'], 403);

        $item = TaskChecklistItem::where('task_id', $taskId)->findOrFail($itemId);

        $validator = Validator::make($request->all(), [
            'content' => 'sometimes|string|max:500',
            'order' => 'sometimes|integer|min:0',
        ]);
        if ($validator->fails()) return response()->json(['success' => false, 'errors' => $validator->errors()], 422);

        $item->update($request->only(['content', 'order']));
        return response()->json(['success' => true, 'data' => $item]);
    }

    /**
     * DELETE /teams/{teamId}/tasks/{taskId}/checklist/{itemId}
     */
    public function destroy($teamId, $taskId, $itemId)
    {
        $team = Team::findOrFail($teamId);
        $user = auth()->user();
        $isLeader = $team->created_by === $user->id || $user->role === 'admin';
        if (!$isLeader) return response()->json(['success' => false, 'message' => 'Not authorized'], 403);

        $item = TaskChecklistItem::where('task_id', $taskId)->findOrFail($itemId);
        $item->delete();

        return response()->json(['success' => true, 'message' => 'Item deleted']);
    }

    private function ensureMember($teamId)
    {
        if (auth()->user()->role === 'admin') return;
        $isMember = TeamMember::where('team_id', $teamId)->where('user_id', auth()->id())->exists();
        if (!$isMember) abort(403, 'Not a team member');
    }
}
