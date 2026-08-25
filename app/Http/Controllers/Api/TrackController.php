<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Track;
use App\Models\UserTrack;
use App\Models\UserQuizAttempt;
use App\Services\UnlockService;
use App\Services\ProgressService;
use Illuminate\Http\JsonResponse;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TrackController extends Controller
{
    public function __construct(
        private UnlockService $unlockService,
        private ProgressService $progressService
    ) {}

public function index(Request $request): JsonResponse
{
    $page   = $request->get('page', 1);
    $cacheKey = CacheService::tracksKey($page);

    $data = Cache::remember($cacheKey, CacheService::TTL_TRACKS, function () {
        $tracks = Track::with(['creator', 'courses'])->paginate(12);

        return [
            'data' => TrackResource::collection($tracks->items())->resolve(),
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

    public function enroll($id)
    {
        $user = auth()->user();
        $track = Track::findOrFail($id);

        // Already enrolled in this track?
        $existing = UserTrack::where('user_id', $user->id)
            ->where('track_id', $track->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Already enrolled in this track'
            ], 400);
        }

        $activeTrack = UserTrack::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if ($activeTrack) {
            $activeProgress = $this->getTrackProgress($user->id, $activeTrack->track_id);
            $threshold = $activeTrack->unlock_threshold; 

            if ($activeProgress < $threshold) {
                return response()->json([
                    'success' => false,
                    'message' => "You need to reach {$threshold}% in your current track before enrolling in another. Current progress: {$activeProgress}%"
                ], 400);
            }

            UserTrack::create([
                'user_id' => $user->id,
                'track_id' => $track->id,
                'status' => 'waitlist',
                'started_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Added to waitlist. You can switch to this track from your dashboard.'
            ]);
        }

        UserTrack::create([
            'user_id' => $user->id,
            'track_id' => $track->id,
            'status' => 'active',
            'started_at' => now(),
        ]);

        $this->progressService->initializeTrackProgress($user, $track);
        $this->unlockService->unlockFirstCourseInTrack($user, $track);

        return response()->json([
            'success' => true,
            'message' => 'Enrolled successfully'
        ]);
    }

 
    public function switchTrack($id)
    {
        $user = auth()->user();

        $target = UserTrack::where('user_id', $user->id)
            ->where('track_id', $id)
            ->first();

        if (!$target) {
            return response()->json([
                'success' => false,
                'message' => 'Not enrolled in this track'
            ], 400);
        }

        if ($target->status === 'active') {
            return response()->json([
                'success' => false,
                'message' => 'This track is already active'
            ], 400);
        }

        UserTrack::where('user_id', $user->id)
            ->where('status', 'active')
            ->update(['status' => 'waitlist']);

        $target->update(['status' => 'active']);

        $track = Track::find($id);
        if ($track) {
            $this->progressService->initializeTrackProgress($user, $track);
            $this->unlockService->unlockFirstCourseInTrack($user, $track);
        }

        return response()->json([
            'success' => true,
            'message' => 'Switched to this track'
        ]);
    }

    public function myTracks()
    {
        $user = auth()->user();

        $tracks = $user->tracks()
            ->withPivot('status', 'unlock_threshold', 'started_at', 'completed_at')
            ->with(['courses' => function($query) use ($user) {
                $query->with(['userProgress' => function($q) use ($user) {
                    $q->where('user_id', $user->id);
                }]);
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tracks
        ]);
    }


    private function getTrackProgress($userId, $trackId)
    {
        $track = Track::with('courses.topics')->find($trackId);
        if (!$track) return 0;

        $totalTopics = 0;
        $totalScore = 0;
        $maxScore = 0;

        foreach ($track->courses as $course) {
            $totalTopics += $course->topics->count();
        }

        $quizIds = [];
        foreach ($track->courses as $course) {
            foreach ($course->topics as $topic) {
                $quiz = \App\Models\Quiz::where('topic_id', $topic->id)->first();
                if ($quiz) {
                    $quizIds[] = $quiz->id;
                    $maxScore += $quiz->total_points;
                }
            }
            $courseQuiz = \App\Models\Quiz::where('course_id', $course->id)->first();
            if ($courseQuiz) {
                $quizIds[] = $courseQuiz->id;
                $maxScore += $courseQuiz->total_points;
            }
        }

        if ($maxScore === 0) return 0;

        foreach ($quizIds as $qid) {
            $best = UserQuizAttempt::where('user_id', $userId)
                ->where('quiz_id', $qid)
                ->where('passed', true)
                ->max('score');
            $totalScore += ($best ?? 0);
        }

        return min(100, round(($totalScore / $maxScore) * 100));
    }
}
