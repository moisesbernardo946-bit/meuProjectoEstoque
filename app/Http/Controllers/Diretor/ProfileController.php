<?php

namespace App\Http\Controllers\Diretor;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = auth()->user();
        $company = $user->company;
        $group = $company?->group;

        return view('diretor.profile.index', [
            'user' => $user,
            'company' => $company,
            'group' => $group,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $user->update($validated);

        return back()->with('success', 'Perfil atualizado com sucesso.');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error_password', 'A senha atual não confere.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success_password', 'Senha alterada com sucesso.');
    }
}
