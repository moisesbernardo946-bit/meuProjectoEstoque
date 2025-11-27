<?php

namespace App\Http\Controllers\Diretor;

use App\Http\Controllers\Controller;

use App\Models\Requisition;
use App\Models\RequisitionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DiretorRequisitionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        $statusFilter = $request->get('status');
        $search = $request->get('search');

        $query = Requisition::with(['client', 'user'])
            ->where('company_id', $company->id)
            ->orderByDesc('created_at');

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('requester_name', 'like', "%{$search}%");
            });
        }

        $requisitions = $query->paginate(15);

        // Para filtros de status na tela
        $availableStatuses = [
            'pendente' => 'Pendente',
            'aprovado' => 'Aprovado',
            'parcial' => 'Parcial',
            'rejeitado' => 'Rejeitado',
            'em curso' => 'Em curso',
            'concluido' => 'Concluído',
        ];

        return view('diretor.requisitions.index', compact(
            'requisitions',
            'company',
            'availableStatuses',
            'statusFilter',
            'search'
        ));
    }

    /**
     * Detalhe de uma requisição para o diretor.
     */
    public function show($id)
    {
        $user = auth()->user();
        $company = $user->company;

        $requisition = Requisition::with([
            'items.product.unit',
            'client',
            'user',
            'company',
        ])
            ->where('company_id', $company->id)
            ->findOrFail($id);

        // Regra: diretor só pode ir para tela de aprovação se estiver em estado "pendente"
        $canApprove = $requisition->status === 'pendente';

        return view('diretor.requisitions.show', compact(
            'requisition',
            'company',
            'canApprove'
        ));
    }

    /**
     * Diretor decide sobre a requisição:
     * - Aprovar tudo
     * - Rejeitar tudo
     * - Aprovar a requisição, mas rejeitar alguns itens (status "parcial")
     */
    public function approvalForm($id)
    {
        $user = auth()->user();
        $company = $user->company;

        $requisition = Requisition::with([
            'items.product.unit',
            'client',
            'company',
        ])
            ->where('company_id', $company->id)
            ->findOrFail($id);

        // Regras extras:
        // diretor não pode mais modificar requisição com status:
        // concluido, em curso, aprovado, parcial, rejeitado
        if (
            in_array($requisition->status, [
                'concluido',
                'em curso',
                'aprovado',
                'parcial',
                'rejeitado',
            ])
        ) {
            return redirect()
                ->route('diretor.requisitions.show', $requisition->id ?? $id)
                ->with('warning', 'Esta requisição não pode ser mais alterada pelo diretor.');
        }

        return view('diretor.requisitions.approve', compact(
            'requisition',
            'company'
        ));
    }

    /**
     * Processa aprovação/rejeição da requisição pelo diretor.
     *
     * Regras:
     * - Botão "Rejeitar Requisição":
     *     -> requisition.status = 'rejeitado'
     *     -> todos itens.item_status = 'rejeitado'
     * - Botão "Aprovar Requisição" (action=approve):
     *     - Se TODOS itens marcados OU NENHUM marcado:
     *         -> requisition.status = 'aprovado'
     *         -> todos itens.item_status = 'aprovado'
     *     - Se ALGUNS marcados e outros não:
     *         -> requisition.status = 'parcial'
     *         -> marcados -> item_status = 'aprovado'
     *         -> não marcados -> item_status = 'rejeitado'
     */
    public function approvalStore(Request $request, $id)
    {
        $user = auth()->user();
        $company = $user->company;

        $requisition = Requisition::with(['items'])
            ->where('company_id', $company->id)
            ->findOrFail($id);

        // Mesma trava do formulário: não permite alterar certos status
        if (
            in_array($requisition->status, [
                'concluido',
                'em curso',
                'aprovado',
                'parcial',
                'rejeitado',
            ])
        ) {
            return redirect()
                ->route('diretor.requisitions.index', $requisition->id)
                ->with('warning', 'Esta requisição não pode ser mais alterada pelo diretor.');
        }

        $data = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer', 'exists:requisition_items,id'],
            'items.*.approved' => ['nullable', 'boolean'], // checkbox
            'items.*.rejection_reason' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($data, $requisition) {

            $itemsInput = $data['items'];
            $action = $data['action'];

            // Mapeia itens da requisição por ID para fácil acesso
            $itemsById = $requisition->items->keyBy('id');

            // ========== CASO 1: REJEITAR REQUISIÇÃO INTEIRA ==========
            if ($action === 'reject') {

                foreach ($itemsInput as $itemInput) {
                    $itemId = (int) $itemInput['id'];

                    /** @var \App\Models\RequisitionItem|null $item */
                    $item = $itemsById->get($itemId);
                    if (!$item) {
                        continue;
                    }

                    $item->item_status = 'rejeitado';
                    $item->rejection_reason = $itemInput['rejection_reason'] ?? null;
                    $item->save();
                }

                $requisition->status = 'rejeitado';
                $requisition->save();

                return;
            }

            // ========== CASO 2: APROVAR (OU PARCIAL) ==========
            // Conta quantos itens foram marcados como approved
            $totalItens = count($itemsInput);
            $aprovadosCount = 0;

            foreach ($itemsInput as $itemInput) {
                if (!empty($itemInput['approved'])) {
                    $aprovadosCount++;
                }
            }

            // Se TODOS marcados OU NENHUM marcado -> tudo APROVADO
            $todosMarcados = ($aprovadosCount === $totalItens);
            $nenhumMarcado = ($aprovadosCount === 0);

            if ($todosMarcados || $nenhumMarcado) {

                foreach ($itemsInput as $itemInput) {
                    $itemId = (int) $itemInput['id'];

                    /** @var \App\Models\RequisitionItem|null $item */
                    $item = $itemsById->get($itemId);
                    if (!$item) {
                        continue;
                    }

                    $item->item_status = 'aprovado';
                    // opcional: pode limpar motivo de rejeição
                    $item->rejection_reason = null;
                    $item->save();
                }

                $requisition->status = 'aprovado';
                $requisition->save();

                return;
            }

            // ========== CASO 3: APROVAÇÃO PARCIAL ==========
            // Alguns marcados, outros não -> parcial
            foreach ($itemsInput as $itemInput) {
                $itemId = (int) $itemInput['id'];

                /** @var \App\Models\RequisitionItem|null $item */
                $item = $itemsById->get($itemId);
                if (!$item) {
                    continue;
                }

                $isApproved = !empty($itemInput['approved']);

                if ($isApproved) {
                    $item->item_status = 'aprovado';
                    $item->rejection_reason = null; // se quiser, limpa
                } else {
                    $item->item_status = 'rejeitado';
                    $item->rejection_reason = $itemInput['rejection_reason'] ?? null;
                }

                $item->save();
            }

            $requisition->status = 'parcial';
            $requisition->save();
        });

        return redirect()
            ->route('diretor.requisitions.index', $requisition->id)
            ->with('success', 'Requisição analisada com sucesso.');
    }
}
