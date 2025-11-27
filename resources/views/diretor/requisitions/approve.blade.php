@extends('layouts.diretor')

@section('title', 'Analisar Requisição')
@section('page-title', 'Analisar Requisição')
@section('page-subtitle', 'Aprovação ou rejeição de produtos da requisição')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Ops!</strong> Verifique os erros abaixo.
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="mb-3">
                <h5 class="mb-1">
                    Requisição: {{ $requisition->code }} /
                    Status atual: <span class="badge bg-secondary">{{ strtoupper($requisition->status) }}</span>
                </h5>
                <div class="small text-muted">
                    Cliente/Destinatário:
                    @if ($requisition->client)
                        {{ $requisition->client->code }} - {{ $requisition->client->name }}
                    @else
                        {{ $requisition->company->code ?? 'EMP' }} - {{ $requisition->company->name }} (PRÓPRIA EMPRESA)
                    @endif
                    <br>
                    Requisitante: {{ $requisition->requester_name }}
                    <br>
                    Prioridade: <strong>{{ strtoupper($requisition->priority) }}</strong>
                </div>
            </div>

            <hr>

            <form action="{{ route('diretor.requisitions.approval.store', $requisition->id) }}" method="POST" id="approval-form">
                @csrf

                {{-- campo escondido para saber qual ação o diretor clicou --}}
                <input type="hidden" name="action" id="action-field" value="">

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Itens da requisição</h6>

                    <div class="d-flex gap-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="check-all">
                            <label class="form-check-label" for="check-all">Selecionar tudo</label>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%; text-align:center;">Sel.</th>
                                <th style="width: 40%;">Produto</th>
                                <th style="width: 10%; text-align:center;">Unid.</th>
                                <th style="width: 10%; text-align:center;">Qtd solicitada</th>
                                <th style="width: 10%; text-align:center;">Qtd entregue</th>
                                <th style="width: 10%; text-align:center;">Status item</th>
                                <th style="width: 15%;">Motivo rejeição (opcional)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($requisition->items as $idx => $item)
                                <tr>
                                    <td class="text-center">
                                        {{-- Checkbox para marcar se o item será aprovado --}}
                                        <input type="checkbox"
                                               name="items[{{ $idx }}][approved]"
                                               value="1"
                                               class="form-check-input item-checkbox"
                                               {{ old("items.$idx.approved", $item->item_status === 'aprovado' ? 'checked' : '') }}>
                                    </td>
                                    <td>
                                        <input type="hidden" name="items[{{ $idx }}][id]" value="{{ $item->id }}">
                                        <div class="fw-bold" style="font-size: 0.9rem;">
                                            {{ $item->product->code ?? '' }} - {{ $item->product->name ?? '' }}
                                        </div>
                                        <div class="text-muted small">
                                            {{ $item->notes }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        {{ $item->product->unit->abbreviation ?? '' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $item->requested_quantity }}
                                    </td>
                                    <td class="text-center">
                                        {{ $item->delivered_quantity }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge
                                            @if($item->item_status === 'aprovado') bg-success
                                            @elseif($item->item_status === 'rejeitado') bg-danger
                                            @elseif($item->item_status === 'pendente') bg-secondary
                                            @elseif($item->item_status === 'em curso') bg-warning text-dark
                                            @else bg-secondary
                                            @endif
                                        ">
                                            {{ strtoupper($item->item_status ?? 'pendente') }}
                                        </span>
                                    </td>
                                    <td>
                                        <input type="text"
                                               name="items[{{ $idx }}][rejection_reason]"
                                               class="form-control form-control-sm"
                                               value="{{ old("items.$idx.rejection_reason", $item->rejection_reason) }}"
                                               placeholder="Motivo se rejeitar este item">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex justify-content-end gap-2">
                    <a href="{{ route('diretor.requisitions.index') }}" class="btn btn-sm btn-secondary">
                        Voltar
                    </a>

                    {{-- Botão REJEITAR REQUISIÇÃO (tudo rejeitado) --}}
                    <button type="button" class="btn btn-sm btn-danger" id="btn-reject-all">
                        <i class="bi bi-x-circle"></i> Rejeitar Requisição
                    </button>

                    {{-- Botão APROVAR REQUISIÇÃO (aprovado/parcial conforme checkboxes) --}}
                    <button type="button" class="btn btn-sm btn-success" id="btn-approve">
                        <i class="bi bi-check-circle"></i> Aprovar Requisição
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkAll = document.getElementById('check-all');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        const form = document.getElementById('approval-form');
        const actionField = document.getElementById('action-field');
        const btnApprove = document.getElementById('btn-approve');
        const btnRejectAll = document.getElementById('btn-reject-all');

        // Selecionar / deselecionar todos
        if (checkAll) {
            checkAll.addEventListener('change', function () {
                itemCheckboxes.forEach(cb => {
                    cb.checked = checkAll.checked;
                });
            });
        }

        // Botão APROVAR REQUISIÇÃO
        if (btnApprove) {
            btnApprove.addEventListener('click', function () {
                actionField.value = 'approve'; // vamos usar isto no controller
                form.submit();
            });
        }

        // Botão REJEITAR REQUISIÇÃO (tudo rejeitado)
        if (btnRejectAll) {
            btnRejectAll.addEventListener('click', function () {
                if (confirm('Tem certeza que deseja rejeitar a requisição inteira? Todos os itens serão rejeitados.')) {
                    actionField.value = 'reject';
                    form.submit();
                }
            });
        }
    });
</script>
@endpush
