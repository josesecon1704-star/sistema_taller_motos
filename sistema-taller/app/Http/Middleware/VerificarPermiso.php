<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarPermiso
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $modulo, string $accion): Response
    {
        $user = $request->user();

        if (! $user || ! $user->tienePermiso($modulo, $accion)) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        return $next($request);
    }

}
