<?php

// app/Http/Controllers/Financeiro/FinancialMovementController.php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\CostCenter;
use App\Models\FinancialMovement;
use Illuminate\Http\Request;

class FinancialMovementController extends Controller
{
    /**
     * Lista de movimentos (pode deixar simples por enquanto)
     */
    public function index(Request $request)
    {
        $query = FinancialMovement::with('costCenter')
            ->orderByDesc('movement_date')
            ->orderByDesc('id');

        // filtro opcional por centro de custo
        if ($request->filled('cost_center_id')) {
            $query->where('cost_center_id', $request->get('cost_center_id'));
        }

        // filtro opcional por tipo
        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        $movements = $query->paginate(15);

        $costCenters = CostCenter::orderBy('name')->get();

        return view('financeiro.financial_movements.index', compact('movements', 'costCenters'));
    }

    /**
     * Formulário de criação
     */
    public function create()
    {
        $costCenters = CostCenter::orderBy('name')->get();

        return view('financeiro.financial_movements.create', compact('costCenters'));
    }

    /**
     * Salvar novo movimento financeiro
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cost_center_id' => ['required', 'exists:cost_centers,id'],
            'type' => ['required', 'in:receita,custo,despesa'],
            'movement_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
        ]);

        FinancialMovement::create($validated);

        return redirect()
            ->route('financeiro.financial_movements.index')
            ->with('success', 'Movimento financeiro lançado com sucesso.');
    }

    /**
     * (Opcional) Edição simples
     */
    public function edit(FinancialMovement $financial_movement)
    {
        $costCenters = CostCenter::orderBy('name')->get();

        return view('financeiro.financial_movements.edit', [
            'movement' => $financial_movement,
            'costCenters' => $costCenters,
        ]);
    }

    public function update(Request $request, FinancialMovement $financial_movement)
    {
        $validated = $request->validate([
            'cost_center_id' => ['required', 'exists:cost_centers,id'],
            'type' => ['required', 'in:receita,custo,despesa'],
            'movement_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
        ]);

        $financial_movement->update($validated);

        return redirect()
            ->route('financeiro.financial_movements.index')
            ->with('success', 'Movimento financeiro atualizado com sucesso.');
    }

    /**
     * Excluir (se quiser permitir)
     */
    public function destroy(FinancialMovement $financial_movement)
    {
        $financial_movement->delete();

        return redirect()
            ->route('financeiro.financial_movements.index')
            ->with('success', 'Movimento financeiro removido com sucesso.');
    }
}
