<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::with(['creator', 'topic', 'course', 'questions.answers'])->get();

        return response()->json([
            'success' => true,
            'data' => $quizzes
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|in:assessment,topic,course',
            'topic_id' => 'required_if:type,topic|nullable|exists:topics,id',
            'course_id' => 'required_if:type,course|nullable|exists:courses,id',
            'total_points' => 'required|integer|min:1',
            'pass_percentage' => 'required|integer|min:0|max:100',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.points' => 'required|integer|min:1',
            'questions.*.answers' => 'required|array|min:2',
            'questions.*.answers.*.answer_text' => 'required|string',
            'questions.*.answers.*.is_correct' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $quiz = Quiz::create([
                'title' => $request->title,
                'type' => $request->type,
                'topic_id' => $request->topic_id,
                'course_id' => $request->course_id,
                'total_points' => $request->total_points,
                'pass_percentage' => $request->pass_percentage,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->questions as $questionData) {
                $question = Question::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => $questionData['question_text'],
                    'points' => $questionData['points'],
                ]);

                foreach ($questionData['answers'] as $answerData) {
                    Answer::create([
                        'question_id' => $question->id,
                        'answer_text' => $answerData['answer_text'],
                        'is_correct' => $answerData['is_correct'],
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quiz created successfully',
                'data' => $quiz->load('questions.answers')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create quiz',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|in:assessment,topic,course',
            'topic_id' => 'required_if:type,topic|nullable|exists:topics,id',
            'course_id' => 'required_if:type,course|nullable|exists:courses,id',
            'total_points' => 'required|integer|min:1',
            'pass_percentage' => 'required|integer|min:0|max:100',
            // Questions are optional on update — if provided, replaces all
            'questions' => 'nullable|array',
            'questions.*.question_text' => 'required_with:questions|string',
            'questions.*.points' => 'required_with:questions|integer|min:1',
            'questions.*.answers' => 'required_with:questions|array|min:2',
            'questions.*.answers.*.answer_text' => 'required|string',
            'questions.*.answers.*.is_correct' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $quiz = Quiz::findOrFail($id);

        DB::beginTransaction();
        try {
            $quiz->update([
                'title' => $request->title,
                'type' => $request->type,
                'topic_id' => $request->topic_id,
                'course_id' => $request->course_id,
                'total_points' => $request->total_points,
                'pass_percentage' => $request->pass_percentage,
            ]);

            // If questions provided, delete old and create new
            if ($request->has('questions') && is_array($request->questions)) {
                // Delete old questions (cascade deletes answers)
                $quiz->questions()->each(function ($q) {
                    $q->answers()->delete();
                    $q->delete();
                });

                // Create new questions + answers
                foreach ($request->questions as $qData) {
                    $question = Question::create([
                        'quiz_id' => $quiz->id,
                        'question_text' => $qData['question_text'],
                        'points' => $qData['points'],
                    ]);
                    foreach ($qData['answers'] as $aData) {
                        Answer::create([
                            'question_id' => $question->id,
                            'answer_text' => $aData['answer_text'],
                            'is_correct' => $aData['is_correct'],
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quiz updated successfully',
                'data' => $quiz->load('questions.answers')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update quiz', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->delete();

        return response()->json([
            'success' => true,
            'message' => 'Quiz deleted successfully'
        ]);
    }
}