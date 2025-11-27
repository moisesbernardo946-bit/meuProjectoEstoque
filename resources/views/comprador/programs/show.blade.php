{{-- resources/views/comprador/programs/show.blade.php --}}
@extends('layouts.comprador')

@section('title', 'Detalhes da Programação de Compra')
@section('page-title', 'Programação de Compra')
@section('page-subtitle', 'Detalhes da programação de compra')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $req = $program->requisition;
        $client = $req?->client;
    @endphp

    <div class="mb-3 d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('comprador.programs.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>

        <div class="d-flex gap-2">
            @if ($program->status === 'pendente')
                <a href="{{ route('comprador.programs.edit', $program->id) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-pencil"></i> Editar
                </a>
            @endif
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Código da programação</h6>
                    <p class="mb-0 fw-semibold">{{ $program->code }}</p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Status</h6>
                    <span
                        class="badge
                        @switch($program->status)
                            @case('pendente') bg-warning-subtle text-warning @break
                            @case('aprovado') bg-success-subtle text-success @break
                            @case('rejeitado') bg-danger-subtle text-danger @break
                            @case('concluido') bg-primary-subtle text-primary @break
                            @default bg-secondary
                        @endswitch
                    ">
                        {{ ucfirst($program->status) }}
                    </span>
                </div>

                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Requisição</h6>
                    <p class="mb-0 fw-semibold">
                        {{ $req?->code ?? '—' }}
                    </p>
                    @if ($req)
                        <small class="text-muted">
                            Prioridade: {{ ucfirst($req->priority) }}
                        </small>
                    @endif
                </div>

                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Data da programação</h6>
                    <p class="mb-0">{{ $program->created_at?->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <hr>

            <div class="row g-3">
                <div class="col-md-4">
                    <h6 class="text-muted mb-1">Solicitante</h6>
                    <p class="mb-0">
                        {{ $req?->requester_name ?? '—' }}<br>
                        <small class="text-muted">
                            Setor: {{ $req?->user?->department ?? '—' }}
                        </small>
                    </p>
                </div>

                <div class="col-md-4">
                    <h6 class="text-muted mb-1">Cliente / Destinatário</h6>
                    <p class="mb-0">
                        @if ($client)
                            {{ $client->code }} - {{ $client->name }}
                        @else
                            {{ $req?->company?->code }} - {{ $req?->company?->name }} (PRÓPRIA EMPRESA)
                        @endif
                    </p>
                </div>

                <div class="col-md-4">
                    <h6 class="text-muted mb-1">Responsável pela programação</h6>
                    <p class="mb-0">
                        {{ $program->buyer_name }}<br>
                        <small class="text-muted">
                            Tel: {{ $program->buyer_phone ?? '—' }}<br>
                            Email: {{ $program->buyer_email ?? '—' }}
                        </small>
                    </p>
                </div>
            </div>

            @if ($program->notes)
                <hr>
                <h6 class="text-muted mb-1">Observações da programação</h6>
                <p class="mb-0">{{ $program->notes }}</p>
            @endif
        </div>
    </div>

    {{-- ITENS DA PROGRAMAÇÃO --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-light">
            <strong>Itens da programação</strong>
        </div>
        <div class="card-body p-0">
            @php
                $totalGeral = 0;
            @endphp

            <div class="table-responsive">
                <table class="table table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Produto</th>
                            <th class="text-center">Qtd</th>
                            <th>Unidade</th>
                            <th>Forma de Pagamento</th>
                            <th>Fornecedor</th>
                            <th class="text-end">Valor Orçado (un)</th>
                            <th class="text-end">Total Orçado</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($program->items as $item)
                            @php
                                $reqItem = $item->requisitionItem;
                                $product = $reqItem?->product;
                                $unit = $product?->unit;
                                $qty = $reqItem?->requested_quantity ?? 0;
                                $totalGeral += $item->budget_total_value;
                            @endphp
                            <tr>
                                <td>
                                    @if ($product)
                                        <strong>{{ $product->code }}</strong> - {{ $product->name }}<br>
                                        <small class="text-muted">
                                            Finalidade: {{ $reqItem?->purpose ?? ($req?->purpose ?? '—') }}
                                        </small>
                                    @else
                                        <span class="text-muted">Produto não encontrado</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $qty }}</td>
                                <td>{{ $unit?->symbol ?? ($unit?->name ?? '—') }}</td>
                                <td>{{ $item->payment_method ?? '—' }}</td>
                                <td>{{ $item->supplier_name ?? '—' }}</td>
                                <td class="text-end">
                                    {{ number_format($item->budget_unit_value, 2, ',', '.') }}
                                </td>
                                <td class="text-end">
                                    {{ number_format($item->budget_total_value, 2, ',', '.') }}
                                </td>
                                <td>
                                    <span
                                        class="badge
                                        @switch($item->status)
                                            @case('pendente') bg-warning-subtle text-warning @break
                                            @case('aprovado') bg-success-subtle text-success @break
                                            @case('rejeitado') bg-danger-subtle text-danger @break
                                            @case('concluido') bg-primary-subtle text-primary @break
                                            @case('faltando') bg-secondary-subtle text-secondary @break
                                            @default bg-secondary
                                        @endswitch
                                    ">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                            </tr>
                            @if ($item->notes)
                                <tr>
                                    <td colspan="8">
                                        <small class="text-muted">Obs: {{ $item->notes }}</small>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    Nenhum item encontrado nesta programação.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($program->items->count() > 0)
                        <tfoot>
                            <tr class="table-light">
                                <th colspan="6" class="text-end">Total Geral:</th>
                                <th class="text-end">
                                    {{ number_format($totalGeral, 2, ',', '.') }}
                                </th>
                                <th></th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- ANEXOS --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <strong>Anexos da programação</strong>

            <div class="d-flex gap-2">
                <a href="{{ route('comprador.programs.export.pdf', $program->id) }}" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-file-earmark-pdf"></i> PDF
                </a>

                <a href="{{ route('comprador.programs.export.excel', $program->id) }}"
                    class="btn btn-sm btn-outline-success">
                    <i class="bi bi-file-earmark-excel"></i> Excel
                </a>
            </div>
        </div>

        <div class="card-body">
            @if ($program->attachments->count() === 0)
                <p class="text-muted mb-0">Nenhum anexo nesta programação.</p>
            @else
                <ul class="list-group list-group-flush">
                    @foreach ($program->attachments as $att)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-paperclip me-2"></i>
                                {{ $att->original_name ?? basename($att->path) }}
                            </div>

                            <a href="{{ asset('storage/' . $att->path) }}" target="_blank"
                                class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

@endsection
