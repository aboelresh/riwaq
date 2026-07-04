<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Quiz\SubmitQuizRequest;
use App\Models\Quiz;
use App\Models\UserQuizAttempt;
use App\Services\ProgressService;
use App\Services\QuizService;
use App\Services\UnlockService;
use Illuminate\Http\JsonResponse;

class QuizController extends Controller
{
    public function __construct(
        private QuizService     $quizService,
        private UnlockService   $unlockService,
        private ProgressService $progressService
    ) {}

    public function show($id): JsonResponse
    {
        $user = auth()->user();
        $quiz = Quiz::with('questions.answers')->findOrFail($id);

        if (!$user->can('view', $quiz)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot access this quiz at this time.',
            ], 403);
        }

        if (!$this->quizService->canTakeQuiz($user, $quiz)) {
            $lastAttempt = $this->quizService->getLastAttempt($user, $quiz);

            return response()->json([
                'success'      => false,
                'message'      => 'Cannot take quiz at this time.',
                'can_retry_at' => $lastAttempt?->can_retry_at,
            ], 403);
        }

        // Hide is_correct from user before submission
        $questions = $quiz->questions->map(fn($question) => [
            'id'            => $question->id,
            'question_text' => $question->question_text,
            'points'        => $question->points,
            'answers'       => $question->answers->map(fn($answer) => [
                'id'          => $answer->id,
                'answer_text' => $answer->answer_text,
                // is_correct intentionally hidden
            ]),
        ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'quiz'      => $quiz,
                'questions' => $questions,
            ],
        ]);
    }

    public function submit(SubmitQuizRequest $request, $id): JsonResponse
    {
        $user = auth()->user();
        $quiz = Quiz::findOrFail($id);

        // Convert [{question_id, answer_id}] to [question_id => answer_id]
        $answersMap = [];
        foreach ($request->validated('answers') as $a) {
            $answersMap[$a['question_id']] = $a['answer_id'];
        }

        $attempt = $this->quizService->submitQuiz($user, $quiz, $answersMap);

        if ($attempt->passed) {
            if ($quiz->type === 'topic' && $quiz->topic_id) {
                $topic  = $quiz->topic;
                $course = $topic->courses()->first();
                if ($course) {
                    $this->unlockService->unlockNextTopicInCourse($user, $course, $topic);
                    $this->progressService->updateCourseScore($user, $course);
                }
            } elseif ($quiz->type === 'course' && $quiz->course_id) {
                $course = $quiz->course;
                $this->progressService->updateCourseScore($user, $course);
                $track = $course->tracks()->first();
                if ($track) {
                    $this->unlockService->unlockNextCourseInTrack($user, $track, $course);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Quiz submitted successfully',
            'data'    => $attempt,
        ]);
    }

    public function result($attemptId): JsonResponse
    {
        $user = auth()->user();

        $attempt = UserQuizAttempt::with([
            'quiz',
            'answers.question',
            'answers.answer',
        ])->where('user_id', $user->id)->findOrFail($attemptId);

        return response()->json([
            'success' => true,
            'data'    => $attempt,
        ]);
    }
}