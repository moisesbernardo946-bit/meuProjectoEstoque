<?php

namespace App\Http\Controllers\Diretor;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $user    = auth()->user();
        $company = $user->company;

        // Filtro simples por nome/código
        $search = $request->input('search');

        $clientsQuery = Client::where('company_id', $company->id);

        if ($search) {
            $clientsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('nif', 'like', "%{$search}%");
            });
        }

        $clients = $clientsQuery
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        return view('diretor.clientes.index', compact('clients', 'search', 'company'));
    }

    public function create()
    {
        $user    = auth()->user();
        $company = $user->company;

        return view('diretor.clientes.create', compact('company'));
    }

    public function store(Request $request)
    {
        $user    = auth()->user();
        $company = $user->company;

        $data = $request->validate([
            'code'    => 'required|string|max:50|unique:clients,code',
            'name'    => 'required|string|max:255',
            'nif'     => 'nullable|string|max:100',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $data['company_id'] = $company->id;
        $data['is_active']  = $request->boolean('is_active', true);

        Client::create($data);

        return redirect()
            ->route('diretor.clientes.index')
            ->with('success', 'Cliente criado com sucesso.');
    }

    public function edit(Client $client)
    {
        $user    = auth()->user();
        $company = $user->company;

        // Garantir que o diretor só edita cliente da sua empresa
        if ($client->company_id !== $company->id) {
            abort(403, 'Você não tem permissão para editar este cliente.');
        }

        return view('diretor.clientes.edit', compact('client', 'company'));
    }

    public function update(Request $request, Client $client)
    {
        $user    = auth()->user();
        $company = $user->company;

        if ($client->company_id !== $company->id) {
            abort(403, 'Você não tem permissão para editar este cliente.');
        }

        $data = $request->validate([
            'code'    => 'required|string|max:50|unique:clients,code,' . $client->id,
            'name'    => 'required|string|max:255',
            'nif'     => 'nullable|string|max:100',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $client->update($data);

        return redirect()
            ->route('diretor.clientes.index')
            ->with('success', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Client $client)
    {
        $user    = auth()->user();
        $company = $user->company;

        if ($client->company_id !== $company->id) {
            abort(403, 'Você não tem permissão para excluir este cliente.');
        }

        $client->delete();

        return redirect()
            ->route('diretor.clientes.index')
            ->with('success', 'Cliente excluído com sucesso.');
    }
}
