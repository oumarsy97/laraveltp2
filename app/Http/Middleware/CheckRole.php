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
        if (!$request->user() || !$request->user()->role) {
        return $this->sendResponse(null, 'non autorisé',Response::HTTP_FORBIDDEN,ResponseStatus::ECHEC);
    }

    foreach ($roles as $role) {
        if ($request->user()->can($role)) {
            return $next($request);
        }
    }

    return $this->sendResponse(null, 'non autorisé',Response::HTTP_FORBIDDEN,ResponseStatus::ECHEC);
    }
}
