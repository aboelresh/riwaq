<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TeamNote;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeamNoteController extends Controller
{
    /**
     * GET /teams/{teamId}/notes/{userId}
     * Get all notes about a specific member.
     * - Leader/admin sees all notes (private + shared)
     * - The member themselves sees only shared notes about them
     */
    public function index($teamId, $userId)
    {
        $team = Team::findOrFail($teamId);
        $this->ensureMember($team);

        $user = auth()->user();
        $isLeader = $team->created_by === $user->id || $user->role === 'admin';

        $query = TeamNote::where('team_id', $teamId)
            ->where('user_id', $userId)
            ->with('author:id,name,email,profile_photo')
            ->orderBy('created_at', 'desc');

        // Non-leaders only see shared (non-private) notes about themselves
        if (!$isLeader) {
            $query->where('is_private', false);
        }

        return response()->json(['success' => true, 'data' => $query->get()]);
    }

    /**
     * GET /teams/{teamId}/notes
     * Get ALL notes in this team (leader only) — overview.
     */
    public function all($teamId)
    {
        $team = Team::findOrFail($teamId);
        $this->ensureLeader($team);

        $notes = TeamNote::where('team_id', $teamId)
            ->with([
                'author:id,name,profile_photo',
                'user:id,name,profile_photo',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $notes]);
    }

    /**
     * POST /teams/{teamId}/notes
     * Body: { user_id, content, is_private }
     * Only leader/admin can create notes.
     */
    public function store(Request $request, $teamId)
    {
        $team = Team::findOrFail($teamId);
        $this->ensureLeader($team);

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'content' => 'required|string|max:2000',
            'is_private' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Verify target user is a team member
        $isMember = TeamMember::where('team_id', $teamId)
            ->where('user_id', $request->user_id)
            ->exists();

        if (!$isMember) {
            return response()->json(['success' => false, 'message' => 'User is not a team member'], 400);
        }

        $note = TeamNote::create([
            'team_id' => $teamId,
            'user_id' => $request->user_id,
            'author_id' => auth()->id(),
            'content' => $request->content,
            'is_private' => $request->is_private ?? false,
        ]);

        $note->load('author:id,name,email,profile_photo');

        // Notify the member (only for shared notes)
        if (!$note->is_private && $request->user_id !== auth()->id()) {
            NotificationService::send(
                $request->user_id,
                'note_received',
                auth()->user()->name . ' ' . ($request->is_private ? '' : 'left you a note'),
                mb_substr($request->content, 0, 80),
                ['team_id' => $teamId, 'url' => '/teams/' . $teamId]
            );
        }

        return response()->json(['success' => true, 'message' => 'Note added', 'data' => $note], 201);
    }

    /**
     * PUT /teams/{teamId}/notes/{noteId}
     * Update a note (leader/admin who wrote it, or any leader).
     */
    public function update(Request $request, $teamId, $noteId)
    {
        $team = Team::findOrFail($teamId);
        $this->ensureLeader($team);

        $note = TeamNote::where('team_id', $teamId)->findOrFail($noteId);

        $validator = Validator::make($request->all(), [
            'content' => 'sometimes|string|max:2000',
            'is_private' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $note->update($request->only(['content', 'is_private']));
        $note->load('author:id,name,email,profile_photo');

        return response()->json(['success' => true, 'data' => $note]);
    }

    /**
     * DELETE /teams/{teamId}/notes/{noteId}
     */
    public function destroy($teamId, $noteId)
    {
        $team = Team::findOrFail($teamId);
        $this->ensureLeader($team);

        $note = TeamNote::where('team_id', $teamId)->findOrFail($noteId);
        $note->delete();

        return response()->json(['success' => true, 'message' => 'Note deleted']);
    }

    private function ensureMember(Team $team)
    {
        if (auth()->user()->role === 'admin') return;
        $isMember = TeamMember::where('team_id', $team->id)->where('user_id', auth()->id())->exists();
        if (!$isMember) abort(403, 'Not a team member');
    }

    private function ensureLeader(Team $team)
    {
        $user = auth()->user();
        if ($user->role === 'admin') return;
        if ($team->created_by !== $user->id) {
            abort(403, 'Only the team leader can manage notes');
        }
    }
}
