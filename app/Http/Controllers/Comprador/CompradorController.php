<?php

namespace App\Http\Controllers\Comprador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseProgram;

class CompradorController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        $status = $request->get('status');
        $search = $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = PurchaseProgram::with(['requisition', 'buyer', 'items'])
            ->where('company_id', $company->id)
            ->orderByDesc('created_at');

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('requisition', function ($qr) use ($search) {
                        $qr->where('code', 'like', "%{$search}%")
                            ->orWhere('requester_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $programs = $query->withCount('items')->paginate(15);

        $stats = [
            'pendente' => PurchaseProgram::where('company_id', $company->id)->where('status', 'pendente')->count(),
            'aprovado' => PurchaseProgram::where('company_id', $company->id)->where('status', 'aprovado')->count(),
            'concluido' => PurchaseProgram::where('company_id', $company->id)->where('status', 'concluido')->count(),
            'total' => PurchaseProgram::where('company_id', $company->id)->count(),
        ];

        return view('comprador.dashboard.index', compact('company', 'programs', 'stats'));
    }
}
