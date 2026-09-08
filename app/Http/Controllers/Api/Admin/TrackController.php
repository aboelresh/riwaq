<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTrackRequest;
use App\Http\Requests\Admin\UpdateTrackRequest;
use App\Http\Resources\TrackResource;
use App\Models\Track;
use Illuminate\Http\JsonResponse;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;
use App\Services\AuditLogService;
class TrackController extends Controller
{
   public function index(): JsonResponse
{
    $tracks = Track::with(['creator', 'courses'])->paginate(20);

    return response()->json([
        'success' => true,
        'data'    => TrackResource::collection($tracks->items()),
        'meta'    => [
            'current_page' => $tracks->currentPage(),
            'per_page'     => $tracks->perPage(),
            'total'        => $tracks->total(),
            'last_page'    => $tracks->lastPage(),
        ],
    ]);
}

    public function store(StoreTrackRequest $request): JsonResponse
    {
         $org = \App\SaaS\TenantContext::isResolved()
        ? \App\SaaS\TenantContext::current()
        : null;

        if ($org) {
        $result = \App\SaaS\EntitlementService::canCreateTrack($org);
        if (!$result->allowed) {
            return response()->json([
                'success' => false,
                'message' => $result->reason,
            ], 403);
        }
    }
        $track = Track::create([
            'title'       => $request->title,
            'description' => $request->description,
            'created_by'  => auth()->id(),
        ]);

        AuditLogService::created('Track', $track);

        if ($request->filled('course_ids')) {
            $courses = [];
            foreach ($request->course_ids as $index => $courseId) {
                $courses[$courseId] = ['order' => $index + 1];
            }
            $track->courses()->attach($courses);
        }

        CacheService::clearTracks();

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

        AuditLogService::updated('Track', $track, $request->validated());

        if ($request->filled('course_ids')) {
            $courses = [];
            foreach ($request->course_ids as $index => $courseId) {
                $courses[$courseId] = ['order' => $index + 1];
            }
            $track->courses()->sync($courses);
        }

        CacheService::clearTracks();

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
        AuditLogService::deleted('Track', $track);
        CacheService::clearTracks();

        return response()->json([
            'success' => true,
            'message' => 'Track deleted successfully',
        ]);
    }
}