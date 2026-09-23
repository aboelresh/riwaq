<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Jobs\SendVerificationEmailJob;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class RegisterController extends Controller
{
    #[OA\Post(
        path: '/auth/register',
        summary: 'Register a new learner account',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'name',                  type: 'string',  example: 'Ahmed Mohamed'),
                    new OA\Property(property: 'username',              type: 'string',  example: 'ahmed_dev', nullable: true),
                    new OA\Property(property: 'email',                 type: 'string',  format: 'email'),
                    new OA\Property(property: 'password',              type: 'string',  format: 'password', minLength: 8),
                    new OA\Property(property: 'password_confirmation', type: 'string',  format: 'password'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Registered successfully - check email for verification code'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function register(RegisterRequest $request): JsonResponse
    {
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'name'                         => $request->name,
            'username'                     => $request->username,
            'email'                        => $request->email,
            'password'                     => Hash::make($request->password),
            'role'                         => UserRole::Learner->value,
            'verification_code'            => $verificationCode,
            'verification_code_expires_at' => now()->addMinutes(15),
        ]);

        SendVerificationEmailJob::dispatch($user, $verificationCode);

        \App\Services\NotificationService::send(
            userId: $user->id,
            type:   'welcome',
            title:  'Welcome to Code Master!',
            body:   "Hi {$user->name}! Start by taking the assessment quiz.",
            data:   ['url' => '/assessment']
        );

        $token = auth('api')->login($user);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful! Check your email for verification code.',
            'data'    => [
                'user'       => new UserResource($user),
                'token'      => $token,
                'token_type' => 'bearer',
            ],
        ], 201);
    }
}