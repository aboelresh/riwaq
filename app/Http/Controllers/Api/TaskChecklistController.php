<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TaskChecklistItem;
use App\Models\TeamTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskChecklistController extends Controller
{
    public function index($teamId, $taskId): JsonResponse
    {
        TeamTask::where('team_id', $teamId)->findOrFail($taskId);

        $items = TaskChecklistItem::where('task_id', $taskId)
            ->with('completedBy')
            ->orderBy('created_at')
            ->get();

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function store(Request $request, $teamId, $taskId): JsonResponse
    {
        TeamTask::where('team_id', $teamId)->findOrFail($taskId);

        $isBulk = $request->has('items');

        if ($isBulk) {
            $request->validate([
                'items'   => 'required|array|min:1|max:20',
                'items.*' => 'required|string|max:500',
            ]);

            // Bug 011 Fix: wrap bulk creation in transaction
            $items = DB::transaction(function () use ($request, $taskId) {
                return collect($request->items)->map(fn($content) =>
                    TaskChecklistItem::create([
                        'task_id' => $taskId,
                        'content' => $content,
                    ])
                );
            });

            return response()->json([
                'success' => true,
                'message' => count($request->items) . ' items added.',
                'data'    => $items,
            ], 201);
        }

        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $item = TaskChecklistItem::create([
            'task_id' => $taskId,
            'content' => $request->content,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Checklist item added.',
            'data'    => $item,
        ], 201);
    }

    public function toggle($teamId, $taskId, $itemId): JsonResponse
    {
        TeamTask::where('team_id', $teamId)->findOrFail($taskId);
        $item = TaskChecklistItem::where('task_id', $taskId)->findOrFail($itemId);

        if ($item->is_completed) {
            $item->update([
                'is_completed'  => false,
                'completed_by'  => null,
                'completed_at'  => null,
            ]);
        } else {
            $item->update([
                'is_completed'  => true,
                'completed_by'  => auth()->id(),
                'completed_at'  => now(),
            ]);
        }

        return response()->json(['success' => true, 'data' => $item->load('completedBy')]);
    }

    public function update(Request $request, $teamId, $taskId, $itemId): JsonResponse
    {
        TeamTask::where('team_id', $teamId)->findOrFail($taskId);
        $item = TaskChecklistItem::where('task_id', $taskId)->findOrFail($itemId);

        $request->validate(['content' => 'required|string|max:500']);

        $item->update(['content' => $request->content]);

        return response()->json(['success' => true, 'message' => 'Item updated.', 'data' => $item]);
    }

    public function destroy($teamId, $taskId, $itemId): JsonResponse
    {
        TeamTask::where('team_id', $teamId)->findOrFail($taskId);
        $item = TaskChecklistItem::where('task_id', $taskId)->findOrFail($itemId);

        $item->delete();

        return response()->json(['success' => true, 'message' => 'Item deleted.']);
    }
}