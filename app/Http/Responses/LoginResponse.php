<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->role === 'diretor') {

            if (! $user->company) {
                return redirect()->route('login')
                    ->withErrors(['email' => 'Diretor sem empresa associada.']);
            }

            // Aqui podes usar o "code" da empresa (0001, 0002, etc) ou criar uma coluna "slug"
            $companyCode = $user->company->code;

            return redirect()->route('diretor.dashboard', $companyCode);
        }

        // Outros roles
        return match ($user->role) {
            'almoxarife' => redirect()->route('almoxarife.dashboard'),
            'financeiro' => redirect()->route('financeiro.dashboard'),
            'comprador'  => redirect()->route('comprador.dashboard'),
            'contador'   => redirect()->route('contador.dashboard'),
            'motorista'  => redirect()->route('motorista.dashboard'),
            'requerente'  => redirect()->route('requerente.dashboard'),
            default      => redirect()->route('login'),
        };
    }
}