<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\UserTopicProgress;
use App\Services\ProgressService;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function __construct(
        private ProgressService $progressService
    ) {}

    public function show($id)
    {
        $user = auth()->user();
        $topic = Topic::findOrFail($id);

        // Policy check only (handles unlock logic internally)
        if (!$user->can('view', $topic)) {
            return response()->json([
                'success' => false,
                'message' => 'This topic is locked.'
            ], 403);
        }

        // Load quiz relationship
        $topic->load('quiz');

        return response()->json([
            'success' => true,
            'data' => $topic
        ]);
    }

    public function markAsViewed($id)
    {
        $user = auth()->user();
        $topic = Topic::findOrFail($id);

        // Policy check
        if (!$user->can('markAsViewed', $topic)) {
            return response()->json([
                'success' => false,
                'message' => 'This topic is locked.'
            ], 403);
        }

        $this->progressService->markTopicAsViewed($user, $topic);

        return response()->json([
            'success' => true,
            'message' => 'Topic marked as viewed'
        ]);
    }

    /**
     * POST /topics/{id}/video-progress
     * Save video watch progress (position, watched seconds)
     */
    public function updateVideoProgress(Request $request, $id)
    {
        $user = auth()->user();
        $topic = Topic::findOrFail($id);

        if ($topic->type !== 'video') {
            return response()->json(['success' => false, 'message' => 'Not a video topic'], 400);
        }

        $validated = $request->validate([
            'current_time' => 'required|numeric|min:0',
            'duration' => 'required|numeric|min:1',
            'watched_seconds' => 'required|integer|min:0',
        ]);

        $progress = UserTopicProgress::firstOrCreate(
            ['user_id' => $user->id, 'topic_id' => $id],
            ['is_unlocked' => true]
        );

        $progress->update([
            'video_last_position' => (int) $validated['current_time'],
            'video_total_seconds' => (int) $validated['duration'],
            'video_watched_seconds' => max($progress->video_watched_seconds, (int) $validated['watched_seconds']),
        ]);

        // Auto-mark as viewed if watched 80%+
        $watchedPct = $validated['duration'] > 0 ? ($validated['watched_seconds'] / $validated['duration']) * 100 : 0;
        if ($watchedPct >= 80 && !$progress->is_viewed) {
            $this->progressService->markTopicAsViewed($user, $topic);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'watched_percent' => round($watchedPct),
                'last_position' => (int) $validated['current_time'],
                'is_completed' => $watchedPct >= 80,
            ]
        ]);
    }

    /**
     * GET /topics/{id}/video-progress
     * Get saved video progress for resume
     */
    public function getVideoProgress($id)
    {
        $user = auth()->user();
        $progress = UserTopicProgress::where('user_id', $user->id)->where('topic_id', $id)->first();

        return response()->json([
            'success' => true,
            'data' => [
                'last_position' => $progress?->video_last_position ?? 0,
                'watched_seconds' => $progress?->video_watched_seconds ?? 0,
                'total_seconds' => $progress?->video_total_seconds ?? 0,
                'is_viewed' => $progress?->is_viewed ?? false,
            ]
        ]);
    }
}