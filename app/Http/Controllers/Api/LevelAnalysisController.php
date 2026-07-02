<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LevelAnalysisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LevelAnalysisController extends Controller
{
    public function __construct(private LevelAnalysisService $service) {}

    public function show(Request $request): JsonResponse
    {
        $forceRefresh = $request->boolean('refresh');
        $result = $this->service->analyze($request->user(), $forceRefresh);

        return response()->json([
            'success' => true,
            'message' => $result['from_cache']
                ? 'تم استرجاع التحليل المحفوظ'
                : 'تم إنشاء تحليل جديد',
            'data' => $result,
        ]);
    }
}
