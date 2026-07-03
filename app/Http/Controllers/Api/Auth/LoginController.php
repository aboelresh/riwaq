<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        // Case-insensitive email lookup — works on both SQLite and MySQL
        // regardless of collation. This also handles existing seeded data
        // that was stored with mixed-case emails.
        $user = User::whereRaw('LOWER(email) = ?', [
            strtolower(trim($request->email))
        ])->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = auth('api')->login($user);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data'    => [
                'user'       => new UserResource($user),
                'token'      => $token,
                'token_type' => 'bearer',
            ],
        ]);
    }
}