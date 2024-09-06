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
        // Obtenir la réponse de la prochaine étape de la requête
        $response = $next($request);

        // Vérifie si la réponse est un JsonResponse
        if ($response instanceof JsonResponse) {
            $originalData = $response->getData(true); // Récupère les données JSON sous forme de tableau

            // Extraire les données, message et statut de la réponse
            $data = $originalData['data'] ?? [];
            $message = $originalData['message'] ?? 'Success';
            $status = $originalData['status'] ?? true;

            // Formater la nouvelle réponse
            $formattedResponse = [
                'status' => $status,
                'message' => $message,
                'data' => $data,
            ];

            // Retourner la réponse formatée avec le même code de statut
            return response()->json($formattedResponse, $response->getStatusCode());
        }

        // Retourne la réponse inchangée si ce n'est pas une JsonResponse
        return $response;
    }
}
