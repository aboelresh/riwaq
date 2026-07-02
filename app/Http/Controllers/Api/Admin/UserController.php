<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'learner')->get();

        $usersData = $users->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_photo' => $user->profile_photo,
                'created_at' => $user->created_at,
                'stats' => [
                    'total_tracks' => $user->tracks()->count(),
                    'courses_completed' => $user->courseProgress()->where('is_completed', true)->count(),
                    'topics_viewed' => $user->topicProgress()->where('is_viewed', true)->count(),
                    'quizzes_passed' => $user->quizAttempts()->where('passed', true)->count(),
                    'total_score' => $user->quizAttempts()->where('passed', true)->sum('score'),
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $usersData
        ]);
    }

    public function show($id)
    {
        $user = User::with([
            'tracks.courses',
            'courseProgress.course',
            'topicProgress.topic',
            'quizAttempts.quiz',
            'teams'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }
}