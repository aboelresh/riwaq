<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuizRequest;
use App\Http\Requests\Admin\UpdateQuizRequest;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function index(): JsonResponse
{
    $quizzes = Quiz::with(['creator', 'topic', 'course'])->paginate(20);

    return response()->json([
        'success' => true,
        'data'    => $quizzes->items(),
        'meta'    => [
            'current_page' => $quizzes->currentPage(),
            'per_page'     => $quizzes->perPage(),
            'total'        => $quizzes->total(),
            'last_page'    => $quizzes->lastPage(),
        ],
    ]);
}

    public function store(StoreQuizRequest $request): JsonResponse
    {
        // DB::transaction() auto-rollbacks on any exception —
        // no try/catch needed. Exception propagates to ApiExceptionHandler.
        $quiz = DB::transaction(function () use ($request) {
            $quiz = Quiz::create([
                'title'           => $request->title,
                'type'            => $request->type,
                'topic_id'        => $request->topic_id,
                'course_id'       => $request->course_id,
                'total_points'    => $request->total_points,
                'pass_percentage' => $request->pass_percentage,
                'created_by'      => auth()->id(),
            ]);

            foreach ($request->questions as $questionData) {
                $question = Question::create([
                    'quiz_id'       => $quiz->id,
                    'question_text' => $questionData['question_text'],
                    'points'        => $questionData['points'],
                ]);

                foreach ($questionData['answers'] as $answerData) {
                    Answer::create([
                        'question_id' => $question->id,
                        'answer_text' => $answerData['answer_text'],
                        'is_correct'  => $answerData['is_correct'],
                    ]);
                }
            }

            return $quiz;
        });

        return response()->json([
            'success' => true,
            'message' => 'Quiz created successfully.',
            'data'    => $quiz->load('questions.answers'),
        ], 201);
    }

    public function update(UpdateQuizRequest $request, $id): JsonResponse
    {
        $quiz = Quiz::findOrFail($id);

        $quiz = DB::transaction(function () use ($request, $quiz) {
            $quiz->update([
                'title'           => $request->title,
                'type'            => $request->type,
                'topic_id'        => $request->topic_id,
                'course_id'       => $request->course_id,
                'total_points'    => $request->total_points,
                'pass_percentage' => $request->pass_percentage,
            ]);

            if ($request->filled('questions')) {
                $quiz->questions()->each(function ($q) {
                    $q->answers()->delete();
                    $q->delete();
                });

                foreach ($request->questions as $qData) {
                    $question = Question::create([
                        'quiz_id'       => $quiz->id,
                        'question_text' => $qData['question_text'],
                        'points'        => $qData['points'],
                    ]);
                    foreach ($qData['answers'] as $aData) {
                        Answer::create([
                            'question_id' => $question->id,
                            'answer_text' => $aData['answer_text'],
                            'is_correct'  => $aData['is_correct'],
                        ]);
                    }
                }
            }

            return $quiz;
        });

        return response()->json([
            'success' => true,
            'message' => 'Quiz updated successfully.',
            'data'    => $quiz->load('questions.answers'),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $quiz = Quiz::findOrFail($id);

        DB::transaction(function () use ($quiz) {
            $quiz->questions()->each(function ($q) {
                $q->answers()->delete();
                $q->delete();
            });
            $quiz->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Quiz deleted successfully.',
        ]);
    }
}