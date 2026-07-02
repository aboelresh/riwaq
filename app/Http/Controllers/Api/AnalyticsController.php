<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserTopicProgress;
use App\Models\UserQuizAttempt;
use App\Models\UserCourseProgress;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userId = $user->id;
        $now = Carbon::now();

        // ── Daily activity for heatmap (last 365 days) ──
        $startDate = $now->copy()->subDays(364)->startOfDay();

        // Topics viewed per day
        $topicDays = UserTopicProgress::where('user_id', $userId)
            ->where('status', 'viewed')
            ->where('updated_at', '>=', $startDate)
            ->select(DB::raw('DATE(updated_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Quiz attempts per day
        $quizDays = UserQuizAttempt::where('user_id', $userId)
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Merge into daily activity
        $heatmap = [];
        $date = $startDate->copy();
        while ($date->lte($now)) {
            $key = $date->format('Y-m-d');
            $heatmap[$key] = ($topicDays[$key] ?? 0) + ($quizDays[$key] ?? 0);
            $date->addDay();
        }

        // ── Weekly activity (last 7 days) ──
        $weeklyActivity = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i)->format('Y-m-d');
            $weeklyActivity[] = $heatmap[$day] ?? 0;
        }

        // ── Monthly progress (last 12 months) ──
        $monthlyProgress = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthStart = $now->copy()->subMonths($i)->startOfMonth();
            $monthEnd = $now->copy()->subMonths($i)->endOfMonth();

            $topics = UserTopicProgress::where('user_id', $userId)
                ->where('status', 'viewed')
                ->whereBetween('updated_at', [$monthStart, $monthEnd])
                ->count();

            $quizzes = UserQuizAttempt::where('user_id', $userId)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $monthlyProgress[] = [
                'month' => $monthStart->format('M'),
                'value' => $topics + $quizzes,
            ];
        }

        // ── Skill distribution (based on quiz scores per track) ──
        $skills = DB::table('user_quiz_attempts')
            ->join('quizzes', 'user_quiz_attempts.quiz_id', '=', 'quizzes.id')
            ->leftJoin('course_topics', 'quizzes.topic_id', '=', 'course_topics.topic_id')
            ->leftJoin('track_courses', 'course_topics.course_id', '=', 'track_courses.course_id')
            ->leftJoin('tracks', 'track_courses.track_id', '=', 'tracks.id')
            ->where('user_quiz_attempts.user_id', $userId)
            ->select('tracks.title as track', DB::raw('AVG(user_quiz_attempts.score) as avg_score'), DB::raw('COUNT(*) as attempts'))
            ->groupBy('tracks.title')
            ->get()
            ->map(fn($row) => [
                'track' => $row->track ?? 'General',
                'score' => round($row->avg_score ?? 0),
                'attempts' => $row->attempts,
            ]);

        // ── Streaks ──
        // Preserve yesterday's streak until the user does something today:
        // if today has no activity yet, start counting from yesterday.
        $streak = 0;
        $checkDate = $now->copy()->startOfDay();
        if (($heatmap[$checkDate->format('Y-m-d')] ?? 0) === 0) {
            $checkDate->subDay();
        }
        while (($heatmap[$checkDate->format('Y-m-d')] ?? 0) > 0) {
            $streak++;
            $checkDate->subDay();
        }

        // ── Achievements ──
        $totalTopics = UserTopicProgress::where('user_id', $userId)->where('status', 'viewed')->count();
        $totalQuizzes = UserQuizAttempt::where('user_id', $userId)->where('passed', true)->count();
        $totalCourses = UserCourseProgress::where('user_id', $userId)->where('status', 'completed')->count();
        $totalTracks = $user->tracks()->count();
        $totalScore = UserQuizAttempt::where('user_id', $userId)->sum('score');

        // ── Daily bar chart (this week, per day) ──
        $dailyBars = [];
        $dayLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $weekStart = $now->copy()->startOfWeek(Carbon::MONDAY);
        for ($i = 0; $i < 7; $i++) {
            $day = $weekStart->copy()->addDays($i)->format('Y-m-d');
            $dailyBars[] = [
                'label' => $dayLabels[$i],
                'value' => $heatmap[$day] ?? 0,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'heatmap' => $heatmap,
                'weekly_activity' => $weeklyActivity,
                'monthly_progress' => $monthlyProgress,
                'daily_bars' => $dailyBars,
                'skills' => $skills,
                'streak' => $streak,
                'total_topics' => $totalTopics,
                'total_quizzes_passed' => $totalQuizzes,
                'total_courses_completed' => $totalCourses,
                'total_tracks' => $totalTracks,
                'total_score' => $totalScore,
                'total_courses_unlocked' => UserCourseProgress::where('user_id', $userId)->count(),
            ],
        ]);
    }
}
