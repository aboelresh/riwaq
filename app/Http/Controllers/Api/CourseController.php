<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\UnlockService;

class CourseController extends Controller
{
    public function __construct(
        private UnlockService $unlockService
    ) {}

    /**
     * Public: list all courses with their track info and topic count.
     */
    public function index()
    {
        $courses = Course::with(['tracks' => function ($q) {
            $q->select('tracks.id', 'tracks.title');
        }])
        ->withCount('topics')
        ->get()
        ->map(function ($course) {
            return [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'topics_count' => $course->topics_count,
                'track' => $course->tracks->first() ? [
                    'id' => $course->tracks->first()->id,
                    'title' => $course->tracks->first()->title,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $courses,
        ]);
    }

    public function show($id)
    {
        $user = auth()->user();
        $course = Course::with(['topics.quiz'])->findOrFail($id);

        // Check if course is unlocked
        $progress = $user->courseProgress()
            ->where('course_id', $course->id)
            ->first();

        if (!$progress || !$progress->is_unlocked) {
            return response()->json([
                'success' => false,
                'message' => 'This course is locked. Complete the previous course first.'
            ], 403);
        }

        // Get topics with unlock status
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

        // Get course final quiz
        $courseFinalQuiz = $course->quizzes()
            ->where('type', 'course')
            ->first();

        $courseFinalQuizData = null;
        if ($courseFinalQuiz) {
            // Check if user can take the final quiz
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