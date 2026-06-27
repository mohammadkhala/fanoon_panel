<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiResponse
{
    /**
     * Success response.
     */
    public static function success(mixed $data = null, string $message = '', int $httpCode = 200): JsonResponse
    {
        $payload = ['success' => true];
        if ($message !== '') {
            $payload['message'] = $message;
        }
        if ($data !== null) {
            $payload['data'] = $data;
        }
        return response()->json($payload, $httpCode);
    }

    /**
     * Error response.
     */
    public static function error(array $errors, string $message = '', int $httpCode = 400): JsonResponse
    {
        $payload = ['success' => false, 'errors' => $errors];
        if ($message !== '') {
            $payload['message'] = $message;
        }
        return response()->json($payload, $httpCode);
    }

    /**
     * Paginated response.
     */
    public static function paginated(LengthAwarePaginator $paginator, string $dataKey = 'data'): JsonResponse
    {
        return response()->json([
            'success' => true,
            $dataKey => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ], 200);
    }
}
