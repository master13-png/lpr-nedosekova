<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiFormat
{
    /**
     * @param  array<string, mixed>  $extra
     */
    public static function success(mixed $result, int $status = 200, array $extra = []): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'result' => $result,
            'student' => config('lab.slug'),
            'lesson' => config('lab.code'),
            ...$extra,
        ], $status, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param  array<string, mixed>  $details
     */
    public static function error(string $message, int $status, array $details = []): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'httpCode' => $status,
            'message' => $message,
            'details' => (object) $details,
        ], $status, [], JSON_UNESCAPED_UNICODE);
    }
}
