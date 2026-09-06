<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        // Case-insensitive email lookup (Bug002 fix)
        $user = User::whereRaw('LOWER(email) = ?', [
            strtolower(trim($request->email))
        ])->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Resolve user's organizations
        $memberships = Organization::whereHas('users', fn($q) =>
            $q->where('user_id', $user->id)->where('status', 'active')
        )->get();

        // Single License Mode — always org #1
        if (config('app.single_license_mode', false)) {
            $org   = Organization::find(config('app.single_license_organization_id', 1));
            $token = auth('api')->claims(['org_id' => $org?->id])->login($user);

            return $this->tokenResponse($user, $token, $org, $memberships);
        }

        // Single organization — auto-select
        if ($memberships->count() === 1) {
            $org   = $memberships->first();
            $token = auth('api')->claims(['org_id' => $org->id])->login($user);

            return $this->tokenResponse($user, $token, $org, $memberships);
        }

        // Multiple organizations — issue token WITHOUT org_id
        // Client must call POST /auth/switch-organization to get scoped token
        if ($memberships->count() > 1) {
            $token = auth('api')->login($user);

            return response()->json([
                'success'       => true,
                'message'       => 'Login successful. Please select an organization.',
                'requires_org_selection' => true,
                'data'          => [
                    'user'          => new UserResource($user),
                    'token'         => $token,
                    'token_type'    => 'bearer',
                    'organizations' => $memberships->map(fn($o) => [
                        'id'   => $o->id,
                        'name' => $o->name,
                        'slug' => $o->slug,
                        'logo' => $o->logo,
                        'role' => $o->users()
                                    ->where('user_id', $user->id)
                                    ->first()?->pivot->role,
                    ]),
                ],
            ]);
        }

        // No organization membership — system admin or unassigned user
        $token = auth('api')->login($user);

        return $this->tokenResponse($user, $token, null, $memberships);
    }

    private function tokenResponse(User $user, string $token, ?Organization $org, $memberships): JsonResponse
    {
        return response()->json([
            'success'    => true,
            'message'    => 'Login successful',
            'data'       => [
                'user'          => new UserResource($user),
                'token'         => $token,
                'token_type'    => 'bearer',
                'organization'  => $org ? [
                    'id'   => $org->id,
                    'name' => $org->name,
                    'slug' => $org->slug,
                ] : null,
            ],
        ]);
    }
}