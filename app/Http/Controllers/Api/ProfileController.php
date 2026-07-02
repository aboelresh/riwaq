<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        return response()->json([
            'success' => true,
            'message' => 'Profile retrieved',
            'data' => [
                'user' => auth()->user(),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'goals' => 'nullable|string|max:1000',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists — use raw attribute (not accessor)
            $rawPhoto = $user->getRawOriginal('profile_photo');
            if ($rawPhoto) {
                Storage::disk('public')->delete($rawPhoto);
            }

            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $validated['profile_photo'] = $path;
        }

        $validated['profile_completed'] = true;
        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'user' => $user->fresh(),
            ],
        ]);
    }
}
