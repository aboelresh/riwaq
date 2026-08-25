<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Mail\VerificationCodeMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VerificationController extends Controller
{
    public function verify(VerifyEmailRequest $request): JsonResponse
    {
        $user = auth()->user();

        if ($user->email_verified_at) {
            return response()->json([
                'success' => true,
                'message' => 'Email already verified.',
            ]);
        }

        if ($user->verification_code !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code.',
            ], 400);
        }

        if ($user->verification_code_expires_at < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code expired. Please request a new one.',
            ], 400);
        }

        $user->update([
            'email_verified_at'            => now(),
            'verification_code'            => null,
            'verification_code_expires_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully!',
        ]);
    }

    public function resend(): JsonResponse
    {
        $user = auth()->user();

        if ($user->email_verified_at) {
            return response()->json([
                'success' => true,
                'message' => 'Email already verified.',
            ]);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'verification_code'            => $code,
            'verification_code_expires_at' => now()->addMinutes(15),
        ]);

        \App\Jobs\SendVerificationEmailJob::dispatch($user, $code);

        return response()->json([
            'success' => true,
            'message' => 'Verification code sent to your email.',
        ]);
    }
}