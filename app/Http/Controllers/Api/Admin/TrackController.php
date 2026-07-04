<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTrackRequest;
use App\Http\Requests\Admin\UpdateTrackRequest;
use App\Http\Resources\TrackResource;
use App\Models\Track;
use Illuminate\Http\JsonResponse;

class TrackController extends Controller
{
    public function index(): JsonResponse
    {
        $tracks = Track::with(['creator', 'courses'])->get();

        return response()->json([
            'success' => true,
            'data'    => TrackResource::collection($tracks),
        ]);
    }

    public function store(StoreTrackRequest $request): JsonResponse
    {
        $track = Track::create([
            'title'       => $request->title,
            'description' => $request->description,
            'created_by'  => auth()->id(),
        ]);

        if ($request->filled('course_ids')) {
            $courses = [];
            foreach ($request->course_ids as $index => $courseId) {
                $courses[$courseId] = ['order' => $index + 1];
            }
            $track->courses()->attach($courses);
        }

        return response()->json([
            'success' => true,
            'message' => 'Track created successfully',
            'data'    => new TrackResource($track->load('courses')),
        ], 201);
    }

    public function update(UpdateTrackRequest $request, $id): JsonResponse
    {
        $track = Track::findOrFail($id);

        $track->update([
            'title'       => $request->title,
            'description' => $request->description,
        ]);

        if ($request->filled('course_ids')) {
            $courses = [];
            foreach ($request->course_ids as $index => $courseId) {
                $courses[$courseId] = ['order' => $index + 1];
            }
            $track->courses()->sync($courses);
        }

        return response()->json([
            'success' => true,
            'message' => 'Track updated successfully',
            'data'    => new TrackResource($track->load('courses')),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $track = Track::findOrFail($id);

        $enrolledCount = $track->users()->count();
        if ($enrolledCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete track: {$enrolledCount} user(s) are currently enrolled.",
            ], 409);
        }

        $track->delete();

        return response()->json([
            'success' => true,
            'message' => 'Track deleted successfully',
        ]);
    }
}