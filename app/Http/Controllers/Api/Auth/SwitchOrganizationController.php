<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SwitchOrganizationController extends Controller
{
    public function switch(Request $request): JsonResponse
    {
        $request->validate([
            'organization_id' => 'required|integer|exists:organizations,id',
        ]);

        $user = auth()->user();
        $org  = Organization::find($request->organization_id);

        // Verify membership — JWT org_id ≠ authorization
        $membership = $org->users()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$membership) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of this organization.',
            ], 403);
        }

        if (!$org->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This organization is inactive.',
            ], 403);
        }

        // Issue new token scoped to the selected organization
        $token = auth('api')->claims(['org_id' => $org->id])->login($user);

        return response()->json([
            'success' => true,
            'message' => 'Switched to ' . $org->name,
            'data'    => [
                'user'         => new UserResource($user),
                'token'        => $token,
                'token_type'   => 'bearer',
                'organization' => [
                    'id'   => $org->id,
                    'name' => $org->name,
                    'slug' => $org->slug,
                    'role' => $membership->pivot->role,
                ],
            ],
        ]);
    }
}