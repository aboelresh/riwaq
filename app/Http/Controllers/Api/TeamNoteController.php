<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Team\StoreNoteRequest;
use App\Http\Requests\Team\UpdateNoteRequest;
use App\Models\Team;
use App\Models\TeamNote;
use Illuminate\Http\JsonResponse;

class TeamNoteController extends Controller
{
    private function ensureLeader(Team $team): void
    {
        $user = auth()->user();
        if ($user->isAdmin()) return;
        if ($team->created_by !== $user->id) {
            abort(403, 'Only the team leader can manage notes.');
        }
    }

    public function all($teamId): JsonResponse
    {
        $team = Team::findOrFail($teamId);
        $user = auth()->user();

        $notes = TeamNote::where('team_id', $teamId)
            ->when(!$user->isAdmin() && $team->created_by !== $user->id, fn($q) =>
                $q->where('user_id', $user->id)->where('is_private', false)
            )
            ->with('user')
            ->get();

        return response()->json(['success' => true, 'data' => $notes]);
    }

    public function index($teamId, $userId): JsonResponse
    {
        $team = Team::findOrFail($teamId);
        $user = auth()->user();

        $notes = TeamNote::where('team_id', $teamId)
            ->where('user_id', $userId)
            ->when(!$user->isAdmin() && $team->created_by !== $user->id, fn($q) =>
                $q->where('is_private', false)
            )
            ->with('user')
            ->get();

        return response()->json(['success' => true, 'data' => $notes]);
    }

    public function store(StoreNoteRequest $request, $teamId): JsonResponse
    {
        $team = Team::findOrFail($teamId);
        $this->ensureLeader($team);

        $note = TeamNote::create([
            'team_id'    => $teamId,
            'user_id'    => $request->user_id,
            'content'    => $request->content,
            'is_private' => $request->is_private,
            'author_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Note created.',
            'data'    => $note->load('user'),
        ], 201);
    }

    public function update(UpdateNoteRequest $request, $teamId, $noteId): JsonResponse
    {
        $team = Team::findOrFail($teamId);
        $note = TeamNote::where('team_id', $teamId)->findOrFail($noteId);
        $this->ensureLeader($team);

        $note->update($request->validated());

        return response()->json(['success' => true, 'message' => 'Note updated.', 'data' => $note]);
    }

    public function destroy($teamId, $noteId): JsonResponse
    {
        $team = Team::findOrFail($teamId);
        $note = TeamNote::where('team_id', $teamId)->findOrFail($noteId);
        $this->ensureLeader($team);

        $note->delete();

        return response()->json(['success' => true, 'message' => 'Note deleted.']);
    }
}