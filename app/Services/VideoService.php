<?php

namespace App\Services;

use App\Models\Video;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoService
{
    public function uploadVideo(UploadedFile $file, User $user, string $title, ?int $topicId = null): Video
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '_' . time() . '.' . $extension;

        $path = $file->storeAs('videos', $filename, 'public');

        $size = $file->getSize();

        return Video::create([
            'title' => $title,
            'filename' => $filename,
            'path' => $path,
            'size' => $size,
            'duration' => null,
            'uploaded_by' => $user->id,
            'topic_id' => $topicId,
        ]);
    }

    public function deleteVideo(Video $video): bool
    {
        if (Storage::disk('public')->exists($video->path)) {
            Storage::disk('public')->delete($video->path);
        }
        
        return $video->delete();
    }

    public function getVideoUrl(Video $video): string
    {
        return asset('storage/' . $video->path);
    }

    public function getAllVideos()
    {
        return Video::with(['uploader', 'topic:id,title'])->latest()->get();
    }

    public function getVideoById(int $id): ?Video
    {
        return Video::with('uploader')->find($id);
    }
}