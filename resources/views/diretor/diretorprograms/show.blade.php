{{-- resources/views/diretor/programs/show.blade.php --}}
@extends('layouts.diretor')

@section('title', 'Programação ' . $program->code)
@section('page-title', 'Programação de Compra ' . $program->code)
@section('page-subtitle', 'Análise e aprovação do diretor')

@section('content')
    {{-- alertas --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Cabeçalho com status e botão aprovar --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <span class="badge bg-secondary">{{ $program->code }}</span>

            @php
                $cls = 'secondary';
                $label = $program->status;

                switch ($program->status) {
                    case 'pendente':
                        $cls = 'warning';
                        $label = 'Pendente';
                        break;
                    case 'aprovado':
                        $cls = 'success';
                        $label = 'Aprovado';
                        break;
                    case 'concluido':
                        $cls = 'info';
                        $label = 'Concluído';
                        break;
                }
            @endphp
            <span class="badge bg-{{ $cls }}">{{ $label }}</span>

            @if ($program->finance_approved ?? false)
                <span class="badge bg-success">
                    Aprovado Diretor
                    @if ($program->finance_approved_at)
                        ({{ $program->finance_approved_at->format('d/m/Y H:i') }})
                    @endif
                </span>
            @endif
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('diretor.diretorprograms.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>

            @if ($program->status !== 'aprovado')
                <form method="POST" action="{{ route('diretor.diretorprograms.approve', $program->id) }}"
                    onsubmit="return confirm('Tem certeza que deseja aprovar esta programação?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="bi bi-check2-circle"></i> Aprovar programação
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Informações gerais da programação --}}
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-semibold mb-2">Empresa</h6>
                    <p class="mb-1">
                        {{ $program->company?->code }} - {{ $program->company?->name }}
                    </p>
                    <p class="mb-0 text-muted small">
                        Grupo:
                        {{ $program->company?->group?->name ?? '—' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-semibold mb-2">Requisição</h6>
                    <p class="mb-1">
                        Código: {{ $program->requisition?->code ?? '-' }}
                    </p>
                    <p class="mb-0 text-muted small">
                        Solicitante:
                        {{ $program->requisition?->user?->name ?? '-' }}<br>
                        Cliente/Destinatário:
                        {{ $program->requisition?->client?->code }}
                        - {{ $program->requisition?->client?->name ?? 'PRÓPRIA EMPRESA' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-semibold mb-2">Comprador / Programador</h6>
                    <p class="mb-1">
                        {{ $program->buyer_name ?? $program->buyer?->name }}
                    </p>
                    <p class="mb-0 text-muted small">
                        Email: {{ $program->buyer_email ?? $program->buyer?->email }}<br>
                        Telefone: {{ $program->buyer_phone ?? $program->buyer?->phone ?? '—' }}<br>
                        Criado em: {{ $program->created_at?->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Observações gerais --}}
    @if ($program->notes)
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0">Observações da programação</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $program->notes }}</p>
            </div>
        </div>
    @endif

    {{-- Itens da programação --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Itens da programação</h6>
        </div>
        <div class="card-body p-0">
            @php
                $totalGeral = 0;
            @endphp

            @if ($program->items->isEmpty())
                <p class="text-muted text-center my-3">Nenhum item nesta programação.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>#</th>
                                <th>Cód. Produto</th>
                                <th>Descrição</th>
                                <th>Und.</th>
                                <th>Qtd</th>
                                <th>Fornecedor</th>
                                <th>Forma Pagamento</th>
                                <th>Vlr Unit. Orçado</th>
                                <th>Vlr Total Orçado</th>
                                <th>Status Item</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($program->items as $idx => $item)
                                @php
                                    $totalGeral += (float) $item->budget_total_value;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $idx + 1 }}</td>
                                    <td class="text-center">
                                        {{ $item->product?->code ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $item->product?->name ?? '—' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $item->product?->unit?->code ?? '-' }}
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($item->requisitionItem?->requested_quantity ?? 0, 2, ',', '.') }}
                                    </td>
                                    <td>
                                        {{ $item->supplier_name ?? '—' }}
                                    </td>
                                    <td>
                                        {{ $item->payment_method ?? '—' }}
                                    </td>
                                    <td class="text-end">
                                        @if (!is_null($item->budget_unit_value))
                                            {{ number_format($item->budget_unit_value, 2, ',', '.') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if (!is_null($item->budget_total_value))
                                            {{ number_format($item->budget_total_value, 2, ',', '.') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $clsItem = 'secondary';
                                            $lblItem = $item->status ?? '—';

                                            switch ($item->status) {
                                                case 'pendente':
                                                    $clsItem = 'warning';
                                                    $lblItem = 'Pendente';
                                                    break;
                                                case 'aprovado':
                                                    $clsItem = 'success';
                                                    $lblItem = 'Aprovado';
                                                    break;
                                                case 'concluido':
                                                    $clsItem = 'info';
                                                    $lblItem = 'Concluído';
                                                    break;
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $clsItem }}">{{ $lblItem }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="8" class="text-end">Total Geral Orçado:</th>
                                <th class="text-end">
                                    {{ number_format($totalGeral, 2, ',', '.') }}
                                </th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
