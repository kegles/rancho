<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OrgAuth
{
    public function handle(Request $request, Closure $next)
    {
        //NUNCA bloqueie as rotas de login/logout
        if ($request->routeIs('admin.login', 'admin.login.post', 'admin.logout')) {
            return $next($request);
        }

        // Já autenticado?
        if ($request->session()->get('org.auth') === true) {
            return $next($request);
        }

        // Não autenticado → manda para login
        return redirect()->route('admin.login');
    }
}
