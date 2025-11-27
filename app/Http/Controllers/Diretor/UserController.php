<?php

namespace App\Http\Controllers\Diretor;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Lista os usuários da mesma empresa do diretor.
     */
    public function index(Request $request)
    {
        $diretor = auth()->user();
        $company = $diretor->company;

        $search = $request->input('search');
        $role = $request->input('role');

        $query = User::where('company_id', $diretor->company_id);

        // 🔍 Filtro por nome ou email
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // 🎭 Filtro por role
        if (!empty($role)) {
            $query->where('role', $role);
        }

        $users = $query->orderBy('name')
            ->paginate(10)
            ->withQueryString(); // mantém filtros na paginação

        // Para popular o select de roles
        $roles = [
            'diretor' => 'Diretor',
            'almoxarife' => 'Almoxarife',
            'comprador' => 'Comprador',
            'financeiro' => 'Financeiro',
            'contador' => 'Contador',
            'motorista' => 'Motorista',
        ];

        return view('diretor.users.index', compact(
            'users',
            'company',
            'diretor',
            'search',
            'role',
            'roles'
        ));
    }

    /**
     * Formulário de criação de usuário.
     */
    public function create()
    {
        $diretor = auth()->user();
        $company = $diretor->company;

        $roles = [
            'diretor' => 'Diretor',
            'almoxarife' => 'Almoxarife',
            'comprador' => 'Comprador',
            'financeiro' => 'Financeiro',
            'contador' => 'Contador',
            'motorista' => 'Motorista',
        ];

        return view('diretor.users.create', compact('company', 'roles'));
    }

    /**
     * Salva o novo usuário.
     */
    public function store(Request $request)
    {
        $diretor = auth()->user();

        $roles = ['diretor', 'almoxarife', 'comprador', 'financeiro', 'contador', 'motorista'];

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|max:30|unique:users,phone',
            'password' => 'required|string|min:6|confirmed',
            'role' => ['required', Rule::in($roles)],
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['company_id'] = $diretor->company_id;

        User::create($data);

        return redirect()
            ->route('diretor.users.index')
            ->with('success', 'Usuário criado com sucesso.');
    }

    /**
     * Formulário de edição.
     */
    public function edit(User $user)
    {
        $diretor = auth()->user();

        // garantir que é da mesma empresa
        if ($user->company_id !== $diretor->company_id) {
            abort(403, 'Você não tem permissão para editar este usuário.');
        }

        $company = $diretor->company;

        $roles = [
            'diretor' => 'Diretor',
            'almoxarife' => 'Almoxarife',
            'comprador' => 'Comprador',
            'financeiro' => 'Financeiro',
            'contador' => 'Contador',
            'motorista' => 'Motorista',
        ];

        return view('diretor.users.edit', compact('user', 'company', 'roles'));
    }

    /**
     * Atualiza o usuário.
     */
    public function update(Request $request, User $user)
    {
        $diretor = auth()->user();

        if ($user->company_id !== $diretor->company_id) {
            abort(403, 'Você não tem permissão para editar este usuário.');
        }

        $roles = ['diretor', 'almoxarife', 'comprador', 'financeiro', 'contador', 'motorista'];

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => ['required', Rule::in($roles)],
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('diretor.users.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    /**
     * Remove um usuário (opcional).
     */
    public function destroy(User $user)
    {
        $diretor = auth()->user();

        // não permite excluir a si mesmo
        if ($user->id === $diretor->id) {
            return back()->with('error', 'Você não pode excluir o próprio usuário.');
        }

        if ($user->company_id !== $diretor->company_id) {
            abort(403, 'Você não tem permissão para excluir este usuário.');
        }

        $user->delete();

        return redirect()
            ->route('diretor.users.index')
            ->with('success', 'Usuário excluído com sucesso.');
    }
}
