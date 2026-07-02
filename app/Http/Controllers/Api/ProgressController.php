<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProgressService;

class ProgressController extends Controller
{
    public function __construct(
        private ProgressService $progressService
    ) {}

    public function index()
    {
        $user = auth()->user();
        $summary = $this->progressService->getUserProgressSummary($user);

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }
}