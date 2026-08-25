<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\UnlockService;
use Illuminate\Http\JsonResponse;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
class CourseController extends Controller
{
    public function __construct(
        private UnlockService $unlockService
    ) {}


    public function index(Request $request): JsonResponse
{
    $page     = $request->get('page', 1);
    $cacheKey = CacheService::coursesKey($page);

    $data = Cache::remember($cacheKey, CacheService::TTL_COURSES, function () {
        $courses = Course::with(['tracks' => fn($q) => $q->select('tracks.id', 'tracks.title')])
            ->withCount('topics')
            ->paginate(15);

        $items = collect($courses->items())->map(fn($course) => [
            'id'           => $course->id,
            'title'        => $course->title,
            'description'  => $course->description,
            'topics_count' => $course->topics_count,
            'track'        => $course->tracks->first()
                ? ['id' => $course->tracks->first()->id, 'title' => $course->tracks->first()->title]
                : null,
        ])->toArray();

        return [
            'data' => $items,
            'meta' => [
                'current_page' => $courses->currentPage(),
                'per_page'     => $courses->perPage(),
                'total'        => $courses->total(),
                'last_page'    => $courses->lastPage(),
            ],
        ];
    });

    return response()->json(['success' => true] + $data);
}
    public function show($id)
    {
        $user = auth()->user();
        $course = Course::with(['topics.quiz'])->findOrFail($id);

        $progress = $user->courseProgress()
            ->where('course_id', $course->id)
            ->first();

        if (!$progress || !$progress->is_unlocked) {
            return response()->json([
                'success' => false,
                'message' => 'This course is locked. Complete the previous course first.'
            ], 403);
        }

        $topics = $course->topics()->orderBy('order')->get()->map(function($topic) use ($user) {
            $topicProgress = $user->topicProgress()
                ->where('topic_id', $topic->id)
                ->first();

            return [
                'id' => $topic->id,
                'title' => $topic->title,
                'type' => $topic->type,
                'order' => $topic->pivot->order,
                'is_unlocked' => $topicProgress ? $topicProgress->is_unlocked : false,
                'is_viewed' => $topicProgress ? $topicProgress->is_viewed : false,
                'quiz' => $topic->quiz ? [
                    'id' => $topic->quiz->id,
                    'title' => $topic->quiz->title,
                    'total_points' => $topic->quiz->total_points,
                    'pass_percentage' => $topic->quiz->pass_percentage,
                ] : null,
            ];
        });

        $courseFinalQuiz = $course->quizzes()
            ->where('type', 'course')
            ->first();

        $courseFinalQuizData = null;
        if ($courseFinalQuiz) {
            $allTopicsViewed = $topics->every(fn($t) => $t['is_viewed']);
            
            $courseFinalQuizData = [
                'id' => $courseFinalQuiz->id,
                'title' => $courseFinalQuiz->title,
                'total_points' => $courseFinalQuiz->total_points,
                'pass_percentage' => $courseFinalQuiz->pass_percentage,
                'is_available' => $allTopicsViewed,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'course' => [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                ],
                'topics' => $topics,
                'course_final_quiz' => $courseFinalQuizData,
            ]
        ]);
    }
}