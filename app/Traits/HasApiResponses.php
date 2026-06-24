<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use function Laravel\Prompts\alert;

trait HasApiResponses
{
    /**
     * Return a standardized success JSON response.
     */
    protected function successResponse(string $message, mixed $data = [], int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'alert'   => 'msg: ' . $message
        ], $statusCode);
    }

    /**
     * Return a standardized error JSON response (Replaces your manual 404 block).
     */
    protected function errorResponse(string $message, int $statusCode = 404): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $statusCode);
    }
}