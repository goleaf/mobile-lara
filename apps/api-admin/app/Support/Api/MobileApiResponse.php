<?php

namespace App\Support\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

final class MobileApiResponse
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $meta
     */
    public static function success(array $data = [], array $meta = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => self::meta($meta),
        ], $status);
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public static function error(
        string $code,
        string $message,
        string $category,
        string $nextAction,
        int $status,
        array $meta = [],
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
                'category' => $category,
                'next_action' => $nextAction,
            ],
            'meta' => self::meta($meta),
        ], $status);
    }

    /**
     * @param  array<string, mixed>  $meta
     * @return array<string, mixed>
     */
    private static function meta(array $meta): array
    {
        return [
            'api_version' => 'v1',
            'server_time' => Carbon::now()->toIso8601String(),
            ...$meta,
        ];
    }
}
