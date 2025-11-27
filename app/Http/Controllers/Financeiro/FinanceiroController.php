<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\PurchaseProgram;
use Illuminate\Http\Request;

class FinanceiroController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        // Total de programações por status
        $totalPendentes = PurchaseProgram::where('status', 'pendente')->count();
        $totalAprovadas = PurchaseProgram::where('status', 'aprovado')->count();
        $totalConcluidas = PurchaseProgram::where('status', 'concluido')->count();
        $totalTodas = PurchaseProgram::count();

        // Últimas programações pendentes (pra lista rápida)
        $ultimasPendentes = PurchaseProgram::with(['requisition', 'buyer', 'company'])
            ->where('status', 'pendente')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('financeiro.dashboard.index', compact(
            'company',
            'totalPendentes',
            'totalAprovadas',
            'totalConcluidas',
            'totalTodas',
            'ultimasPendentes'
        ));
    }
}
