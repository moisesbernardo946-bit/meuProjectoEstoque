<?php

namespace App\Http\Controllers\Almoxarife;

use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\EntityProduct;
use App\Models\EntryProduct;
use App\Models\ExitProduct;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AlmoxarifeController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        if (!$company) {
            abort(403, 'Nenhuma empresa associada ao usuário.');
        }

        $selectedMonth = $request->input('month', date('m'));
        $selectedYear  = $request->input('year', date('Y'));

        $startDate = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
        $endDate   = Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth();

        // === MÉTRICAS GERAIS (iguais às suas) ===

        $totalProdutos = Product::count();

        $requisicoesQuery = Requisition::where('company_id', $company->id)
            ->whereBetween('created_at', [$startDate, $endDate]);

        $totalRequisicoes     = (clone $requisicoesQuery)->count();
        $requisicoesPendentes = (clone $requisicoesQuery)->where('status', 'pendente')->count();
        $requisicoesAprovadas = (clone $requisicoesQuery)->where('status', 'aprovado')->count();
        $requisicoesParcial= (clone $requisicoesQuery)->where('status', 'parcial')->count();

        $itensRequisicao = RequisitionItem::whereHas('requisition', function ($q) use ($company, $startDate, $endDate) {
            $q->where('company_id', $company->id)
              ->whereBetween('created_at', [$startDate, $endDate]);
        })->get();

        $totalItensSolicitados = $itensRequisicao->sum('requested_quantity');
        $totalItensEntregues   = $itensRequisicao->sum('delivered_quantity');

        $entradas = EntryProduct::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $saidas = ExitProduct::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalEntradas = $entradas->sum('quantity');
        $totalSaidas   = $saidas->sum('quantity');

        // === ESTOQUE POR ENTITY_PRODUCT (empresa do almoxarife) ===

        $estoqueEmpresa = EntityProduct::with('product')
            ->where('entity_type', 'company')
            ->where('entity_id', $company->id)
            ->get();

        $totalProdutosEmpresa   = $estoqueEmpresa->count();
        $totalQuantidadeEmpresa = $estoqueEmpresa->sum('quantity');

        // 👉 Cálculo do "status" em memória (computed_status)
        $estoqueEmpresa = $estoqueEmpresa->map(function ($ep) {
            $qty = (int) ($ep->quantity ?? 0);
            $min = $ep->min_stock;
            $max = $ep->max_stock;

            if ($min !== null && $max !== null) {
                if ($qty < $min) {
                    $ep->computed_status = 'critico';
                } elseif ($qty > $max) {
                    $ep->computed_status = 'excesso';
                } else {
                    $ep->computed_status = 'normal';
                }
            } else {
                // se não tem min/max definidos, vamos considerar "normal"
                $ep->computed_status = 'normal';
            }

            return $ep;
        });

        // 👉 Contagem por status usando o computed_status
        $estoqueStatusCount = [
            'normal'  => $estoqueEmpresa->where('computed_status', 'normal')->count(),
            'critico' => $estoqueEmpresa->where('computed_status', 'critico')->count(),
            'excesso' => $estoqueEmpresa->where('computed_status', 'excesso')->count(),
        ];

        // === TOP 5 produtos que mais saem (usando entityProduct->product) ===

        $topSaidas = ExitProduct::selectRaw('entity_product_id, SUM(quantity) as total_saiu')
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('entity_product_id')
            ->orderByDesc('total_saiu')
            ->with(['entityProduct.product']) // 👈 importante!
            ->limit(5)
            ->get();

        // Últimas 5 requisições
        $ultimasRequisicoes = $requisicoesQuery
            ->with(['user', 'client'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('almoxarife.dashboard.index', compact(
            'company',
            'selectedMonth',
            'selectedYear',
            'totalProdutos',
            'totalRequisicoes',
            'requisicoesPendentes',
            'requisicoesAprovadas',
            'requisicoesParcial',
            'totalItensSolicitados',
            'totalItensEntregues',
            'totalEntradas',
            'totalSaidas',
            'totalProdutosEmpresa',
            'totalQuantidadeEmpresa',
            'estoqueStatusCount',
            'topSaidas',
            'ultimasRequisicoes'
        ));
    }
}
