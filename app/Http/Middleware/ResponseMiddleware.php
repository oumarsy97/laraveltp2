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
            $message = $originalData['message'] ?? 'Success';
            $status = $originalData['status'] ?? true;

            // Format the response
            $formattedResponse = [
                'status' => $status,
                'message' => $message,
                'data' => $originalData['data'] ?? [],
            ];
             return response()->json($formattedResponse, $response->getStatusCode());
        }

        return $response;
    }
}
