@extends('layouts.almoxarife')

@section('title', 'Receber produtos da requisição')
@section('page-title', 'Receber produtos')
@section('page-subtitle', 'Atualizar quantidades entregues da requisição')

@section('page-actions')
    <a href="{{ route('almoxarife.requisitions.show', $requisition->id) }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Ops!</strong> Verifique os erros abaixo.
            <ul class="mb-0 mt-1 small">
                @foreach($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Cabeçalho da requisição --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between flex-wrap gap-3">
                <div>
                    <div class="small text-muted">Requisição</div>
                    <div class="fw-bold fs-5">{{ $requisition->code }}</div>
                    <div class="small text-muted">
                        Criada em: {{ $requisition->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>

                <div>
                    <div class="small text-muted">Cliente / Empresa</div>
                    <div class="fw-semibold">
                        @if($requisition->client)
                            {{ $requisition->client->name }}
                        @else
                            {{ $company->name ?? '-' }}
                        @endif
                    </div>
                    <div class="small text-muted">
                        Solicitante: {{ $requisition->requester_name }}
                    </div>
                </div>

                <div>
                    <div class="small text-muted">Prioridade</div>
                    @php
                        $priorityClass = match($requisition->priority) {
                            'alta'    => 'danger',
                            'urgente' => 'dark',
                            'media'   => 'warning',
                            default   => 'secondary',
                        };
                    @endphp
                    <span class="badge bg-{{ $priorityClass }}">
                        {{ ucfirst($requisition->priority) }}
                    </span>

                    <div class="mt-2 small text-muted">Status atual</div>
                    @php
                        // status geral da requisição: pendente, aprovado, parcial, rejeitado, em curso, concluido
                        $statusClass = match($requisition->status) {
                            'aprovado'  => 'success',
                            'parcial'   => 'info',
                            'pendente'  => 'secondary',
                            'rejeitado' => 'danger',
                            'em curso'  => 'warning',
                            'concluido' => 'primary',
                            default     => 'secondary',
                        };
                    @endphp
                    <span class="badge bg-{{ $statusClass }}">
                        {{ ucfirst($requisition->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Formulário de recebimento --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold">
                <i class="bi bi-box-arrow-in-down me-1"></i>
                Receber produtos
            </h6>
            <small class="text-muted">
                Informe quanto está sendo entregue agora. Só pode receber itens aprovados ou em curso.
            </small>
        </div>

        <form action="{{ route('almoxarife.requisitions.receive.store', $requisition->id) }}" method="POST">
            @csrf
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Produto</th>
                                <th class="text-end">Qtd. solicitada</th>
                                <th class="text-end">Qtd. já entregue</th>
                                <th class="text-end">Qtd. restante</th>
                                <th class="text-center">Situação do item</th>
                                <th class="text-end" style="width: 150px;">Qtd. a receber agora</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requisition->items as $index => $item)
                                @php
                                    $requested   = (int) ($item->requested_quantity ?? 0);
                                    $delivered   = (int) ($item->delivered_quantity ?? 0);
                                    $restante    = max(0, $requested - $delivered);

                                    $itemStatus  = $item->item_status ?? 'pendente';
                                    // status do item: pendente, aprovado, rejeitado, em curso, concluido
                                    $itemBadgeClass = 'secondary';
                                    $rowClass       = '';

                                    switch ($itemStatus) {
                                        case 'pendente':
                                            $itemBadgeClass = 'secondary';
                                            break;
                                        case 'aprovado':
                                            $itemBadgeClass = 'success';
                                            break;
                                        case 'em curso':
                                            $itemBadgeClass = 'warning text-dark';
                                            $rowClass       = 'table-warning';
                                            break;
                                        case 'concluido':
                                            $itemBadgeClass = 'primary';
                                            $rowClass       = 'table-success';
                                            break;
                                        case 'rejeitado':
                                            $itemBadgeClass = 'danger';
                                            $rowClass       = 'table-danger';
                                            break;
                                    }

                                    // Pode receber apenas se item_status = aprovado ou em curso, e ainda tiver restante
                                    $canReceive = in_array($itemStatus, ['aprovado', 'em curso']) && $restante > 0;
                                @endphp

                                <tr class="{{ $rowClass }}">
                                    <td>
                                        <strong>{{ $item->product?->name ?? '—' }}</strong><br>
                                        <small class="text-muted">ID: {{ $item->product_id }}</small>
                                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                    </td>

                                    <td class="text-end">
                                        {{ number_format($requested) }}
                                    </td>

                                    <td class="text-end">
                                        {{ number_format($delivered) }}
                                    </td>

                                    <td class="text-end">
                                        {{ number_format($restante) }}
                                    </td>

                                    <td class="text-center">
                                        <span class="badge bg-{{ $itemBadgeClass }}">
                                            {{ ucfirst($itemStatus) }}
                                        </span>
                                        @if($itemStatus === 'rejeitado' && $item->rejection_reason)
                                            <div class="small text-muted mt-1">
                                                Motivo: {{ $item->rejection_reason }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="text-end">
                                        @if($canReceive)
                                            <input
                                                type="number"
                                                name="items[{{ $index }}][received_now]"
                                                class="form-control form-control-sm text-end @error("items.$index.received_now") is-invalid @enderror"
                                                min="0"
                                                max="{{ $restante }}"
                                                value="" {{-- NÃO pré-preencher --}}
                                            >
                                            @error("items.$index.received_now")
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        @else
                                            {{-- Não pode receber: concluido ou rejeitado (ou sem restante) --}}
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">
                                        Nenhum item cadastrado para esta requisição.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($requisition->items->isNotEmpty())
                <div class="card-footer bg-white border-0">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Fornecedor (opcional)</label>
                            <input type="text" name="supplier" class="form-control form-control-sm"
                                   value="{{ old('supplier') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Tipo de entrada (opcional)</label>
                            <input type="text" name="type" class="form-control form-control-sm"
                                   value="{{ old('type', 'requisição') }}">
                        </div>
                        <div class="col-md-4 d-flex justify-content-end align-items-end">
                            <button type="submit" class="btn btn-sm btn-success">
                                <i class="bi bi-check2-circle me-1"></i>
                                Confirmar recebimento
                            </button>
                        </div>
                    </div>

                    <div class="small text-muted mt-2">
                        Itens com status <strong>concluído</strong> ou <strong>rejeitado</strong> não permitem entrada e aparecem destacados.
                    </div>
                </div>
            @endif
        </form>
    </div>
@endsection
