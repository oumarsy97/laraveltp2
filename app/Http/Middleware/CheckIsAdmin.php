<?php

namespace App\Http\Middleware;

use App\Enums\ResponseStatus;
use App\Traits\ApiResponser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIsAdmin
{
    use ApiResponser;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (auth()->user() && auth()->user()->role->libelle === 'ADMIN') {
            return $next($request);
        }

        return $this->sendResponse(null, 'non autorisé',Response::HTTP_FORBIDDEN,ResponseStatus::ECHEC);
    }
}
