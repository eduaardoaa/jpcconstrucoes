<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermissao
{
    public function handle(Request $request, Closure $next, string $chave): Response
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissao($chave)) {
            abort(403, 'Você não tem permissão para acessar este módulo.');
        }

        return $next($request);
    }
}