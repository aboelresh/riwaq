<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TrackResource;
use App\Models\Track;
use App\Models\UserTrack;
use App\Services\CacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use OpenApi\Attributes as OA;

class TrackController extends Controller
{
    #[OA\Get(
        path: '/tracks',
        summary: 'List all tracks - public, paginated, cached 1h',
        tags: ['Tracks'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated tracks list'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $page     = $request->get('page', 1);
        $cacheKey = CacheService::tracksKey($page);

        $data = Cache::remember($cacheKey, CacheService::TTL_TRACKS, function () {
            $tracks = Track::with(['creator', 'courses'])->paginate(12);

            return [
                'data' => collect($tracks->items())->map(fn($track) => [
                    'id'          => $track->id,
                    'title'       => $track->title,
                    'description' => $track->description,
                    'created_by'  => $track->created_by,
                    'created_at'  => $track->created_at?->toDateTimeString(),
                ])->toArray(),
                'meta' => [
                    'current_page' => $tracks->currentPage(),
                    'per_page'     => $tracks->perPage(),
                    'total'        => $tracks->total(),
                    'last_page'    => $tracks->lastPage(),
                ],
            ];
        });

        return response()->json(['success' => true] + $data);
    }

    #[OA\Get(
        path: '/tracks/{id}',
        summary: 'Show track details with courses',
        tags: ['Tracks'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Track with nested courses'),
            new OA\Response(response: 404, description: 'Track not found'),
        ]
    )]
    public function show($id): JsonResponse
    {
        $cacheKey = CacheService::trackKey($id);

        $track = Cache::remember($cacheKey, CacheService::TTL_TRACKS, function () use ($id) {
            return Track::with(['courses.topics'])->findOrFail($id);
        });

        return response()->json([
            'success' => true,
            'data'    => new TrackResource($track),
        ]);
    }

    #[OA\Post(
        path: '/tracks/{id}/enroll',
        summary: 'Enroll in a track',
        tags: ['Tracks'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Enrolled successfully'),
            new OA\Response(response: 400, description: 'Already enrolled or threshold not met'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function enroll($id): JsonResponse
    {
        $user  = auth()->user();
        $track = Track::findOrFail($id);

        if ($user->tracks()->where('track_id', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Already enrolled in this track.',
            ], 400);
        }

        $activeTrack = $user->tracks()->where('status', 'active')->first();
        if ($activeTrack && $activeTrack->id !== (int) $id) {
            $activeProgress = UserTrack::where('user_id', $user->id)
                ->where('track_id', $activeTrack->id)->first();

            if (!$activeProgress || $activeProgress->progress_percentage < 25) {
                return response()->json([
                    'success' => false,
                    'message' => 'Complete at least 25% of your current track first.',
                ], 400);
            }
        }

        UserTrack::create([
            'user_id'  => $user->id,
            'track_id' => $id,
            'status'   => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully enrolled in: ' . $track->title,
        ]);
    }

    #[OA\Get(
        path: '/tracks/my-tracks',
        summary: 'Get enrolled tracks for authenticated user',
        tags: ['Tracks'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Enrolled tracks with progress'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function myTracks(): JsonResponse
    {
        $user   = auth()->user();
        $tracks = $user->tracks()
            ->withPivot('status', 'progress_percentage', 'started_at', 'completed_at')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $tracks,
        ]);
    }
}