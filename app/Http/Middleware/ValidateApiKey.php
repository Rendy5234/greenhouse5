<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    /**
     * Handle incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');

        if (empty($apiKey)) {
            return response()->json([
                'status' => 'error',
                'message' => 'API key tidak ditemukan',
            ], 401);
        }

        $validKey = config('app.api_key');

        if ($apiKey !== $validKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'API key tidak valid',
            ], 403);
        }

        return $next($request);
    }
}
