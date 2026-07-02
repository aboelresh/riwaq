<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamChallenge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class TeamChallengeController extends Controller
{
    /**
     * GET /teams/{teamId}/challenges — current week + past challenges
     */
    public function index($teamId)
    {
        $team = Team::findOrFail($teamId);

        $current = TeamChallenge::where('team_id', $teamId)
            ->currentWeek()
            ->with('creator:id,name,profile_photo')
            ->orderBy('created_at', 'desc')
            ->get();

        $past = TeamChallenge::where('team_id', $teamId)
            ->where('week_end', '<', now()->toDateString())
            ->with('creator:id,name,profile_photo')
            ->orderBy('week_start', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'current' => $current,
                'past' => $past,
            ]
        ]);
    }

    /**
     * POST /teams/{teamId}/challenges — leader creates challenge
     */
    public function store(Request $request, $teamId)
    {
        $team = Team::findOrFail($teamId);
        $user = auth()->user();

        if ($team->created_by !== $user->id && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Not authorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'target_type' => 'required|in:topics_viewed,quizzes_passed,xp_earned,tasks_completed',
            'target_value' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Auto-calculate current week boundaries (Monday–Sunday)
        $now = Carbon::now();
        $weekStart = $now->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
        $weekEnd = $now->copy()->endOfWeek(Carbon::SUNDAY)->toDateString();

        $challenge = TeamChallenge::create([
            'team_id' => $teamId,
            'title' => $request->title,
            'description' => $request->description,
            'target_type' => $request->target_type,
            'target_value' => $request->target_value,
            'current_value' => 0,
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
            'is_completed' => false,
            'created_by' => $user->id,
        ]);

        $challenge->load('creator:id,name,profile_photo');

        return response()->json([
            'success' => true,
            'message' => 'Challenge created',
            'data' => $challenge,
        ], 201);
    }

    /**
     * GET /teams/{teamId}/challenges/progress — recalculate current_value from real data
     */
    public function progress($teamId)
    {
        $team = Team::findOrFail($teamId);
        $memberIds = $team->members()->pluck('user_id')->toArray();

        $challenges = TeamChallenge::where('team_id', $teamId)
            ->currentWeek()
            ->get();

        foreach ($challenges as $challenge) {
            $weekStart = $challenge->week_start->startOfDay();
            $weekEnd = $challenge->week_end->endOfDay();
            $value = 0;

            switch ($challenge->target_type) {
                case 'topics_viewed':
                    $value = \DB::table('user_topic_progress')
                        ->whereIn('user_id', $memberIds)
                        ->where('is_viewed', true)
                        ->whereBetween('viewed_at', [$weekStart, $weekEnd])
                        ->count();
                    break;

                case 'quizzes_passed':
                    $value = \DB::table('user_quiz_attempts')
                        ->whereIn('user_id', $memberIds)
                        ->where('passed', true)
                        ->whereBetween('created_at', [$weekStart, $weekEnd])
                        ->count();
                    break;

                case 'xp_earned':
                    $value = (int) \DB::table('user_quiz_attempts')
                        ->whereIn('user_id', $memberIds)
                        ->where('passed', true)
                        ->whereBetween('created_at', [$weekStart, $weekEnd])
                        ->sum('score');
                    break;

                case 'tasks_completed':
                    $value = \DB::table('team_tasks')
                        ->where('team_id', $teamId)
                        ->where('status', 'done')
                        ->whereBetween('updated_at', [$weekStart, $weekEnd])
                        ->count();
                    break;
            }

            $completed = $value >= $challenge->target_value;
            $challenge->update([
                'current_value' => $value,
                'is_completed' => $completed,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $challenges->fresh('creator:id,name,profile_photo'),
        ]);
    }
}
