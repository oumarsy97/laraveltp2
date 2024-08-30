<?php
namespace Laravel\Passport\Http\Middleware;

use App\Enums\ResponseStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiResponser;

class CheckScopes
{
    use ApiResponser;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $scope
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $scope)
    {
        if ($request->user()->tokenCan($scope)) {
            return $next($request);
        }

               return $this->sendResponse(null, 'non autorisé',Response::HTTP_FORBIDDEN,ResponseStatus::ECHEC) ; // Retournez une réponse 403 Forbidden

    }
}
