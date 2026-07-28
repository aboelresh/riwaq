<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVideoRequest;
use App\Services\VideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class VideoController extends Controller
{
    public function __construct(
        private VideoService $videoService
    ) {}

    public function index(): JsonResponse
    {
        $videos = $this->videoService->getAllVideos();

        return response()->json([
            'success' => true,
            'data'    => $videos,
        ]);
    }

    public function upload(StoreVideoRequest $request): JsonResponse
    {
        // Bug 016 Fix: removed ini_set() calls for upload_max_filesize/post_max_size
        // These PHP directives are read before any code executes and cannot
        // be changed at runtime. The correct values are set in public/.htaccess
        // which is read by Apache before PHP starts.
        // set_time_limit(300) is kept — it CAN be set at runtime safely.
        set_time_limit(300);

        $video = $this->videoService->uploadVideo(
            $request->file('video'),
            auth()->user(),
            $request->title,
            $request->topic_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Video uploaded successfully.',
            'data'    => $video,
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $video = $this->videoService->getVideoById($id);

        if (!$video) {
            return response()->json([
                'success' => false,
                'message' => 'Video not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $video,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $video = $this->videoService->getVideoById($id);

        if (!$video) {
            return response()->json([
                'success' => false,
                'message' => 'Video not found.',
            ], 404);
        }

        $this->videoService->deleteVideo($video);

        return response()->json([
            'success' => true,
            'message' => 'Video deleted successfully.',
        ]);
    }
}