<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Assessment\SubmitAssessmentRequest;
use App\Services\AssessmentService;
use Illuminate\Http\JsonResponse;

class AssessmentController extends Controller
{
    public function __construct(
        private AssessmentService $assessmentService
    ) {}

    public function getQuestions(): JsonResponse
    {
        $questions = $this->assessmentService->getAssessmentQuestions();

        return response()->json([
            'success' => true,
            'data'    => [
                'questions'       => $questions,
                'total_questions' => count($questions),
            ],
        ]);
    }

    public function submit(SubmitAssessmentRequest $request): JsonResponse
    {
        $user   = auth()->user();
        $result = $this->assessmentService->evaluateAssessment(
            $user,
            $request->validated('answers')
        );

        return response()->json([
            'success' => true,
            'message' => 'Assessment completed successfully!',
            'data'    => [
                'recommended_track' => $result->recommendedTrack,
                'scores'            => $result->scores,
                'analysis'          => $result->analysis,
            ],
        ]);
    }

    public function getMyResult(): JsonResponse
    {
        $user   = auth()->user();
        $result = $this->assessmentService->getUserAssessment($user);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'No assessment found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $result,
        ]);
    }
}