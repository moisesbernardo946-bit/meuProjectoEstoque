<?php

namespace App\Http\Controllers\Comprador;

use App\Http\Controllers\Controller;
use App\Models\Requisition;
use Illuminate\Http\Request;

class CompradorRequisitionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        $statusFilter = $request->get('status');
        $search = $request->get('search');

        $query = Requisition::with(['client', 'user'])
            ->where('company_id', $company->id)
            ->whereIn('status', ['aprovado', 'parcial'])
            // NÃO mostrar requisições que já tenham programação de compra
            ->whereDoesntHave('purchasePrograms')
            ->orderByDesc('created_at');

        if ($statusFilter && in_array($statusFilter, ['aprovado', 'parcial'])) {
            $query->where('status', $statusFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('requester_name', 'like', "%{$search}%");
            });
        }

        $requisitions = $query->paginate(15);

        return view('comprador.requisitions.index', compact(
            'requisitions',
            'company',
            'statusFilter',
            'search'
        ));
    }
}
