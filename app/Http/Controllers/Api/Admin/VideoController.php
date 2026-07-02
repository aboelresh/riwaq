<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\VideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
    public function __construct(
        private VideoService $videoService
    ) {}

    public function index()
    {
        $videos = $this->videoService->getAllVideos();

        return response()->json([
            'success' => true,
            'data' => $videos
        ]);
    }

    public function upload(Request $request)
    {
        // Override PHP limits for large video uploads (works regardless of php.ini)
        set_time_limit(300);
        ini_set('max_execution_time', '300');
        ini_set('upload_max_filesize', '512M');
        ini_set('post_max_size', '600M');
        ini_set('memory_limit', '1G');

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'video' => 'required|file|mimes:mp4,avi,mov,wmv|max:512000',
            'topic_id' => 'nullable|integer|exists:topics,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $video = $this->videoService->uploadVideo(
                $request->file('video'),
                auth()->user(),
                $request->title,
                $request->topic_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Video uploaded successfully',
                'data' => $video
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload video',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $video = $this->videoService->getVideoById($id);

        if (!$video) {
            return response()->json([
                'success' => false,
                'message' => 'Video not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $video
        ]);
    }

    public function destroy($id)
    {
        $video = $this->videoService->getVideoById($id);

        if (!$video) {
            return response()->json([
                'success' => false,
                'message' => 'Video not found'
            ], 404);
        }

        $this->videoService->deleteVideo($video);

        return response()->json([
            'success' => true,
            'message' => 'Video deleted successfully'
        ]);
    }
}