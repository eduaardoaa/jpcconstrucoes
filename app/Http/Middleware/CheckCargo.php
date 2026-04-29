<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCargo
{
    public function handle(Request $request, Closure $next, ...$cargos): Response
    {
        $user = auth()->user();

        if (!$user || !$user->cargo) {
            abort(403, 'Acesso negado');
        }

        if (!in_array($user->cargo->nome, $cargos)) {
            abort(403, 'Você não tem permissão');
        }

        return $next($request);
    }
}