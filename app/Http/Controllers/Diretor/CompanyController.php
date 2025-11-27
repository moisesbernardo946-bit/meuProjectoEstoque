<?php

namespace App\Http\Controllers\Diretor;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Lista “a” empresa do diretor (na prática, uma só).
     */
    public function index()
    {
        $user    = auth()->user();
        $company = $user->company;
        $group   = $company?->group;

        // por enquanto, só a própria empresa
        $companies = collect([$company])->filter();

        return view('diretor.companies.index', compact('companies', 'company', 'group'));
    }

    /**
     * Formulário de edição da empresa do diretor.
     */
    public function edit(Company $company)
    {
        $user = auth()->user();

        // impede que o diretor edite empresa de outro
        if ($user->company_id !== $company->id) {
            abort(403, 'Você não tem permissão para editar esta empresa.');
        }

        $group = $company->group;

        return view('diretor.companies.edit', compact('company', 'group'));
    }

    /**
     * Atualiza dados da empresa.
     */
    public function update(Request $request, Company $company)
    {
        $user = auth()->user();

        if ($user->company_id !== $company->id) {
            abort(403, 'Você não tem permissão para editar esta empresa.');
        }

        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'code'    => 'required|string|max:50|unique:companies,code,' . $company->id,
            'nif'     => 'nullable|string|max:100',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $company->update($data);

        return redirect()
            ->route('diretor.companies.index')
            ->with('success', 'Dados da empresa atualizados com sucesso.');
    }
}
