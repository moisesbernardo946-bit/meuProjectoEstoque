@extends('layouts.almoxarife')

@section('title', 'Detalhes da Requisição')
@section('page-title', 'Detalhes da Requisição')
@section('page-subtitle', 'Visualização completa da requisição')

@section('page-actions')
    <a href="{{ route('almoxarife.requisitions.index') }}" class="btn btn-sm btn-secondary">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>

    {{-- Botões PDF / Excel --}}
    <a href="{{ route('almoxarife.requisitions.export.pdf', $requisition->id) }}"
       class="btn btn-sm btn-outline-danger">
        <i class="bi bi-filetype-pdf"></i> PDF
    </a>
    <a href="{{ route('almoxarife.requisitions.export.excel', $requisition->id) }}"
       class="btn btn-sm btn-outline-success">
        <i class="bi bi-file-earmark-excel"></i> Excel
    </a>

    {{-- Botão Editar só se status = pendente --}}
    @if($requisition->status === 'pendente')
        <a href="{{ route('almoxarife.requisitions.edit', $requisition->id) }}"
           class="btn btn-sm btn-primary">
            <i class="bi bi-pencil"></i> Editar
        </a>
    @endif
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 mb-3">
        {{-- CARD PRINCIPAL --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <h5 class="mb-1">
                                Requisição #{{ $requisition->code }}
                            </h5>
                            <div class="small text-muted">
                                Criada em {{ $requisition->created_at->format('d/m/Y H:i') }}
                                por {{ $requisition->user?->name ?? '-' }}
                            </div>
                        </div>
                        <div class="text-end">
                            @php
                                $statusClass = match($requisition->status) {
                                    'aprovado'  => 'success',
                                    'parcial'   => 'info',
                                    'concluido' => 'secondary',
                                    'rejeitado' => 'danger',
                                    'pendente'  => 'warning',
                                    default     => 'secondary',
                                };

                                $priorityClass = match($requisition->priority) {
                                    'baixa'   => 'secondary',
                                    'media'   => 'info',
                                    'alta'    => 'warning',
                                    'urgente' => 'danger',
                                    default   => 'secondary',
                                };
                            @endphp
                            <div>
                                <span class="badge bg-{{ $statusClass }}">
                                    {{ ucfirst($requisition->status) }}
                                </span>
                            </div>
                            <div class="mt-1">
                                <span class="badge bg-{{ $priorityClass }}">
                                    Prioridade: {{ ucfirst($requisition->priority) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <div class="small text-muted">Cliente</div>
                            <div class="fw-semibold">
                                {{ $requisition->client?->code }} - {{ $requisition->client?->name }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Requisitante</div>
                            <div class="fw-semibold">
                                {{ $requisition->requester_name }}
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <div class="small text-muted">Finalidade / Propósito</div>
                            <div>{{ $requisition->purpose ?: '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Observações</div>
                            <div>{{ $requisition->notes ?: '—' }}</div>
                        </div>
                    </div>

                    <hr>

                    {{-- ITENS --}}
                    <h6 class="mb-2">Itens da Requisição</h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Produto</th>
                                <th class="text-end">Qt. Solicitada</th>
                                <th class="text-end">Qt. Entregue</th>
                                <th>Status do Item</th>
                                <th>Motivo da Rejeição</th>
                                <th>Observações</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($requisition->items as $index => $item)
                                @php
                                    $itemStatusClass = match($item->item_status) {
                                        'aprovado'   => 'success',
                                        'parcial'    => 'info',
                                        'concluido'  => 'secondary',
                                        'rejeitado'  => 'danger',
                                        'pendente'   => 'warning',
                                        default      => 'secondary',
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $item->product?->name ?? '—' }}</strong><br>
                                        <small class="text-muted">{{ $item->product?->code ?? '' }}</small>
                                    </td>
                                    <td class="text-end">{{ $item->requested_quantity }}</td>
                                    <td class="text-end">{{ $item->delivered_quantity ?? 0 }}</td>
                                    <td>
                                        <span class="badge bg-{{ $itemStatusClass }}">
                                            {{ $item->item_status ? ucfirst($item->item_status) : '—' }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $item->rejection_reason ?: '—' }}
                                    </td>
                                    <td>
                                        {{ $item->notes ?: '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        Nenhum item cadastrado nesta requisição.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- SE QUISER, AQUI NO FUTURO VOCÊ PODE COLOCAR BOTÃO PARA LANÇAR ENTRADAS
                         BASEADO NO STATUS (aprovada/parcial/etc.)
                    --}}
                </div>
            </div>
        </div>

        {{-- CARD RESUMO RÁPIDO --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-semibold mb-2">Resumo</h6>
                    @php
                        $totalSolicitado = $requisition->items->sum('requested_quantity');
                        $totalEntregue   = $requisition->items->sum('delivered_quantity');
                    @endphp

                    <div class="d-flex justify-content-between small mb-1">
                        <span>Total solicitado:</span>
                        <span class="fw-semibold">{{ $totalSolicitado }}</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Total entregue:</span>
                        <span class="fw-semibold">{{ $totalEntregue }}</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Itens:</span>
                        <span class="fw-semibold">{{ $requisition->items->count() }}</span>
                    </div>
                </div>
            </div>

            {{-- AÇÕES POR STATUS (exibir apenas instruções ou botões futuros) --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-semibold mb-2">Situação da Requisição</h6>
                    <p class="small mb-0">
                        Status atual:
                        <strong class="text-capitalize">{{ $requisition->status }}</strong>
                    </p>

                    @if($requisition->status === 'pendente')
                        <p class="small text-muted mb-0 mt-2">
                            Esta requisição ainda pode ser editada.
                        </p>
                    @elseif(in_array($requisition->status, ['aprovada', 'parcial']))
                        <p class="small text-muted mb-0 mt-2">
                            Requisição aprovada/parcial. Pode haver lançamento de entrada de produtos (quantidades entregues).
                        </p>
                    @elseif($requisition->status === 'concluida')
                        <p class="small text-muted mb-0 mt-2">
                            Requisição concluída. Não são permitidas alterações.
                        </p>
                    @elseif($requisition->status === 'rejeitada')
                        <p class="small text-muted mb-0 mt-2">
                            Requisição rejeitada. Apenas visualização está disponível.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
