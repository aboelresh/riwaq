<?php

namespace App\Services;

use App\Models\AssessmentResult;
use App\Models\Topic;
use App\Models\User;
use App\Models\UserCourseProgress;
use App\Models\UserLevelAnalysis;
use App\Models\UserQuizAttempt;
use App\Models\UserTopicProgress;
use App\Models\UserTrack;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LevelAnalysisService
{
    private const CACHE_HOURS = 24;

    public function analyze(User $user, bool $forceRefresh = false): array
    {
        $cached = UserLevelAnalysis::where('user_id', $user->id)->first();
        $isFresh = $cached && $cached->analyzed_at->gt(now()->subHours(self::CACHE_HOURS));

        if ($isFresh && !$forceRefresh) {
            return [
                'stats' => $cached->stats,
                'analysis' => $cached->analysis,
                'model_used' => $cached->model_used,
                'analyzed_at' => $cached->analyzed_at->toIso8601String(),
                'from_cache' => true,
                'next_refresh_in_hours' => max(0, self::CACHE_HOURS - now()->diffInHours($cached->analyzed_at)),
            ];
        }

        $stats = $this->gatherStats($user);
        $analysis = $this->callGroq($stats);
        $modelUsed = env('GROQ_MODEL', 'llama-3.3-70b-versatile');

        $record = UserLevelAnalysis::updateOrCreate(
            ['user_id' => $user->id],
            [
                'stats' => $stats,
                'analysis' => $analysis,
                'model_used' => $modelUsed,
                'analyzed_at' => now(),
            ]
        );

        return [
            'stats' => $stats,
            'analysis' => $analysis,
            'model_used' => $modelUsed,
            'analyzed_at' => $record->analyzed_at->toIso8601String(),
            'from_cache' => false,
            'next_refresh_in_hours' => self::CACHE_HOURS,
        ];
    }

    private function gatherStats(User $user): array
{
    $user->loadMissing([
        'quizAttempts',
        'topicProgress',
        'courseProgress',
        'tracks',
    ]);

    $attempts     = $user->quizAttempts;
    $totalAttempts = $attempts->count();
    $passed        = $attempts->where('passed', true)->count();
    $failed        = $totalAttempts - $passed;
    $passRate      = $totalAttempts > 0 ? round(($passed / $totalAttempts) * 100, 1) : 0;
    $avgScore      = $totalAttempts > 0
        ? round($attempts->avg(fn($a) => ($a->score / max($a->max_score, 1)) * 100), 1)
        : 0;

    $recentScores = $attempts->sortByDesc('attempted_at')
        ->take(10)
        ->map(fn($a) => round(($a->score / max($a->max_score, 1)) * 100, 1))
        ->values()
        ->toArray();

    $topicProgress = $user->topicProgress;
    $topicsViewed  = $topicProgress->where('is_viewed', true)->count();
    $topicsTotal   = \App\Models\Topic::count(); 

    $courseProgress   = $user->courseProgress;
    $coursesCompleted = $courseProgress->where('is_completed', true)->count();
    $coursesStarted   = $courseProgress->filter(fn($c) => !is_null($c->started_at))->count();

    $activeUserTrack = \App\Models\UserTrack::with('track')
        ->where('user_id', $user->id)
        ->where('status', 'active')
        ->first();

    $streak = $this->calculateStreak($user->id);

    $viewedDates = $topicProgress
        ->filter(fn($p) => !is_null($p->viewed_at) && $p->viewed_at >= now()->subDays(30))
        ->map(fn($p) => \Carbon\Carbon::parse($p->viewed_at)->format('Y-m-d'))
        ->unique()
        ->count();

    $lastActivity = $topicProgress
        ->filter(fn($p) => !is_null($p->viewed_at))
        ->sortByDesc('viewed_at')
        ->first()?->viewed_at;

    $daysSinceLast = $lastActivity
        ? (int) now()->diffInDays(\Carbon\Carbon::parse($lastActivity))
        : null;

    $assessment = \App\Models\AssessmentResult::where('user_id', $user->id)
        ->latest()
        ->first();

    return [
        'profile' => [
            'name'               => $user->name,
            'role'               => $user->role,
            'goals'              => $user->goals,
            'member_since_days'  => (int) now()->diffInDays($user->created_at),
        ],
        'quiz' => [
            'total_attempts' => $totalAttempts,
            'passed'         => $passed,
            'failed'         => $failed,
            'pass_rate'      => $passRate,
            'average_score'  => $avgScore,
            'recent_scores'  => $recentScores,
        ],
        'content' => [
            'topics_viewed'          => $topicsViewed,
            'topics_total'           => $topicsTotal,
            'topic_completion_rate'  => $topicsTotal > 0
                ? round(($topicsViewed / $topicsTotal) * 100, 1) : 0,
            'courses_completed'      => $coursesCompleted,
            'courses_started'        => $coursesStarted,
        ],
        'track' => [
            'current' => $activeUserTrack ? [
                'title'             => $activeUserTrack->track?->title,
                'category'          => $activeUserTrack->track?->category,
                'enrolled_days_ago' => (int) now()->diffInDays($activeUserTrack->created_at),
            ] : null,
            'recommended_at_assessment' => $assessment?->recommended_track,
        ],
        'engagement' => [
            'current_streak_days'     => $streak['current'],
            'longest_streak_days'     => $streak['longest'],
            'active_days_last_30'     => $viewedDates,
            'days_since_last_activity'=> $daysSinceLast,
        ],
    ];
}

    private function calculateStreak(int $userId): array
    {
        $activeDates = UserTopicProgress::where('user_id', $userId)
            ->whereNotNull('viewed_at')
            ->where('viewed_at', '>=', now()->subDays(180))
            ->selectRaw('DATE(viewed_at) as date')
            ->groupBy('date')
            ->pluck('date')
            ->toArray();

        if (empty($activeDates)) {
            return ['current' => 0, 'longest' => 0];
        }

        $dateSet = array_flip($activeDates);
        $current = 0;
        $day = now();
        while (isset($dateSet[$day->format('Y-m-d')])) {
            $current++;
            $day = $day->copy()->subDay();
        }

        sort($activeDates);
        $longest = 1;
        $temp = 1;
        $prev = null;
        foreach ($activeDates as $dStr) {
            $d = Carbon::parse($dStr);
            if ($prev && (int) $prev->diffInDays($d) === 1) {
                $temp++;
            } else {
                $temp = 1;
            }
            $longest = max($longest, $temp);
            $prev = $d;
        }

        return ['current' => $current, 'longest' => $longest];
    }

    private function callGroq(array $stats): array
    {
        $apiKey = env('GROQ_API_KEY');
        $model = env('GROQ_MODEL', 'llama-3.3-70b-versatile');

        if (!$apiKey) {
            Log::warning('GROQ_API_KEY not set — using fallback analysis');
            return $this->fallbackAnalysis($stats);
        }

        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $this->systemPrompt()],
                ['role' => 'user', 'content' => $this->buildUserPrompt($stats)],
            ],
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.3,
            'max_tokens' => 1400,
        ];

        try {
            $response = Http::timeout(45)
                ->withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', $payload);

            if (!$response->successful()) {
                Log::error('Groq API non-2xx', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return $this->fallbackAnalysis($stats);
            }

            $content = $response->json('choices.0.message.content');
            $parsed = json_decode((string) $content, true);

            if (!is_array($parsed) || !isset($parsed['level'])) {
                Log::error('Groq returned malformed JSON', ['content' => $content]);
                return $this->fallbackAnalysis($stats);
            }

            return $this->normalizeAnalysis($parsed);
        } catch (\Throwable $e) {
            Log::error('Groq analysis exception: ' . $e->getMessage());
            return $this->fallbackAnalysis($stats);
        }
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
أنت مدرّب محترف لمنصة تعليم البرمجة Code Master. مهمتك تحليل بيانات المتعلم وإعطاء تقييم مفصّل وعملي.

المتعلم بيتعلم في مسارات (web/mobile/data/game)، كل مسار فيه courses → topics → quizzes.
لازم ترد بـ JSON صحيح فقط (بدون أي markdown أو نص خارج JSON) بالشكل التالي بالظبط:

{
  "level": "Beginner|Intermediate|Advanced|Expert",
  "level_score": 0-100,
  "level_summary": "جملة واحدة بالعربي تصف المستوى الحالي",
  "category_scores": {
    "consistency": 0-100,
    "accuracy": 0-100,
    "depth": 0-100,
    "engagement": 0-100,
    "momentum": 0-100
  },
  "strengths": [
    {"area": "بالعربي", "score": 0-100, "evidence": "دليل من الإحصائيات بالعربي"}
  ],
  "weaknesses": [
    {"area": "بالعربي", "score": 0-100, "evidence": "دليل من الإحصائيات بالعربي"}
  ],
  "focus_areas": [
    {"title": "بالعربي", "priority": "high|medium|low", "reason": "بالعربي"}
  ],
  "improvement_plan": {
    "this_week": ["3 إجراءات بالعربي"],
    "this_month": ["3 إجراءات بالعربي"],
    "next_3_months": ["3 أهداف بالعربي"]
  },
  "motivation": "رسالة تحفيزية شخصية بالعربي من جملتين أو ثلاثة",
  "estimated_completion_weeks": رقم تقديري للمسار الحالي,
  "risk_flags": ["تحذيرات إن وُجدت"]
}

قواعد صارمة:
- 3 strengths، 3 weaknesses كحد أقصى
- 3-5 focus_areas مرتبة حسب الأولوية
- بالعربي ماعدا الـ keys في الـ JSON
- كن صريح ومحدد، استخدم أرقام من الإحصائيات
- لو البيانات قليلة جداً (متعلم جديد)، ركّز على نصائح البداية والاستمرارية بدل تقييم متعمق
- استخدم أرقام تنطبق فعلاً على البيانات (مش تخيلية)
- category_scores يجب أن تكون متناسقة مع level_score
PROMPT;
    }

    private function buildUserPrompt(array $stats): string
    {
        return "حلّل البيانات التالية لهذا المتعلم وارجع التحليل بصيغة JSON المحددة:\n\n"
            . json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function normalizeAnalysis(array $a): array
    {
        $defaults = [
            'level' => 'Beginner',
            'level_score' => 0,
            'level_summary' => '',
            'category_scores' => [
                'consistency' => 0, 'accuracy' => 0, 'depth' => 0, 'engagement' => 0, 'momentum' => 0,
            ],
            'strengths' => [],
            'weaknesses' => [],
            'focus_areas' => [],
            'improvement_plan' => ['this_week' => [], 'this_month' => [], 'next_3_months' => []],
            'motivation' => '',
            'estimated_completion_weeks' => null,
            'risk_flags' => [],
        ];
        return array_replace_recursive($defaults, $a);
    }

    private function fallbackAnalysis(array $stats): array
    {
        $level = $this->inferLevel($stats);
        $avg = $stats['quiz']['average_score'] ?? 0;
        $activity30 = $stats['engagement']['active_days_last_30'] ?? 0;
        $daysSince = $stats['engagement']['days_since_last_activity'] ?? 0;
        $completion = $stats['content']['topic_completion_rate'] ?? 0;
        $streak = $stats['engagement']['current_streak_days'] ?? 0;

        return [
            'level' => $level,
            'level_score' => (int) round($avg),
            'level_summary' => 'تحليل أولي مبني على بياناتك (تعذّر الاتصال بالمحلل الذكي)',
            'category_scores' => [
                'consistency' => min(100, $activity30 * 3),
                'accuracy' => (int) round($avg),
                'depth' => (int) round($completion),
                'engagement' => min(100, $streak * 5),
                'momentum' => max(0, 100 - $daysSince * 10),
            ],
            'strengths' => [],
            'weaknesses' => [],
            'focus_areas' => [],
            'improvement_plan' => ['this_week' => [], 'this_month' => [], 'next_3_months' => []],
            'motivation' => 'استمر في رحلتك التعليمية!',
            'estimated_completion_weeks' => null,
            'risk_flags' => ['تعذّر الاتصال بخدمة التحليل الذكي - النتائج مبنية على حسابات بسيطة'],
            '_fallback' => true,
        ];
    }

    private function inferLevel(array $stats): string
    {
        $score = $stats['quiz']['average_score'] ?? 0;
        $completion = $stats['content']['topic_completion_rate'] ?? 0;
        if ($score >= 85 && $completion >= 50) return 'Expert';
        if ($score >= 70 && $completion >= 30) return 'Advanced';
        if ($score >= 50 && $completion >= 10) return 'Intermediate';
        return 'Beginner';
    }
}
