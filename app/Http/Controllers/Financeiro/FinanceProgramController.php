<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\PurchaseProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceProgramController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        // financeiro SEMPRE vê apenas aprovadas
        $search = $request->get('search');

        $query = PurchaseProgram::with(['requisition', 'buyer', 'company'])
            ->where('status', 'aprovado'); // <- regra fixa

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('requisition', function ($sq) use ($search) {
                        $sq->where('code', 'like', "%{$search}%");
                    })
                    ->orWhere('buyer_name', 'like', "%{$search}%");
            });
        }

        $programs = $query
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // não preciso mais de $status na view
        return view('financeiro.programs.index', compact(
            'programs',
            'search',
            'company'
        ));
    }

    public function show(PurchaseProgram $program)
    {
        $program->load([
            'company',
            'requisition.client',
            'requisition.user',
            'items.product.unit',
            'buyer',
            'attachments',
        ]);

        $user = auth()->user();
        $company = $user->company;

        return view('financeiro.programs.show', compact('program', 'company'));
    }
}
