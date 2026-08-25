<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class ForgotPasswordController extends Controller
{
    public function send(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::whereRaw('LOWER(email) = ?', [
            strtolower(trim($request->email))
        ])->first();


        if (!$user) {
            return response()->json([
                'success' => true,
                'message' => 'If this email is registered, you will receive a reset code shortly.',
            ]);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'verification_code'            => $code,
            'verification_code_expires_at' => now()->addMinutes(15),
        ]);

        \App\Jobs\SendPasswordResetEmailJob::dispatch($user, $code);

        return response()->json([
            'success' => true,
            'message' => 'If this email is registered, you will receive a reset code shortly.',
        ]);
    }
}