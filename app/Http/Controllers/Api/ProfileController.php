<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => new UserResource(auth()->user()),
        ]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user   = auth()->user();
        $fields = $request->only(['name', 'username', 'bio', 'goals']);

        // Profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $fields['profile_photo'] = $request->file('profile_photo')
                ->store('profile_photos', 'public');
        }

        // Bug 008 Fix: only mark completed if meaningful fields are present
        if (!empty($fields['name']) && !empty($fields['bio']) && !empty($fields['goals'])) {
            $fields['profile_completed'] = true;
        }

        $user->update($fields);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data'    => new UserResource($user->fresh()),
        ]);
    }
}