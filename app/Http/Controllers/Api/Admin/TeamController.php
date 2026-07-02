<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::with([
            'creator',
            'members.user',
            'members.track'
        ])->get();

        return response()->json([
            'success' => true,
            'data' => $teams
        ]);
    }

    public function show($id)
    {
        $team = Team::with([
            'creator',
            'members.user.courseProgress',
            'members.user.topicProgress',
            'members.user.quizAttempts',
            'members.track'
        ])->findOrFail($id);

        $membersProgress = $team->members->map(function($member) {
            $user = $member->user;
            
            return [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'track' => $member->track,
                'joined_at' => $member->joined_at,
                'stats' => [
                    'courses_completed' => $user->courseProgress()->where('is_completed', true)->count(),
                    'topics_viewed' => $user->topicProgress()->where('is_viewed', true)->count(),
                    'quizzes_passed' => $user->quizAttempts()->where('passed', true)->count(),
                    'total_score' => $user->quizAttempts()->where('passed', true)->sum('score'),
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'team' => $team,
                'members_progress' => $membersProgress
            ]
        ]);
    }
}