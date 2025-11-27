<?php

namespace App\Http\Controllers\Motorista;

use App\Http\Controllers\Controller;
use App\Models\PurchaseProgram;

class MotoristaController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $company = $user->company;

        $query = PurchaseProgram::query();

        if ($company) {
            $query->where('company_id', $company->id);
        }

        // Motorista trabalha em cima de programações aprovadas / parciais / concluídas
        $approvedCount = PurchaseProgram::where('status', 'aprovado')->count();
        $partialCount = PurchaseProgram::where('status', 'parcial')->count();
        $completedCount = PurchaseProgram::where('status', 'concluido')->count();

        $latestPrograms = PurchaseProgram::with(['requisition', 'company'])
            ->whereIn('status', ['aprovado', 'parcial', 'concluido'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('motorista.dashboard.index', compact(
            'approvedCount',
            'partialCount',
            'completedCount',
            'latestPrograms'
        ));
    }
}
