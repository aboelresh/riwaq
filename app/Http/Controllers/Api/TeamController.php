<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Team\CreateTeamRequest;
use App\Http\Requests\Team\InviteByEmailRequest;
use App\Http\Requests\Team\InviteByUsernameRequest;
use App\Http\Requests\Team\JoinTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Mail\TeamInviteMail;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TeamSection;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TeamController extends Controller
{
    public function create(CreateTeamRequest $request): JsonResponse
    {
        $user = auth()->user();

        if (!$user->tracks()->where('track_id', $request->track_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be enrolled in this track first.',
            ], 400);
        }

        // Bug 009 Fix: wrap all 4 operations in a transaction
        $team = DB::transaction(function () use ($request, $user) {
            do {
                $code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
            } while (Team::where('code', $code)->exists());

            $team = Team::create([
                'name'         => $request->name,
                'description'  => $request->description,
                'type'         => $request->type,
                'code'         => $code,
                'project_type' => $request->project_type,
                'max_members'  => $request->max_members,
                'created_by'   => $user->id,
            ]);

            TeamMember::create([
                'team_id'  => $team->id,
                'user_id'  => $user->id,
                'track_id' => $request->track_id,
            ]);

            $generalSection = TeamSection::create([
                'team_id'    => $team->id,
                'name'       => 'General',
                'is_general' => true,
                'created_by' => $user->id,
            ]);
            $generalSection->members()->attach($user->id, ['role' => 'lead']);

            return $team;
        });

        $team->load('members.user', 'members.track', 'sections');

        return response()->json([
            'success' => true,
            'message' => 'Team created successfully',
            'data'    => $team,
        ], 201);
    }

    public function join(JoinTeamRequest $request): JsonResponse
    {
        $user = auth()->user();
        $team = Team::where('code', $request->code)->firstOrFail();

        if (!$user->tracks()->where('track_id', $request->track_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be enrolled in this track first.',
            ], 400);
        }

        if ($team->type === 'learning') {
            $creatorMember = TeamMember::where('team_id', $team->id)
                ->where('user_id', $team->created_by)->first();

            if ($creatorMember && (int) $creatorMember->track_id !== (int) $request->track_id) {
                $requiredTrack = \App\Models\Track::find($creatorMember->track_id);
                return response()->json([
                    'success' => false,
                    'message' => 'This is a learning team. All members must be on the same track: '
                        . ($requiredTrack->title ?? 'Unknown'),
                    'data'    => ['required_track_id' => $creatorMember->track_id],
                ], 400);
            }
        }

        if ($team->isFull()) {
            return response()->json(['success' => false, 'message' => 'Team is full.'], 400);
        }

        if (TeamMember::where('team_id', $team->id)->where('user_id', $user->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Already a member of this team.'], 400);
        }

        TeamMember::create([
            'team_id'   => $team->id,
            'user_id'   => $user->id,
            'track_id'  => $request->track_id,
            'joined_at' => now(),
        ]);

        $generalSection = $team->generalSection;
        if ($generalSection) {
            $generalSection->members()->syncWithoutDetaching([$user->id => ['role' => 'member']]);
        }

        NotificationService::notifyTeamExcept(
            $team->id, $user->id, 'member_joined',
            $user->name . ' joined the team',
            $user->name . ' has joined "' . $team->name . '"',
            ['sender_id' => $user->id, 'url' => '/teams/' . $team->id]
        );

        return response()->json(['success' => true, 'message' => 'Joined team successfully.']);
    }

    public function show($id): JsonResponse
    {
        $team = Team::with([
            'members.user',
            'members.track',
            'sections',
            'creator',
        ])->findOrFail($id);

        return response()->json(['success' => true, 'data' => $team]);
    }

    public function myTeams(): JsonResponse
    {
        $user   = auth()->user();
        $tracks = $user->tracks()
            ->withPivot('status', 'unlock_threshold', 'started_at', 'completed_at')
            ->with(['courses' => fn($q) => $q->with(['userProgress' => fn($q2) => $q2->where('user_id', $user->id)])])
            ->get();

        return response()->json(['success' => true, 'data' => $tracks]);
    }

    public function update(UpdateTeamRequest $request, $id): JsonResponse
    {
        $team = Team::findOrFail($id);
        $user = auth()->user();

        if ($team->created_by !== $user->id && !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Not authorized.'], 403);
        }

        if ($request->has('max_members')) {
            $currentCount = $team->members()->count();
            if ($request->max_members < $currentCount) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot set max below current member count ({$currentCount}).",
                ], 400);
            }
        }

        $team->update($request->only(['name', 'description', 'max_members']));
        $team->load('members.user', 'members.track', 'sections');

        return response()->json(['success' => true, 'message' => 'Team updated.', 'data' => $team]);
    }

    public function destroy($id): JsonResponse
    {
        $team = Team::findOrFail($id);
        $user = auth()->user();

        if ($team->created_by !== $user->id && !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Not authorized.'], 403);
        }

        $team->delete();

        return response()->json(['success' => true, 'message' => 'Team deleted.']);
    }

    public function leave($id): JsonResponse
    {
        $user   = auth()->user();
        $team   = Team::findOrFail($id);
        $member = TeamMember::where('team_id', $id)->where('user_id', $user->id)->first();

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'You are not a member of this team.'], 400);
        }

        if ($team->created_by === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Team leader cannot leave. Delete the team instead.',
            ], 400);
        }

        $member->delete();

        return response()->json(['success' => true, 'message' => 'Left team successfully.']);
    }

    public function kickMember($id, $userId): JsonResponse
    {
        $user   = auth()->user();
        $team   = Team::findOrFail($id);

        if ($team->created_by !== $user->id && !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Not authorized.'], 403);
        }

        if ((int) $userId === $team->created_by) {
            return response()->json(['success' => false, 'message' => 'Cannot kick the team leader.'], 400);
        }

        $member = TeamMember::where('team_id', $id)->where('user_id', $userId)->first();
        if (!$member) {
            return response()->json(['success' => false, 'message' => 'User is not a member.'], 400);
        }

        $member->delete();

        return response()->json(['success' => true, 'message' => 'Member removed.']);
    }

    public function inviteByUsername(InviteByUsernameRequest $request, $id): JsonResponse
    {
        $user = auth()->user();
        $team = Team::findOrFail($id);

        if ($team->created_by !== $user->id && !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Not authorized.'], 403);
        }

        $targetUser = \App\Models\User::where('username', $request->username)->first();

        if ($team->isFull()) {
            return response()->json(['success' => false, 'message' => 'Team is full.'], 400);
        }

        if (TeamMember::where('team_id', $id)->where('user_id', $targetUser->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'User is already a member.'], 400);
        }

        NotificationService::send(
            $targetUser->id, 'team_invite',
            'Invited to join "' . $team->name . '"',
            $user->name . ' invited you to join team "' . $team->name . '". Use code: ' . $team->code,
            ['team_id' => $id, 'team_code' => $team->code, 'url' => '/teams']
        );

        return response()->json([
            'success' => true,
            'message' => 'Invitation sent to @' . $targetUser->username,
            'data'    => ['user' => ['id' => $targetUser->id, 'name' => $targetUser->name, 'username' => $targetUser->username]],
        ]);
    }

    public function inviteByEmail(InviteByEmailRequest $request, $id): JsonResponse
    {
        $user = auth()->user();
        $team = Team::findOrFail($id);

        if ($team->created_by !== $user->id && !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Not authorized.'], 403);
        }

        try {
            Mail::to($request->email)->send(
                new TeamInviteMail($team->name, $team->code, $user->name, $team->type ?? 'learning')
            );
        } catch (\Exception $e) {
            Log::error('Failed to send team invite email', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to send invitation email.'], 500);
        }

        return response()->json(['success' => true, 'message' => 'Invitation email sent to ' . $request->email]);
    }

    public function searchUser(Request $request): JsonResponse
    {
        $query = $request->get('query', '');
        if (strlen($query) < 2) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $users = \App\Models\User::where('username', 'like', "%{$query}%")
            ->orWhere('name', 'like', "%{$query}%")
            ->select('id', 'name', 'username', 'profile_photo')
            ->limit(10)
            ->get();

        return response()->json(['success' => true, 'data' => $users]);
    }

    public function progress($id): JsonResponse
    {
        $team    = Team::with('members.user')->findOrFail($id);
        $members = $team->members->map(function ($member) {
            $user     = $member->user;
            $viewed   = $user->topicProgress()->where('is_viewed', true)->count();
            $passed   = $user->quizAttempts()->where('passed', true)->count();

            return [
                'user'           => ['id' => $user->id, 'name' => $user->name],
                'topics_viewed'  => $viewed,
                'quizzes_passed' => $passed,
            ];
        });

        return response()->json(['success' => true, 'data' => $members]);
    }

    public function activity($id): JsonResponse
    {
        $team = Team::with('members.user')->findOrFail($id);

        $activities = collect();

        foreach ($team->members as $member) {
            $user = $member->user;

            $user->topicProgress()
                ->where('is_viewed', true)
                ->with('topic')
                ->latest('updated_at')
                ->limit(5)
                ->get()
                ->each(fn($p) => $activities->push([
                    'type'       => 'topic_viewed',
                    'user'       => ['id' => $user->id, 'name' => $user->name],
                    'topic'      => $p->topic?->title,
                    'created_at' => $p->updated_at,
                ]));

            $user->quizAttempts()
                ->where('passed', true)
                ->with('quiz')
                ->latest()
                ->limit(5)
                ->get()
                ->each(fn($a) => $activities->push([
                    'type'       => 'quiz_passed',
                    'user'       => ['id' => $user->id, 'name' => $user->name],
                    'quiz'       => $a->quiz?->title,
                    'score'      => $a->score,
                    'created_at' => $a->created_at,
                ]));
        }

        return response()->json([
            'success' => true,
            'data'    => $activities->sortByDesc('created_at')->values()->take(30),
        ]);
    }

    public function achievements($id, $userId): JsonResponse
    {
        $member = TeamMember::where('team_id', $id)->where('user_id', $userId)->firstOrFail();
        $user   = $member->user;

        $achievements = [
            'topics_viewed'  => $user->topicProgress()->where('is_viewed', true)->count(),
            'quizzes_passed' => $user->quizAttempts()->where('passed', true)->count(),
            'total_score'    => $user->quizAttempts()->where('passed', true)->sum('score'),
        ];

        return response()->json(['success' => true, 'data' => $achievements]);
    }
}