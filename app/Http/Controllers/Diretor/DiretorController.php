<?php

namespace App\Http\Controllers\Diretor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Client;
use App\Models\Company;
use App\Models\Requisition;
use App\Models\User;

class DiretorController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Empresa filha à qual o diretor pertence
        $company = $user->company ?? null;

        // Grupo (empresa mãe)
        $group   = $company?->group ?? null;

        // Se por algum motivo o usuário não tiver empresa ligada,
        // evita erro e mostra tudo como 0
        if (!$company) {
            return view('diretor.dashboard.index', [
                'user'                       => $user,
                'company'                    => null,
                'group'                      => null,
                'totalClientes'              => 0,
                'totalUsers'                 => 0,
                'totalRequisicoes'           => 0,
                'totalRequisicoesPendentes'  => 0,
                'ultimasRequisicoes'         => collect(),
            ]);
        }

        // 🔹 Total de clientes dessa empresa
        $totalClientes = Client::where('company_id', $company->id)->count();

        // 🔹 Total de usuários dessa empresa (qualquer role)
        $totalUsers = User::where('company_id', $company->id)->count();

        // 🔹 Total de requisições da empresa
        $totalRequisicoes = Requisition::where('company_id', $company->id)->count();

        // 🔹 Total de requisições pendentes de aprovação
        $totalRequisicoesPendentes = Requisition::where('company_id', $company->id)
            ->where('status', 'pendente')
            ->count();

        // 🔹 Últimas requisições (ex: 5 mais recentes)
        $ultimasRequisicoes = Requisition::with(['client', 'user'])
            ->where('company_id', $company->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('diretor.dashboard.index', [
            'user'                      => $user,
            'company'                   => $company,
            'group'                     => $group,
            'totalClientes'             => $totalClientes,
            'totalUsers'                => $totalUsers,
            'totalRequisicoes'          => $totalRequisicoes,
            'totalRequisicoesPendentes' => $totalRequisicoesPendentes,
            'ultimasRequisicoes'        => $ultimasRequisicoes,
        ]);
    }
}
