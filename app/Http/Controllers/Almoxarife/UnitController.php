<?php

namespace App\Http\Controllers\Almoxarife;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $units = Unit::when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('symbol', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('almoxarife.units.index', compact('units', 'search'));
    }

    public function create()
    {
        return view('almoxarife.units.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'symbol' => ['nullable', 'string', 'max:10'],
        ]);

        Unit::create($data);

        return redirect()
            ->route('almoxarife.units.index')
            ->with('success', 'Unidade criada com sucesso!');
    }

    public function edit(Unit $unit)
    {
        return view('almoxarife.units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'symbol' => ['nullable', 'string', 'max:10'],
        ]);

        $unit->update($data);

        return redirect()
            ->route('almoxarife.units.index')
            ->with('success', 'Unidade atualizada com sucesso!');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();

        return redirect()
            ->route('almoxarife.units.index')
            ->with('success', 'Unidade removida com sucesso!');
    }
}
