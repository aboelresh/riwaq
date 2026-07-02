<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Services\QuizService;
use App\Services\UnlockService;
use App\Services\ProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    public function __construct(
        private QuizService $quizService,
        private UnlockService $unlockService,
        private ProgressService $progressService
    ) {}

    public function show($id)
    {
        $user = auth()->user();
        $quiz = Quiz::with('questions.answers')->findOrFail($id);

        // Policy check
        if (!$user->can('view', $quiz)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot access this quiz at this time.'
            ], 403);
        }

        // Check if user can take quiz (retry logic)
        if (!$this->quizService->canTakeQuiz($user, $quiz)) {
            $lastAttempt = $this->quizService->getLastAttempt($user, $quiz);
            
            return response()->json([
                'success' => false,
                'message' => 'Cannot take quiz at this time',
                'can_retry_at' => $lastAttempt?->can_retry_at
            ], 403);
        }

        // Prepare questions (hide correct answers)
        $questions = $quiz->questions->map(function($question) {
            return [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'points' => $question->points,
                'answers' => $question->answers->map(function($answer) {
                    return [
                        'id' => $answer->id,
                        'answer_text' => $answer->answer_text,
                        // is_correct is hidden from user
                    ];
                })
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'quiz' => $quiz,
                'questions' => $questions,
            ]
        ]);
    }

    public function submit(Request $request, $id)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'answers' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        $quiz = Quiz::findOrFail($id);

        try {
            // Convert [{question_id, answer_id}] to [question_id => answer_id]
            $answersMap = [];
            foreach ($request->answers as $a) {
                if (is_array($a) && isset($a['question_id'], $a['answer_id'])) {
                    $answersMap[$a['question_id']] = $a['answer_id'];
                } elseif (is_object($a)) {
                    $answersMap[$a->question_id] = $a->answer_id;
                }
            }

            // Submit quiz and get attempt
            $attempt = $this->quizService->submitQuiz($user, $quiz, $answersMap);

            // Handle unlock logic based on quiz type
            if ($attempt->passed) {
                if ($quiz->type === 'topic' && $quiz->topic_id) {
                    // Topic quiz passed - unlock next topic
                    $topic = $quiz->topic;
                    $course = $topic->courses()->first();
                    
                    if ($course) {
                        $this->unlockService->unlockNextTopicInCourse($user, $course, $topic);
                        $this->progressService->updateCourseScore($user, $course);
                    }
                } 
                elseif ($quiz->type === 'course' && $quiz->course_id) {
                    // Course final quiz passed - unlock next course
                    $course = $quiz->course;
                    $this->progressService->updateCourseScore($user, $course);
                    
                    $track = $course->tracks()->first();
                    if ($track) {
                        $this->unlockService->unlockNextCourseInTrack($user, $track, $course);
                    }
                }
                // Assessment quizzes don't unlock anything
            }

            return response()->json([
                'success' => true,
                'message' => 'Quiz submitted successfully',
                'data' => $attempt
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function result($attemptId)
    {
        $user = auth()->user();
        
        $attempt = \App\Models\UserQuizAttempt::with([
            'quiz',
            'answers.question',
            'answers.answer'
        ])->where('user_id', $user->id)
          ->findOrFail($attemptId);

        return response()->json([
            'success' => true,
            'data' => $attempt
        ]);
    }
}