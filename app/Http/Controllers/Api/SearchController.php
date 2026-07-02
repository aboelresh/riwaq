<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Track;
use App\Models\Course;
use App\Models\Topic;
use App\Models\Team;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->query('q', '');

        if (strlen($q) < 2) {
            return response()->json([
                'success' => true,
                'data' => ['tracks' => [], 'courses' => [], 'topics' => [], 'teams' => []],
            ]);
        }

        $tracks = Track::where('title', 'like', "%{$q}%")
            ->select('id', 'title', 'description', 'icon')
            ->limit(5)
            ->get();

        $courses = Course::where('title', 'like', "%{$q}%")
            ->select('id', 'title', 'track_id')
            ->limit(5)
            ->get();

        $topics = Topic::where('title', 'like', "%{$q}%")
            ->select('id', 'title', 'type')
            ->limit(5)
            ->get();

        $teams = Team::where('name', 'like', "%{$q}%")
            ->select('id', 'name', 'description')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => compact('tracks', 'courses', 'topics', 'teams'),
        ]);
    }
}
