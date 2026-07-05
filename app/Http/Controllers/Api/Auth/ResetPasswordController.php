<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $user = User::whereRaw('LOWER(email) = ?', [
            strtolower(trim($request->email))
        ])->first();

        if (!$user || $user->verification_code !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset code.',
            ], 400);
        }

        if ($user->verification_code_expires_at < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Reset code has expired. Please request a new one.',
            ], 400);
        }

        $user->update([
            'password'                     => Hash::make($request->password),
            'verification_code'            => null,
            'verification_code_expires_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. You can now log in with your new password.',
        ]);
    }
}