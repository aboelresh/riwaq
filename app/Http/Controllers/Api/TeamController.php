<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TeamSection;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{
 public function create(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'type' => 'required|in:learning,project',
        'project_type' => 'required|in:web,mobile,data,game',
        'max_members' => 'required|integer|min:2|max:10',
        'track_id' => 'required|integer|exists:tracks,id',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    $user = auth()->user();

    try {

        $isEnrolled = $user->tracks()->where('track_id', $request->track_id)->exists();

        if (!$isEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'You must be enrolled in this track first'
            ], 400);
        }


        do {
            $code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        } while (Team::where('code', $code)->exists());


        $team = Team::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'code' => $code,
            'project_type' => $request->project_type,
            'max_members' => $request->max_members,
            'created_by' => $user->id,
        ]);

        
        TeamMember::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'track_id' => $request->track_id,
        ]);

        // Auto-create general section + add creator
        $generalSection = TeamSection::create([
            'team_id' => $team->id,
            'name' => 'General',
            'is_general' => true,
            'created_by' => $user->id,
        ]);
        $generalSection->members()->attach($user->id, ['role' => 'lead']);

        $team->load('members.user', 'members.track', 'sections');

        return response()->json([
            'success' => true,
            'message' => 'Team created successfully',
            'data' => $team
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to create team: ' . $e->getMessage()
        ], 500);
    }
}

    public function join(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|exists:teams,code',
            'track_id' => 'required|exists:tracks,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        $team = Team::where('code', $request->code)->firstOrFail();

        // Check enrollment
        $isEnrolled = $user->tracks()->where('track_id', $request->track_id)->exists();
        if (!$isEnrolled) {
            return response()->json(['success' => false, 'message' => 'You must be enrolled in this track first'], 400);
        }

        // Learning teams: members must join with the SAME track as the team creator
        if ($team->type === 'learning') {
            $creatorMember = TeamMember::where('team_id', $team->id)->where('user_id', $team->created_by)->first();
            if ($creatorMember && (int) $creatorMember->track_id !== (int) $request->track_id) {
                $requiredTrack = \App\Models\Track::find($creatorMember->track_id);
                return response()->json([
                    'success' => false,
                    'message' => 'This is a learning team. All members must be on the same track: ' . ($requiredTrack->title ?? 'Unknown'),
                    'data' => ['required_track_id' => $creatorMember->track_id]
                ], 400);
            }
        }
        // Project teams: any track is fine, just need enrollment

        if ($team->isFull()) {
            return response()->json([
                'success' => false,
                'message' => 'Team is full'
            ], 400);
        }

        $existing = TeamMember::where('team_id', $team->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Already a member of this team'
            ], 400);
        }

        TeamMember::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'track_id' => $request->track_id,
            'joined_at' => now(),
        ]);

        // Auto-add to general section
        $generalSection = $team->generalSection;
        if ($generalSection) {
            $generalSection->members()->syncWithoutDetaching([$user->id => ['role' => 'member']]);
        }

        // Notify team members that someone joined
        NotificationService::notifyTeamExcept(
            $team->id,
            $user->id,
            'member_joined',
            $user->name . ' joined the team',
            $user->name . ' has joined "' . $team->name . '"',
            ['sender_id' => $user->id, 'url' => '/teams/' . $team->id]
        );

        return response()->json([
            'success' => true,
            'message' => 'Joined team successfully'
        ]);
    }

    public function show($id)
    {
        $team = Team::with([
            'members.user:id,name,email,profile_photo',
            'members.track:id,title',
            'sections.members:id,name,profile_photo',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $team
        ]);
    }

    public function myTeams()
    {
        $user = auth()->user();
        
        $teams = $user->teams()
            ->with([
                'members.user:id,name,email,profile_photo',
                'members.track:id,title'
            ])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $teams
        ]);
    }

    public function progress($id)
    {
        $team = Team::with([
            'members.user' => function($query) {
                $query->with([
                    'courseProgress',
                    'topicProgress',
                    'quizAttempts'
                ]);
            }
        ])->findOrFail($id);

        $membersProgress = $team->members->map(function($member) {
            $user = $member->user;
            
            return [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'track' => $member->track,
                'stats' => [
                    'courses_completed' => $user->courseProgress()->where('is_completed', true)->count(),
                    'topics_viewed' => $user->topicProgress()->where('is_viewed', true)->count(),
                    'quizzes_passed' => $user->quizAttempts()->where('passed', true)->count(),
                    'total_score' => $user->quizAttempts()->where('passed', true)->sum('score'),
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'team' => $team,
                'members_progress' => $membersProgress
            ]
        ]);
    }

    /**
     * PUT /teams/{id} — Edit team (leader/admin only)
     */
    public function update(Request $request, $id)
    {
        $team = Team::findOrFail($id);
        $user = auth()->user();

        if ($team->created_by !== $user->id && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Not authorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'max_members' => 'sometimes|integer|min:2|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Don't allow shrinking below current member count
        if ($request->has('max_members')) {
            $currentCount = $team->members()->count();
            if ($request->max_members < $currentCount) {
                return response()->json(['success' => false, 'message' => 'Cannot set max below current member count (' . $currentCount . ')'], 400);
            }
        }

        $team->update($request->only(['name', 'description', 'max_members']));
        $team->load('members.user', 'members.track', 'sections');

        return response()->json(['success' => true, 'message' => 'Team updated', 'data' => $team]);
    }

    /**
     * DELETE /teams/{id} — Delete team (leader/admin only)
     */
    public function destroy($id)
    {
        $team = Team::findOrFail($id);
        $user = auth()->user();

        if ($team->created_by !== $user->id && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Not authorized'], 403);
        }

        // Notify members before deletion
        NotificationService::notifyTeamExcept(
            $team->id, $user->id, 'team_deleted',
            'Team "' . $team->name . '" was deleted',
            $user->name . ' deleted the team "' . $team->name . '"',
            []
        );

        $team->delete(); // cascade deletes members, sections, tasks, notes

        return response()->json(['success' => true, 'message' => 'Team deleted']);
    }

    /**
     * POST /teams/{id}/leave — Leave team (member removes self)
     */
    public function leave($id)
    {
        $team = Team::findOrFail($id);
        $user = auth()->user();

        // Creator can't leave (must delete instead)
        if ($team->created_by === $user->id) {
            return response()->json(['success' => false, 'message' => 'Team leader cannot leave. Delete the team instead.'], 400);
        }

        $member = TeamMember::where('team_id', $id)->where('user_id', $user->id)->first();
        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Not a member'], 400);
        }

        // Remove from all sections
        \DB::table('team_section_members')
            ->whereIn('section_id', TeamSection::where('team_id', $id)->pluck('id'))
            ->where('user_id', $user->id)
            ->delete();

        // Remove task assignments
        \DB::table('team_task_assignees')
            ->whereIn('task_id', \App\Models\TeamTask::where('team_id', $id)->pluck('id'))
            ->where('user_id', $user->id)
            ->delete();

        $member->delete();

        // Notify team
        NotificationService::notifyTeamExcept(
            $id, $user->id, 'member_left',
            $user->name . ' left the team',
            $user->name . ' has left "' . $team->name . '"',
            ['sender_id' => $user->id, 'url' => '/teams/' . $id]
        );

        return response()->json(['success' => true, 'message' => 'Left team successfully']);
    }

    /**
     * DELETE /teams/{id}/members/{userId} — Kick member (leader/admin only)
     */
    public function kickMember($id, $userId)
    {
        $team = Team::findOrFail($id);
        $user = auth()->user();

        if ($team->created_by !== $user->id && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Not authorized'], 403);
        }

        // Can't kick self or the creator
        if ((int) $userId === $team->created_by) {
            return response()->json(['success' => false, 'message' => 'Cannot kick the team leader'], 400);
        }

        $member = TeamMember::where('team_id', $id)->where('user_id', $userId)->first();
        if (!$member) {
            return response()->json(['success' => false, 'message' => 'User is not a member'], 400);
        }

        // Remove from sections
        \DB::table('team_section_members')
            ->whereIn('section_id', TeamSection::where('team_id', $id)->pluck('id'))
            ->where('user_id', $userId)
            ->delete();

        // Remove task assignments
        \DB::table('team_task_assignees')
            ->whereIn('task_id', \App\Models\TeamTask::where('team_id', $id)->pluck('id'))
            ->where('user_id', $userId)
            ->delete();

        $member->delete();

        // Notify kicked user
        NotificationService::send(
            $userId, 'member_kicked',
            'You were removed from "' . $team->name . '"',
            $user->name . ' removed you from the team',
            ['team_id' => $id]
        );

        return response()->json(['success' => true, 'message' => 'Member removed']);
    }

    /**
     * POST /teams/{id}/invite — Invite by username
     */
    public function inviteByUsername(Request $request, $id)
    {
        $team = Team::findOrFail($id);
        $user = auth()->user();

        if ($team->created_by !== $user->id && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Not authorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|exists:users,username',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $targetUser = \App\Models\User::where('username', $request->username)->first();

        if ($team->isFull()) {
            return response()->json(['success' => false, 'message' => 'Team is full'], 400);
        }

        $alreadyMember = TeamMember::where('team_id', $id)->where('user_id', $targetUser->id)->exists();
        if ($alreadyMember) {
            return response()->json(['success' => false, 'message' => 'User is already a member'], 400);
        }

        // Send invitation notification (user must accept separately or auto-add)
        NotificationService::send(
            $targetUser->id, 'team_invite',
            'Invited to join "' . $team->name . '"',
            $user->name . ' invited you to join team "' . $team->name . '". Use code: ' . $team->code,
            ['team_id' => $id, 'team_code' => $team->code, 'url' => '/teams']
        );

        return response()->json([
            'success' => true,
            'message' => 'Invitation sent to @' . $targetUser->username,
            'data' => ['user' => ['id' => $targetUser->id, 'name' => $targetUser->name, 'username' => $targetUser->username]]
        ]);
    }

    /**
     * POST /teams/{id}/invite-email — Invite by email (sends invitation email)
     */
    public function inviteByEmail(Request $request, $id)
    {
        $team = Team::findOrFail($id);
        $user = auth()->user();

        if ($team->created_by !== $user->id && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Not authorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            \Illuminate\Support\Facades\Mail::to($request->email)->send(
                new \App\Mail\TeamInviteMail($team->name, $team->code, $user->name, $team->type ?? 'learning')
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send team invite email: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send email'], 500);
        }

        return response()->json(['success' => true, 'message' => 'Invitation email sent to ' . $request->email]);
    }

    /**
     * GET /teams/search-user?username=xxx — Search user by username for invite
     */
    public function searchUser(Request $request)
    {
        $q = $request->query('username', '');
        if (strlen($q) < 2) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $users = \App\Models\User::where('username', 'like', '%' . $q . '%')
            ->where('id', '!=', auth()->id())
            ->select('id', 'name', 'username', 'profile_photo')
            ->limit(10)
            ->get();

        return response()->json(['success' => true, 'data' => $users]);
    }

    /**
     * GET /teams/{id}/activity — Recent team activity feed
     * Aggregates: tasks created/completed, member joins, notes, quiz attempts
     */
    public function activity($id)
    {
        $team = Team::findOrFail($id);

        $memberIds = $team->members()->pluck('user_id')->toArray();
        $activities = collect();

        // Recent tasks (created/done)
        $tasks = \App\Models\TeamTask::where('team_id', $id)
            ->with('creator:id,name,profile_photo')
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();

        foreach ($tasks as $task) {
            $activities->push([
                'type' => $task->status === 'done' ? 'task_completed' : 'task_created',
                'user' => $task->creator,
                'title' => $task->title,
                'meta' => ['status' => $task->status, 'priority' => $task->priority],
                'at' => $task->status === 'done' ? $task->updated_at : $task->created_at,
            ]);
        }

        // Member joins
        $joins = TeamMember::where('team_id', $id)
            ->with('user:id,name,profile_photo')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        foreach ($joins as $join) {
            $activities->push([
                'type' => 'member_joined',
                'user' => $join->user,
                'title' => ($join->user->name ?? 'Unknown') . ' joined',
                'meta' => [],
                'at' => $join->joined_at ?? $join->created_at,
            ]);
        }

        // Recent notes (non-private only)
        $notes = \App\Models\TeamNote::where('team_id', $id)
            ->where('is_private', false)
            ->with('author:id,name,profile_photo', 'user:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        foreach ($notes as $note) {
            $activities->push([
                'type' => 'note_added',
                'user' => $note->author,
                'title' => 'Note about ' . ($note->user->name ?? 'member'),
                'meta' => ['preview' => mb_substr($note->content, 0, 60)],
                'at' => $note->created_at,
            ]);
        }

        // Recent quiz attempts by team members
        $quizAttempts = \DB::table('user_quiz_attempts')
            ->whereIn('user_id', $memberIds)
            ->join('users', 'users.id', '=', 'user_quiz_attempts.user_id')
            ->join('quizzes', 'quizzes.id', '=', 'user_quiz_attempts.quiz_id')
            ->select('users.id as user_id', 'users.name', 'users.profile_photo', 'quizzes.title as quiz_title', 'user_quiz_attempts.score', 'user_quiz_attempts.passed', 'user_quiz_attempts.created_at')
            ->orderBy('user_quiz_attempts.created_at', 'desc')
            ->limit(10)
            ->get();

        foreach ($quizAttempts as $attempt) {
            $activities->push([
                'type' => $attempt->passed ? 'quiz_passed' : 'quiz_failed',
                'user' => ['id' => $attempt->user_id, 'name' => $attempt->name, 'profile_photo' => $attempt->profile_photo],
                'title' => $attempt->quiz_title,
                'meta' => ['score' => $attempt->score, 'passed' => $attempt->passed],
                'at' => $attempt->created_at,
            ]);
        }

        // Sort by date desc, take top 30
        $sorted = $activities->sortByDesc('at')->take(30)->values();

        return response()->json(['success' => true, 'data' => $sorted]);
    }

    /**
     * GET /teams/{id}/achievements/{userId} — Calculate badges for a team member
     */
    public function achievements($id, $userId)
    {
        $team = Team::findOrFail($id);
        $user = \App\Models\User::findOrFail($userId);

        $coursesDone = $user->courseProgress()->where('is_completed', true)->count();
        $topicsViewed = $user->topicProgress()->where('is_viewed', true)->count();
        $quizzesPassed = $user->quizAttempts()->where('passed', true)->count();
        $totalXP = $user->quizAttempts()->where('passed', true)->sum('score');
        $tasksCompleted = \DB::table('team_task_assignees')
            ->join('team_tasks', 'team_tasks.id', '=', 'team_task_assignees.task_id')
            ->where('team_tasks.team_id', $id)
            ->where('team_tasks.status', 'done')
            ->where('team_task_assignees.user_id', $userId)
            ->count();
        $notesReceived = \App\Models\TeamNote::where('team_id', $id)->where('user_id', $userId)->where('is_private', false)->count();

        // Check if top XP in team
        $allMembers = $team->members()->pluck('user_id')->toArray();
        $memberXPs = \DB::table('user_quiz_attempts')
            ->whereIn('user_id', $allMembers)
            ->where('passed', true)
            ->selectRaw('user_id, SUM(score) as total')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->get();
        $isTopXP = $memberXPs->first()?->user_id === (int)$userId;
        $isCreator = $team->created_by === (int)$userId;

        $badges = [];

        // Define badges
        $defs = [
            ['key' => 'first_step', 'name' => 'First Step', 'nameAr' => 'الخطوة الأولى', 'desc' => 'Viewed first topic', 'descAr' => 'شاهد أول درس', 'icon' => 'rocket', 'color' => 'blue', 'earned' => $topicsViewed >= 1],
            ['key' => 'scholar', 'name' => 'Scholar', 'nameAr' => 'متعلم', 'desc' => 'Viewed 10 topics', 'descAr' => 'شاهد 10 دروس', 'icon' => 'book', 'color' => 'cyan', 'earned' => $topicsViewed >= 10],
            ['key' => 'quiz_ace', 'name' => 'Quiz Ace', 'nameAr' => 'بطل الاختبارات', 'desc' => 'Passed first quiz', 'descAr' => 'نجح في أول اختبار', 'icon' => 'brain', 'color' => 'green', 'earned' => $quizzesPassed >= 1],
            ['key' => 'quiz_master', 'name' => 'Quiz Master', 'nameAr' => 'سيد الاختبارات', 'desc' => 'Passed 10 quizzes', 'descAr' => 'نجح في 10 اختبارات', 'icon' => 'trophy', 'color' => 'orange', 'earned' => $quizzesPassed >= 10],
            ['key' => 'course_done', 'name' => 'Completer', 'nameAr' => 'منجز', 'desc' => 'Completed a course', 'descAr' => 'أكمل كورس كامل', 'icon' => 'check', 'color' => 'green', 'earned' => $coursesDone >= 1],
            ['key' => 'xp_hunter', 'name' => 'XP Hunter', 'nameAr' => 'صائد النقاط', 'desc' => 'Earned 500+ XP', 'descAr' => 'حصل على 500+ نقطة', 'icon' => 'zap', 'color' => 'orange', 'earned' => $totalXP >= 500],
            ['key' => 'xp_legend', 'name' => 'XP Legend', 'nameAr' => 'أسطورة النقاط', 'desc' => 'Earned 2000+ XP', 'descAr' => 'حصل على 2000+ نقطة', 'icon' => 'star', 'color' => 'purple', 'earned' => $totalXP >= 2000],
            ['key' => 'team_player', 'name' => 'Team Player', 'nameAr' => 'لاعب فريق', 'desc' => 'Completed 5 team tasks', 'descAr' => 'أنجز 5 مهام فريق', 'icon' => 'users', 'color' => 'blue', 'earned' => $tasksCompleted >= 5],
            ['key' => 'mvp', 'name' => 'MVP', 'nameAr' => 'الأفضل', 'desc' => 'Top XP in team', 'descAr' => 'أعلى نقاط في الفريق', 'icon' => 'crown', 'color' => 'orange', 'earned' => $isTopXP && $totalXP > 0],
            ['key' => 'leader', 'name' => 'Captain', 'nameAr' => 'القائد', 'desc' => 'Team creator', 'descAr' => 'منشئ الفريق', 'icon' => 'shield', 'color' => 'purple', 'earned' => $isCreator],
            ['key' => 'noticed', 'name' => 'Noticed', 'nameAr' => 'ملحوظ', 'desc' => 'Received 3+ notes', 'descAr' => 'حصل على 3+ ملاحظات', 'icon' => 'note', 'color' => 'pink', 'earned' => $notesReceived >= 3],
        ];

        foreach ($defs as $d) {
            $badges[] = $d;
        }

        return response()->json(['success' => true, 'data' => $badges]);
    }
}