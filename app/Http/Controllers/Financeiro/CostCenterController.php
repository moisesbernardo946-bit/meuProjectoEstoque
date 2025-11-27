<?php

// app/Http/Controllers/Financeiro/CostCenterController.php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\CostCenter;
use App\Models\CompanyGroup;
use App\Models\Company;
use App\Models\Client;
use App\Models\FinancialMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CostCenterController extends Controller
{
    public function index(Request $request)
    {
        // Financeiro enxerga todos
        $query = CostCenter::with(['group', 'company', 'client'])
            ->orderBy('code');

        // Filtros opcionais
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('company_group_id')) {
            $query->where('company_group_id', $request->company_group_id);
        }

        $costCenters = $query->paginate(15);

        $groups = CompanyGroup::orderBy('name')->get();

        return view('financeiro.cost_centers.index', compact('costCenters', 'groups'));
    }

    public function create()
    {
        // Financeiro pode ver todas as empresas e todos os clientes
        $groups = CompanyGroup::with([
            'companies' => function ($q) {
                $q->orderBy('name');
            }
        ])->orderBy('name')->get();

        // Carrega clientes de todas as empresas
        $companies = Company::with('clients')->orderBy('name')->get();

        return view('financeiro.cost_centers.create', compact('groups', 'companies'));
    }

    public function store(Request $request)
    {
        // type: 'empresa' ou 'cliente'
        $validated = $request->validate([
            'type' => ['required', Rule::in(['empresa', 'cliente'])],

            // quando for empresa
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],

            // quando for cliente
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],

            'director_name' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validated['type'] === 'empresa') {
            $company = Company::with('group')->findOrFail($validated['company_id']);

            $code = $company->code; // código do centro = código da empresa filha

            // nome = nome da empresa mãe + nome da empresa filha
            $groupName = $company->group?->name;
            $name = $groupName
                ? $groupName . ' - ' . $company->name
                : $company->name;

            $costCenterData = [
                'company_group_id' => $company->company_group_id,
                'company_id' => $company->id,
                'client_id' => null,
                'code' => $code,
                'name' => $name,
                'type' => 'empresa',
                'director_name' => $validated['director_name'] ?? null,
                'is_active' => true,
            ];
        } else {
            // cliente
            $client = Client::with('company.group')->findOrFail($validated['client_id']);

            $code = $client->code; // código do centro = código do cliente
            $name = $client->name; // nome do centro = nome do cliente

            $company = $client->company;
            $group = $company?->group;

            $costCenterData = [
                'company_group_id' => $group?->id,
                'company_id' => $company?->id,
                'client_id' => $client->id,
                'code' => $code,
                'name' => $name,
                'type' => 'cliente',
                'director_name' => $validated['director_name'] ?? null,
                'is_active' => true,
            ];
        }

        // garantir que não duplica código
        if (CostCenter::where('code', $costCenterData['code'])->exists()) {
            return back()
                ->withInput()
                ->withErrors(['code' => 'Já existe um centro de custo com este código: ' . $costCenterData['code']]);
        }

        CostCenter::create($costCenterData);

        return redirect()
            ->route('financeiro.cost_centers.index')
            ->with('success', 'Centro de custo criado com sucesso.');
    }

    public function show(Request $request, CostCenter $costCenter)
    {
        $costCenter->load(['group', 'company', 'client']);

        // Filtros de período
        $currentYear = now()->year;

        $year = (int) ($request->get('year') ?: $currentYear);
        $month = $request->get('month'); // '01'..'12' ou null

        $query = $costCenter->financialMovements()
            ->whereYear('movement_date', $year);

        if (!empty($month)) {
            $query->whereMonth('movement_date', $month);
        }

        // agrupar por mês para montar visão mensal (caso queira ver o ano todo)
        // mas também vamos calcular o agregado do período selecionado
        $movements = (clone $query)->get();

        $receita = $movements->where('type', 'receita')->sum('amount');
        $custos = $movements->where('type', 'custo')->sum('amount');
        $despesas = $movements->where('type', 'despesa')->sum('amount');

        $mgLiquida = $receita - $custos;
        $resultado = $mgLiquida - $despesas;

        // visão mensal (para a tabela do ano inteiro)
        $monthlySummary = (clone $costCenter->financialMovements())
            ->whereYear('movement_date', $year)
            ->selectRaw('
            MONTH(movement_date) as month,
            SUM(CASE WHEN type = "receita" THEN amount ELSE 0 END) as total_receita,
            SUM(CASE WHEN type = "custo" THEN amount ELSE 0 END) as total_custo,
            SUM(CASE WHEN type = "despesa" THEN amount ELSE 0 END) as total_despesa
        ')
            ->groupByRaw('MONTH(movement_date)')
            ->orderByRaw('MONTH(movement_date)')
            ->get()
            ->map(function ($row) {
                $mg = $row->total_receita - $row->total_custo;
                $res = $mg - $row->total_despesa;

                return [
                    'month' => (int) $row->month,
                    'receita' => (float) $row->total_receita,
                    'custos' => (float) $row->total_custo,
                    'mg_liquida' => (float) $mg,
                    'despesas' => (float) $row->total_despesa,
                    'resultado' => (float) $res,
                ];
            });

        // para o filtro do select de anos
        $availableYears = FinancialMovement::where('cost_center_id', $costCenter->id)
            ->selectRaw('DISTINCT YEAR(movement_date) as year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [$currentYear];
        }

        return view('financeiro.cost_centers.show', compact(
            'costCenter',
            'year',
            'month',
            'receita',
            'custos',
            'despesas',
            'mgLiquida',
            'resultado',
            'monthlySummary',
            'availableYears'
        ));
    }
}
