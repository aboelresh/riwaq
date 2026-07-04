<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Topic\UpdateVideoProgressRequest;
use App\Http\Resources\TopicResource;
use App\Models\Topic;
use App\Models\UserTopicProgress;
use App\Services\ProgressService;
use Illuminate\Http\JsonResponse;

class TopicController extends Controller
{
    public function __construct(
        private ProgressService $progressService
    ) {}

    public function show($id): JsonResponse
    {
        $user  = auth()->user();
        $topic = Topic::findOrFail($id);

        if (!$user->can('view', $topic)) {
            return response()->json([
                'success' => false,
                'message' => 'This topic is locked.',
            ], 403);
        }

        $topic->load('quiz');

        return response()->json([
            'success' => true,
            'data'    => new TopicResource($topic),
        ]);
    }

    public function markAsViewed($id): JsonResponse
    {
        $user  = auth()->user();
        $topic = Topic::findOrFail($id);

        if (!$user->can('markAsViewed', $topic)) {
            return response()->json([
                'success' => false,
                'message' => 'This topic is locked.',
            ], 403);
        }

        $this->progressService->markTopicAsViewed($user, $topic);

        return response()->json([
            'success' => true,
            'message' => 'Topic marked as viewed',
        ]);
    }

    public function updateVideoProgress(UpdateVideoProgressRequest $request, $id): JsonResponse
    {
        $user  = auth()->user();
        $topic = Topic::findOrFail($id);

        if ($topic->type !== 'video') {
            return response()->json([
                'success' => false,
                'message' => 'This topic is not a video.',
            ], 400);
        }

        $data = $request->validated();

        $progress = UserTopicProgress::firstOrCreate(
            ['user_id' => $user->id, 'topic_id' => $id],
            ['is_unlocked' => true]
        );

        $progress->update([
            'video_last_position'   => (int) $data['current_time'],
            'video_total_seconds'   => (int) $data['duration'],
            'video_watched_seconds' => max($progress->video_watched_seconds, (int) $data['watched_seconds']),
        ]);

        $watchedPct = $data['duration'] > 0
            ? ($data['watched_seconds'] / $data['duration']) * 100
            : 0;

        if ($watchedPct >= 80 && !$progress->is_viewed) {
            $this->progressService->markTopicAsViewed($user, $topic);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'watched_percent' => round($watchedPct),
                'last_position'   => (int) $data['current_time'],
                'is_completed'    => $watchedPct >= 80,
            ],
        ]);
    }

    public function getVideoProgress($id): JsonResponse
    {
        $user     = auth()->user();
        $progress = UserTopicProgress::where('user_id', $user->id)
            ->where('topic_id', $id)
            ->first();

        return response()->json([
            'success' => true,
            'data'    => [
                'last_position'   => $progress?->video_last_position ?? 0,
                'watched_seconds' => $progress?->video_watched_seconds ?? 0,
                'total_seconds'   => $progress?->video_total_seconds ?? 0,
                'is_viewed'       => $progress?->is_viewed ?? false,
            ],
        ]);
    }
}