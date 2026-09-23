<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class LoginController extends Controller
{
    #[OA\Post(
        path: '/auth/login',
        summary: 'Login and receive JWT token',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email',    type: 'string', format: 'email',    example: 'admin@codemaster.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success',    type: 'boolean', example: true),
                        new OA\Property(property: 'token',      type: 'string'),
                        new OA\Property(property: 'token_type', type: 'string',  example: 'bearer'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Invalid credentials'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::whereRaw('LOWER(email) = ?', [
            strtolower(trim($request->email))
        ])->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $memberships = Organization::whereHas('users', fn($q) =>
            $q->where('user_id', $user->id)->where('status', 'active')
        )->get();

        if (config('app.single_license_mode', false)) {
            $org   = Organization::find(config('app.single_license_organization_id', 1));
            $token = auth('api')->claims(['org_id' => $org?->id])->login($user);
            return $this->tokenResponse($user, $token, $org);
        }

        if ($memberships->count() === 1) {
            $org   = $memberships->first();
            $token = auth('api')->claims(['org_id' => $org->id])->login($user);
            return $this->tokenResponse($user, $token, $org);
        }

        if ($memberships->count() > 1) {
            $token = auth('api')->login($user);
            return response()->json([
                'success'                => true,
                'message'                => 'Please select an organization.',
                'requires_org_selection' => true,
                'data'                   => [
                    'user'          => new UserResource($user),
                    'token'         => $token,
                    'token_type'    => 'bearer',
                    'organizations' => $memberships->map(fn($o) => [
                        'id'   => $o->id,
                        'name' => $o->name,
                        'slug' => $o->slug,
                        'role' => $o->users()->where('user_id', $user->id)->first()?->pivot->role,
                    ]),
                ],
            ]);
        }

        $token = auth('api')->login($user);
        return $this->tokenResponse($user, $token, null);
    }

    private function tokenResponse(User $user, string $token, ?Organization $org): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data'    => [
                'user'         => new UserResource($user),
                'token'        => $token,
                'token_type'   => 'bearer',
                'organization' => $org ? [
                    'id'   => $org->id,
                    'name' => $org->name,
                    'slug' => $org->slug,
                ] : null,
            ],
        ]);
    }
}