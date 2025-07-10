<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;

class Handler extends ExceptionHandler
{
    /**
     * Handle unauthenticated exception for API requests.
     */
    protected function unauthenticated($request, AuthenticationException $exception): JsonResponse
    {
            \Log::info('✅ دخلنا فعلاً إلى unauthenticated في Handler!'); // يسجل في ملف اللوج
        return response()->json([
            'message' => __('auth.unauthenticated'),
        ], 401);
    }

    // هنا يبقى باقي الكلاس (render, report ...) كما هو في ExceptionHandler
}
