<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;

class ResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Check if the response is a JsonResponse
        if ($response instanceof JsonResponse) {
            $originalData = $response->getData(true);

            // Format the response
            $formattedResponse = [
                'status' => true, // Or other logic to determine success
                'message' => $originalData['message'] ?? 'Success', // Default message if not provided
                'data' => $originalData['data'] ?? $originalData, // Default to original data if not structured
            ];

            // return response()->json($formattedResponse, $response->getStatusCode());
        }

        return $response;
    }
}
