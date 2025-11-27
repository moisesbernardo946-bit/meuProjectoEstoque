<?php

namespace App\Http\Controllers\Diretor;

use App\Http\Controllers\Controller;
use App\Models\PurchaseProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiretorProgramController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        $status = $request->get('status', 'todos');
        $search = $request->get('search');

        $query = PurchaseProgram::with(['requisition', 'buyer', 'company']);

        // Financeiro vê tudo, sem filtrar por company_id
        if ($status && $status !== 'todos') {
            $query->where('status', $status);
        }

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

        return view('diretor.diretorprograms.index', compact(
            'programs',
            'status',
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

        return view('diretor.diretorprograms.show', compact('program', 'company'));
    }

    public function approve(PurchaseProgram $program)
    {
        $user = auth()->user();

        // se quiser, pode validar role aqui (financeiro)
        // if (!$user->hasRole('financeiro')) abort(403);

        // se já estiver aprovado, só volta com msg
        if ($program->status === 'aprovado') {
            return redirect()
                ->route('diretor.programs.show', $program->id)
                ->with('info', 'Esta programação já está aprovada.');
        }

        DB::transaction(function () use ($program, $user) {
            // 1) Atualiza status da programação
            $program->status = 'aprovado';
            $program->save();

            // 2) Atualiza todos os itens ligados a essa programação
            $program->items()->update([
                'status' => 'aprovado',
            ]);
        });

        return redirect()
            ->route('diretor.diretorprograms.show', $program->id)
            ->with('success', 'Programação aprovada com sucesso. Todos os itens foram marcados como aprovados.');
    }
}
