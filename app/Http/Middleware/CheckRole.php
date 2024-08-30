<?php
namespace App\Http\Middleware;

use App\Enums\ResponseStatus;
use App\Traits\ApiResponser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    use ApiResponser;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string[]  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Vérifier si l'utilisateur est authentifié
        if (!$request->user()) {
            return redirect('/login'); // Redirigez vers la page de login ou retournez une réponse d'erreur
        }

        // Vérifier si l'utilisateur a l'un des rôles requis
        if (!in_array($request->user()->role, $roles)) {
            return $this->sendResponse(null, 'non autorisé',Response ::HTTP_FORBIDDEN,ResponseStatus::ECHEC) ; // Retournez une réponse 403 Forbidden
        }

        return $next($request);
    }
}
