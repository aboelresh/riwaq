<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class ApiExceptionHandler
{
    public static function register(Exceptions $exceptions): void
    {
        $exceptions->shouldRenderJsonWhen(function ($request, Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->render(function (ValidationException $e, $request) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        });

        $exceptions->render(function (AuthenticationException $e, $request) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        });

        $exceptions->render(function (AuthorizationException $e, $request) {
            return response()->json([
                'success' => false,
                'message' => 'This action is unauthorized.',
            ], 403);
        });

        $exceptions->render(function (ModelNotFoundException $e, $request) {
            return response()->json([
                'success' => false,
                'message' => 'Resource not found.',
            ], 404);
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) {
            return response()->json([
                'success' => false,
                'message' => 'Endpoint not found.',
            ], 404);
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, $request) {
            return response()->json([
                'success' => false,
                'message' => 'Method not allowed.',
            ], 405);
        });

        $exceptions->render(function (ThrottleRequestsException $e, $request) {
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please slow down and try again shortly.',
            ], 429);
        });

        $exceptions->render(function (TokenExpiredException $e, $request) {
            return response()->json([
                'success' => false,
                'message' => 'Token has expired.',
            ], 401);
        });

        $exceptions->render(function (TokenInvalidException $e, $request) {
            return response()->json([
                'success' => false,
                'message' => 'Token is invalid.',
            ], 401);
        });

        $exceptions->render(function (JWTException $e, $request) {
            return response()->json([
                'success' => false,
                'message' => 'Token error. Please log in again.',
            ], 401);
        });

        $exceptions->render(function (Throwable $e, $request) {
            Log::error($e->getMessage(), [
                'exception' => get_class($e),
                'url'       => $request->fullUrl(),
                'trace'     => $e->getTraceAsString(),
            ]);

            if (config('app.debug')) {
                return response()->json([
                    'success'   => false,
                    'message'   => $e->getMessage(),
                    'exception' => get_class($e),
                ], 500);
            }

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong on our end. Please try again later.',
            ], 500);
        });
    }
}