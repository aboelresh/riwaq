<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Team\AddSectionMembersRequest;
use App\Http\Requests\Team\StoreSectionRequest;
use App\Http\Requests\Team\UpdateSectionRequest;
use App\Models\Team;
use App\Models\TeamSection;
use Illuminate\Http\JsonResponse;

class TeamSectionController extends Controller
{
    private function ensureMember(Team $team): void
    {
        $user = auth()->user();
        if ($user->isAdmin()) return;
        if (!$team->members()->where('user_id', $user->id)->exists()) {
            abort(403, 'Not a team member.');
        }
    }

    private function ensureLeader(Team $team): void
    {
        $user = auth()->user();
        if ($user->isAdmin()) return;
        if ($team->created_by !== $user->id) {
            abort(403, 'Only the team leader can perform this action.');
        }
    }

    public function index($teamId): JsonResponse
    {
        $team = Team::findOrFail($teamId);
        $this->ensureMember($team);

        $sections = $team->sections()->withCount('tasks')->get();

        return response()->json(['success' => true, 'data' => $sections]);
    }

    public function store(StoreSectionRequest $request, $teamId): JsonResponse
    {
        $team = Team::findOrFail($teamId);
        $this->ensureLeader($team);

        $section = TeamSection::create([
            'team_id'    => $teamId,
            'name'       => $request->name,
            'description'=> $request->description,
            'color'      => $request->color,
            'icon'       => $request->icon,
            'is_general' => false,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Section created.',
            'data'    => $section,
        ], 201);
    }

    public function update(UpdateSectionRequest $request, $teamId, $sectionId): JsonResponse
    {
        $team    = Team::findOrFail($teamId);
        $section = TeamSection::where('team_id', $teamId)->findOrFail($sectionId);
        $this->ensureLeader($team);

        $fields = [];
        if (!$section->is_general && $request->has('name')) {
            $fields['name'] = $request->name;
        }
        if ($request->has('description')) $fields['description'] = $request->description;
        if ($request->has('color'))       $fields['color']       = $request->color;
        if ($request->has('icon'))        $fields['icon']        = $request->icon;

        $section->update($fields);

        return response()->json(['success' => true, 'message' => 'Section updated.', 'data' => $section]);
    }

    public function destroy($teamId, $sectionId): JsonResponse
    {
        $team    = Team::findOrFail($teamId);
        $section = TeamSection::where('team_id', $teamId)->findOrFail($sectionId);
        $this->ensureLeader($team);

        if ($section->is_general) {
            return response()->json(['success' => false, 'message' => 'Cannot delete the general section.'], 400);
        }

        $generalSection = $team->sections()->where('is_general', true)->first();
        if ($generalSection) {
            $section->tasks()->update(['section_id' => $generalSection->id]);
        }

        $section->delete();

        return response()->json(['success' => true, 'message' => 'Section deleted. Tasks moved to General.']);
    }

    public function addMembers(AddSectionMembersRequest $request, $teamId, $sectionId): JsonResponse
    {
        $team    = Team::findOrFail($teamId);
        $section = TeamSection::where('team_id', $teamId)->findOrFail($sectionId);
        $this->ensureLeader($team);

        $teamMemberIds = $team->members()->pluck('user_id')->toArray();
        $validUserIds  = array_intersect($request->user_ids, $teamMemberIds);

        foreach ($validUserIds as $userId) {
            $section->members()->syncWithoutDetaching([
                $userId => ['role' => $request->role ?? 'member'],
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Members added to section.']);
    }

    public function removeMember($teamId, $sectionId, $userId): JsonResponse
    {
        $team    = Team::findOrFail($teamId);
        $section = TeamSection::where('team_id', $teamId)->findOrFail($sectionId);
        $this->ensureLeader($team);

        $section->members()->detach($userId);

        return response()->json(['success' => true, 'message' => 'Member removed from section.']);
    }
}