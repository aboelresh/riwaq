<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VideoStreamController extends Controller
{
    public function stream($topicId)
    {
        $user = auth()->user();
        $topic = Topic::findOrFail($topicId);

        // Admin can access any video
        if ($user->role !== 'admin') {
            $progress = $user->topicProgress()
                ->where('topic_id', $topic->id)
                ->first();

            if (!$progress || !$progress->is_unlocked) {
                return response()->json([
                    'success' => false,
                    'message' => 'This video is locked. Complete previous topics first.'
                ], 403);
            }
        }

        // Check if topic is video type
        if ($topic->type !== 'video') {
            return response()->json([
                'success' => false,
                'message' => 'This topic is not a video.'
            ], 400);
        }

        // Get video file path from URL
        $videoUrl = $topic->video_url;
        $videoPath = str_replace(url('/storage/'), '', $videoUrl);

        // Check if file exists
        if (!Storage::disk('public')->exists($videoPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Video file not found.'
            ], 404);
        }

        $filePath = Storage::disk('public')->path($videoPath);
        $fileSize = Storage::disk('public')->size($videoPath);
        $mimeType = Storage::disk('public')->mimeType($videoPath);

        // Handle range requests for video streaming
        $headers = [
            'Content-Type' => $mimeType,
            'Accept-Ranges' => 'bytes',
        ];

        $request = request();
        
        if ($request->header('Range')) {
            $range = $request->header('Range');
            list($start, $end) = $this->getRange($range, $fileSize);

            $length = $end - $start + 1;

            $headers['Content-Length'] = $length;
            $headers['Content-Range'] = "bytes $start-$end/$fileSize";

            $response = new StreamedResponse(function() use ($filePath, $start, $length) {
                $stream = fopen($filePath, 'rb');
                fseek($stream, $start);
                
                $buffer = 1024 * 8;
                while (!feof($stream) && $length > 0) {
                    $read = ($length > $buffer) ? $buffer : $length;
                    echo fread($stream, $read);
                    flush();
                    $length -= $read;
                }
                
                fclose($stream);
            }, 206, $headers);

            return $response;
        }

        // No range request - send entire file
        $headers['Content-Length'] = $fileSize;

        return response()->file($filePath, $headers);
    }

    private function getRange($range, $fileSize)
    {
        $range = str_replace('bytes=', '', $range);
        $parts = explode('-', $range);
        
        $start = intval($parts[0]);
        $end = isset($parts[1]) && $parts[1] !== '' ? intval($parts[1]) : $fileSize - 1;
        
        return [$start, $end];
    }
}