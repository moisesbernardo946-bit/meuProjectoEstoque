@extends('layouts.diretor')

@section('title', 'Detalhe da Requisição')
@section('page-title', 'Detalhe da Requisição')
@section('page-subtitle', 'Visualização da requisição')

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-sm">
            {{ session('warning') }}
        </div>
    @endif

    <div class="mb-3">
        <a href="{{ route('diretor.requisitions.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>

        @if ($canApprove)
            <a href="{{ route('diretor.requisitions.approval.form', $requisition->id) }}"
               class="btn btn-sm btn-success">
                <i class="bi bi-check2-square"></i> Aprovar / Rejeitar
            </a>
        @endif
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            {{-- Cabeçalho --}}
            <div class="row mb-2">
                <div class="col-md-4">
                    <h6 class="mb-1">Requisição</h6>
                    <div><strong>Código:</strong> {{ $requisition->code }}</div>
                    <div><strong>Data:</strong> {{ $requisition->created_at?->format('d/m/Y H:i') }}</div>
                </div>

                <div class="col-md-4">
                    <h6 class="mb-1">Cliente / Destinatário</h6>
                    @if ($requisition->client)
                        <div>{{ $requisition->client->code }} - {{ $requisition->client->name }}</div>
                    @else
                        <div>{{ $company->code ?? 'EMP' }} - {{ $company->name }} (PRÓPRIA EMPRESA)</div>
                    @endif
                    <div><strong>Requisitante:</strong> {{ $requisition->requester_name }}</div>
                </div>

                <div class="col-md-4">
                    <h6 class="mb-1">Status</h6>
                    <div class="mb-1">
                        @php $status = $requisition->status; @endphp
                        <span class="badge
                            @switch($status)
                                @case('pendente') bg-secondary @break
                                @case('aprovado') bg-success @break
                                @case('parcial') bg-warning text-dark @break
                                @case('rejeitado') bg-danger @break
                                @case('em curso') bg-info @break
                                @case('concluido') bg-primary @break
                                @default bg-secondary
                            @endswitch
                        ">
                            {{ ucfirst($status) }}
                        </span>
                    </div>
                    <div>
                        <strong>Prioridade:</strong>
                        @php
                            $priorityLabels = [
                                'baixa' => 'Baixa',
                                'media' => 'Média',
                                'alta' => 'Alta',
                                'urgente' => 'Urgente',
                            ];
                        @endphp
                        {{ $priorityLabels[$requisition->priority] ?? ucfirst($requisition->priority) }}
                    </div>
                </div>
            </div>

            {{-- Propósito / Observações --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Finalidade / Propósito:</strong><br>
                    {{ $requisition->purpose ?? '—' }}
                </div>
                <div class="col-md-6">
                    <strong>Observações:</strong><br>
                    {{ $requisition->notes ?? '—' }}
                </div>
            </div>

            <hr>

            {{-- Itens --}}
            <h6>Itens da requisição</h6>

            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Produto</th>
                            <th>Unidade</th>
                            <th class="text-end">Qtd solicitada</th>
                            <th class="text-end">Qtd entregue</th>
                            <th>Status item</th>
                            <th>Motivo rejeição</th>
                            <th>Observações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requisition->items as $idx => $item)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    @if ($item->product)
                                        <strong>{{ $item->product->code }}</strong> - {{ $item->product->name }}
                                    @else
                                        Produto #{{ $item->product_id }}
                                    @endif
                                </td>
                                <td>
                                    {{ $item->product->unit->abbreviation ?? $item->product->unit->name ?? '—' }}
                                </td>
                                <td class="text-end">{{ $item->requested_quantity }}</td>
                                <td class="text-end">{{ $item->delivered_quantity }}</td>
                                <td>
                                    @php $istatus = $item->item_status; @endphp
                                    <span class="badge
                                        @switch($istatus)
                                            @case('pendente') bg-secondary @break
                                            @case('aprovado') bg-success @break
                                            @case('parcial') bg-warning text-dark @break
                                            @case('rejeitado') bg-danger @break
                                            @case('em curso') bg-info @break
                                            @case('concluido') bg-primary @break
                                            @default bg-secondary
                                        @endswitch
                                    ">
                                        {{ ucfirst($istatus) }}
                                    </span>
                                </td>
                                <td>{{ $item->rejection_reason ?? '—' }}</td>
                                <td>{{ $item->notes ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    Nenhum item nessa requisição.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($canApprove)
                <div class="mt-3">
                    <a href="{{ route('diretor.requisitions.approval.form', $requisition->id) }}"
                       class="btn btn-sm btn-success">
                        <i class="bi bi-check2-square"></i> Aprovar / Rejeitar
                    </a>
                </div>
            @endif
        </div>
    </div>

@endsection
