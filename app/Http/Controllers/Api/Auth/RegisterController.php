<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Mail\VerificationCodeMail;
use Illuminate\Support\Facades\Mail;
class RegisterController extends Controller
{
    public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'username' => 'nullable|string|min:3|max:30|unique:users|regex:/^[a-zA-Z0-9_]+$/',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    // Generate 6-digit verification code
    $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    $user = User::create([
        'name' => $request->name,
        'username' => $request->username,
        'email' => strtolower(trim($request->email)),
        'password' => Hash::make($request->password),
        'role' => 'learner',
        'verification_code' => $verificationCode,
        'verification_code_expires_at' => now()->addMinutes(15),
    ]);

    // Send verification email
    try {
        Mail::to($user->email)->send(new VerificationCodeMail($user, $verificationCode));
    } catch (\Exception $e) {
        \Log::error('Failed to send verification email: ' . $e->getMessage());
    }

    $token = auth()->login($user);

    return response()->json([
        'success' => true,
        'message' => 'Registration successful! Check your email for verification code.',
        'data' => [
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
        ]
    ], 201);
}
}