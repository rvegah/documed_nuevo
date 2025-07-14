<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar que el usuario esté autenticado
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder.');
        }

        // Verificar que el usuario sea admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'No tienes permisos de administrador para acceder a esta sección.');
        }

        return $next($request);
    }
}