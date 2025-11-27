<?php

namespace App\Http\Controllers\Almoxarife;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $zones = Zone::when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('almoxarife.zones.index', compact('zones', 'search'));
    }

    public function create()
    {
        return view('almoxarife.zones.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        Zone::create($data);

        return redirect()
            ->route('almoxarife.zones.index')
            ->with('success', 'Zona criada com sucesso!');
    }

    public function edit(Zone $zone)
    {
        return view('almoxarife.zones.edit', compact('zone'));
    }

    public function update(Request $request, Zone $zone)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $zone->update($data);

        return redirect()
            ->route('almoxarife.zones.index')
            ->with('success', 'Zona atualizada com sucesso!');
    }

    public function destroy(Zone $zone)
    {
        $zone->delete();

        return redirect()
            ->route('almoxarife.zones.index')
            ->with('success', 'Zona removida com sucesso!');
    }
}
