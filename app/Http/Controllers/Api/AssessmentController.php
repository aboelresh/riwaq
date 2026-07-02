<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AssessmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssessmentController extends Controller
{
    public function __construct(
        private AssessmentService $assessmentService
    ) {}

    /**
     * Get assessment questions
     */
    public function getQuestions()
    {
        $questions = $this->assessmentService->getAssessmentQuestions();

        return response()->json([
            'success' => true,
            'data' => [
                'questions' => $questions,
                'total_questions' => count($questions),
            ]
        ]);
    }

    /**
     * Submit assessment answers
     */
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'answers' => 'required|array',
            'answers.*' => 'required|string|in:A,B,C,D',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();

        try {
            $result = $this->assessmentService->evaluateAssessment($user, $request->answers);

            return response()->json([
                'success' => true,
                'message' => 'Assessment completed successfully!',
                'data' => [
                    'recommended_track' => $result->recommendedTrack,
                    'scores' => $result->scores,
                    'analysis' => $result->analysis,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process assessment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's assessment result
     */
    public function getMyResult()
    {
        $user = auth()->user();
        $result = $this->assessmentService->getUserAssessment($user);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'No assessment found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}