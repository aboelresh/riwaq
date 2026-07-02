<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamSection;
use App\Models\TeamMember;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeamSectionController extends Controller
{
    /**
     * GET /teams/{teamId}/sections
     */
    public function index($teamId)
    {
        $team = Team::findOrFail($teamId);
        $this->ensureMember($team);

        $sections = TeamSection::where('team_id', $teamId)
            ->with('members:id,name,email,profile_photo')
            ->withCount('tasks')
            ->orderBy('is_general', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json(['success' => true, 'data' => $sections]);
    }

    /**
     * POST /teams/{teamId}/sections
     */
    public function store(Request $request, $teamId)
    {
        $team = Team::findOrFail($teamId);
        $this->ensureLeader($team);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $section = TeamSection::create([
            'team_id' => $teamId,
            'name' => $request->name,
            'description' => $request->description,
            'is_general' => false,
            'color' => $request->color,
            'icon' => $request->icon,
            'created_by' => auth()->id(),
        ]);

        $section->load('members:id,name,email,profile_photo');

        return response()->json(['success' => true, 'message' => 'Section created', 'data' => $section], 201);
    }

    /**
     * PUT /teams/{teamId}/sections/{sectionId}
     */
    public function update(Request $request, $teamId, $sectionId)
    {
        $team = Team::findOrFail($teamId);
        $this->ensureLeader($team);

        $section = TeamSection::where('team_id', $teamId)->findOrFail($sectionId);

        // Can't rename general section
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:100',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $fields = $request->only(['description', 'color', 'icon']);
        if (!$section->is_general && $request->has('name')) {
            $fields['name'] = $request->name;
        }

        $section->update($fields);
        $section->load('members:id,name,email,profile_photo');

        return response()->json(['success' => true, 'data' => $section]);
    }

    /**
     * DELETE /teams/{teamId}/sections/{sectionId}
     */
    public function destroy($teamId, $sectionId)
    {
        $team = Team::findOrFail($teamId);
        $this->ensureLeader($team);

        $section = TeamSection::where('team_id', $teamId)->findOrFail($sectionId);

        if ($section->is_general) {
            return response()->json(['success' => false, 'message' => 'Cannot delete the general section'], 400);
        }

        // Move tasks to general section before deleting
        $generalSection = $team->generalSection;
        if ($generalSection) {
            $section->tasks()->update(['section_id' => $generalSection->id]);
        }

        $section->delete();

        return response()->json(['success' => true, 'message' => 'Section deleted']);
    }

    /**
     * POST /teams/{teamId}/sections/{sectionId}/members
     * Body: { user_ids: [1, 2, 3], role: "member" }
     */
    public function addMembers(Request $request, $teamId, $sectionId)
    {
        $team = Team::findOrFail($teamId);
        $this->ensureLeader($team);

        $section = TeamSection::where('team_id', $teamId)->findOrFail($sectionId);

        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
            'role' => 'nullable|string|in:lead,member',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $role = $request->role ?? 'member';
        $syncData = [];
        foreach ($request->user_ids as $uid) {
            // Verify user is a team member
            $isMember = TeamMember::where('team_id', $teamId)->where('user_id', $uid)->exists();
            if ($isMember) {
                $syncData[$uid] = ['role' => $role];
            }
        }

        $section->members()->syncWithoutDetaching($syncData);

        // Notify added members
        $user = auth()->user();
        $notifyIds = array_filter(array_keys($syncData), fn($id) => $id !== $user->id);
        if (!empty($notifyIds)) {
            NotificationService::sendToMany(
                $notifyIds, 'section_added',
                'Added to "' . $section->name . '"',
                $user->name . ' added you to section "' . $section->name . '" in "' . $team->name . '"',
                ['team_id' => $teamId, 'section_id' => $sectionId, 'url' => '/teams/' . $teamId]
            );
        }

        $section->load('members:id,name,email,profile_photo');
        return response()->json(['success' => true, 'data' => $section]);
    }

    /**
     * DELETE /teams/{teamId}/sections/{sectionId}/members/{userId}
     */
    public function removeMember($teamId, $sectionId, $userId)
    {
        $team = Team::findOrFail($teamId);
        $this->ensureLeader($team);

        $section = TeamSection::where('team_id', $teamId)->findOrFail($sectionId);

        if ($section->is_general) {
            return response()->json(['success' => false, 'message' => 'Cannot remove from general section'], 400);
        }

        $section->members()->detach($userId);

        return response()->json(['success' => true, 'message' => 'Member removed from section']);
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
            abort(403, 'Only the team leader can manage sections');
        }
    }
}
