<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Mail\VerificationCodeMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function send(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::whereRaw('LOWER(email) = ?', [
            strtolower(trim($request->email))
        ])->first();

        // Always return 200 even if email not found — prevents user enumeration
        // (attacker can't know if an email is registered or not)
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

        try {
            Mail::to($user->email)->send(new VerificationCodeMail($user, $code));
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'If this email is registered, you will receive a reset code shortly.',
        ]);
    }
}