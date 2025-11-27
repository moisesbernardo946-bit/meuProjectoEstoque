<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Se o role do usuário NÃO estiver na lista permitida → bloqueia
        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Acesso negado.');
        }

        return $next($request);
    }
}
