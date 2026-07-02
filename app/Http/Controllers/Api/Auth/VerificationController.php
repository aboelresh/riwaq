<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Mail\VerificationCodeMail;
use Illuminate\Support\Facades\Mail;
class VerificationController extends Controller
{
    
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();

        
        if ($user->email_verified_at) {
            return response()->json([
                'success' => true,
                'message' => 'Email already verified'
            ]);
        }

        
        if ($user->verification_code !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code'
            ], 400);
        }

        
        if ($user->verification_code_expires_at < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code expired. Please request a new one.'
            ], 400);
        }

        
        $user->update([
            'email_verified_at' => now(),
            'verification_code' => null,
            'verification_code_expires_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully!'
        ]);
    }

   
    public function resend()
{
    $user = auth()->user();

    if ($user->email_verified_at) {
        return response()->json([
            'success' => true,
            'message' => 'Email already verified'
        ]);
    }

    // Generate new code
    $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    $user->update([
        'verification_code' => $verificationCode,
        'verification_code_expires_at' => now()->addMinutes(15),
    ]);

    // Send email
    try {
        Mail::to($user->email)->send(new VerificationCodeMail($user, $verificationCode));
    } catch (\Exception $e) {
        \Log::error('Failed to send verification email: ' . $e->getMessage());
    }

    return response()->json([
        'success' => true,
        'message' => 'Verification code sent to your email!'
    ]);
}
}