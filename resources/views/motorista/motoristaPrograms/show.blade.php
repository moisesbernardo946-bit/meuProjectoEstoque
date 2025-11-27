{{-- resources/views/motorista/show.blade.php --}}
@extends('layouts.motorista')

@section('title', 'Detalhes da Programação')
@section('page-title', 'Detalhes da Programação')
@section('page-subtitle', 'Visualização e conclusão dos itens da programação')

@section('page-actions')
    <div class="btn-group">
        <a href="{{ route('motorista.motoristaPrograms.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>

        {{-- Exportar PDF --}}
        <a href="{{ route('motorista.motoristaPrograms.export.pdf', $program->id) }}" class="btn btn-sm btn-outline-danger">
            <i class="bi bi-file-earmark-pdf"></i> PDF
        </a>

        {{-- Exportar Excel --}}
        <a href="{{ route('motorista.motoristaPrograms.export.excel', $program->id) }}"
            class="btn btn-sm btn-outline-success">
            <i class="bi bi-file-earmark-excel"></i> Excel
        </a>
    </div>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show">
            {{ session('info') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- CABEÇALHO DA PROGRAMAÇÃO --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between flex-wrap">
                <div>
                    <h5 class="fw-bold mb-1">
                        Programação {{ $program->code }}
                    </h5>
                    <div class="text-muted small">
                        Requisição:
                        <strong>{{ $program->requisition?->code ?? '—' }}</strong>
                        @if ($program->requisition?->client)
                            | Cliente:
                            <strong>{{ $program->requisition->client->name }}</strong>
                        @endif
                    </div>
                    <div class="text-muted small mt-1">
                        Empresa:
                        <strong>{{ $program->company?->name ?? '—' }}</strong><br>
                        Criada em: {{ $program->created_at?->format('d/m/Y H:i') }}
                    </div>
                </div>
                <div class="text-end">
                    @php
                        $cls = 'secondary';
                        $label = $program->status;

                        switch ($program->status) {
                            case 'aprovado':
                                $cls = 'primary';
                                $label = 'Aprovado (Diretor)';
                                break;
                            case 'parcial':
                                $cls = 'warning';
                                $label = 'Parcial (Motorista)';
                                break;
                            case 'concluido':
                                $cls = 'success';
                                $label = 'Concluído (Motorista)';
                                break;
                        }
                    @endphp
                    <span class="badge bg-{{ $cls }} mb-1">{{ $label }}</span>

                    <div class="small text-muted">
                        Comprador:
                        <strong>{{ $program->buyer_name ?? ($program->buyer?->name ?? '—') }}</strong><br>
                        Valor orçado:
                        <strong>{{ number_format($program->total_budget_value ?? 0, 2, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ITENS DA PROGRAMAÇÃO + FORM PARA CONCLUSÃO --}}
<div class="card shadow-sm mb-3">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <strong>Itens da Programação</strong>
        <small class="text-muted">Marque apenas os itens entregues/concluídos.</small>
    </div>

    <form method="POST" action="{{ route('motorista.motoristaPrograms.conclude', $program->id) }}">
        @csrf

        <div class="card-body p-0">
            @if ($program->items->count() === 0)
                <p class="text-muted m-3 mb-0">
                    Nenhum item encontrado nesta programação.
                </p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;"></th>
                                <th>Produto</th>
                                <th class="text-end">Qtd</th>
                                <th>Unidade</th>
                                <th>Forma de Pagamento</th>
                                <th>Fornecedor</th>
                                <th class="text-end">Valor Orçado (un)</th>
                                <th class="text-end">Total Orçado</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($program->items as $item)
                                @php
                                    $reqItem = $item->requisitionItem;
                                    $qty = $reqItem?->requested_quantity ?? 0;
                                    $unitSymbol = $item->product?->unit?->symbol
                                        ?? $item->product?->unit?->abbreviation
                                        ?? $item->product?->unit?->name
                                        ?? '—';

                                    $unitValue = $item->budget_unit_value ?? 0;
                                    $totalValue = $item->budget_total_value ?? ($qty * $unitValue);

                                    $itemStatusClass = 'secondary';
                                    $itemStatusLabel = $item->status;

                                    switch ($item->status) {
                                        case 'faltando':
                                            $itemStatusClass = 'warning';
                                            $itemStatusLabel = 'Faltando';
                                            break;
                                        case 'concluido':
                                            $itemStatusClass = 'success';
                                            $itemStatusLabel = 'Concluído';
                                            break;
                                        case 'aprovado':
                                            $itemStatusClass = 'primary';
                                            $itemStatusLabel = 'Aprovado';
                                            break;
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        @if (in_array($item->status, ['faltando', 'aprovado']))
                                            <input type="checkbox" name="items[]" value="{{ $item->id }}">
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $item->product?->name ?? '—' }}</strong>
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($qty, 2, ',', '.') }}
                                    </td>
                                    <td>
                                        {{ $unitSymbol }}
                                    </td>
                                    <td>
                                        {{ $item->payment_method ?? '—' }}
                                    </td>
                                    <td>
                                        {{ $item->supplier_name ?? '—' }}
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($unitValue, 2, ',', '.') }}
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($totalValue, 2, ',', '.') }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $itemStatusClass }}">
                                            {{ $itemStatusLabel }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if ($program->items->count() > 0 && in_array($program->status, ['aprovado', 'parcial']))
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Se nenhum item for marcado e você clicar em "Concluir Programação", todos serão considerados
                    concluídos.
                </small>

                <button type="submit" class="btn btn-success btn-sm">
                    <i class="bi bi-check2-circle"></i> Concluir Programação
                </button>
            </div>
        @else
            <div class="card-footer">
                <small class="text-muted">
                    Programação já concluída. Nenhuma alteração pode ser feita.
                </small>
            </div>
        @endif
    </form>
</div>

    {{-- ANEXOS DO MOTORISTA --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <strong>Anexos da Programação</strong>

            @if ($program->status === 'concluido')
                <span class="badge bg-success">Programação concluída - anexos somente leitura</span>
            @else
                <small class="text-muted">Motorista pode enviar comprovantes, guias, etc.</small>
            @endif
        </div>
        <div class="card-body">
            {{-- Upload - SOMENTE SE NÃO ESTIVER CONCLUÍDO --}}
            @if ($program->status !== 'concluido')
                <form action="{{ route('motorista.motoristaPrograms.attachments.upload', $program->id) }}" method="POST"
                    enctype="multipart/form-data" class="row g-2 mb-3">
                    @csrf

                    <div class="col-md-8">
                        <input type="file" name="files[]" class="form-control form-control-sm" multiple required>
                        @error('files')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        @error('files.*')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 text-md-end">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-cloud-upload"></i> Enviar Anexo
                        </button>
                    </div>
                </form>
            @endif

            {{-- Lista de anexos --}}
            @if ($program->attachments->count() === 0)
                <p class="text-muted mb-0">
                    Nenhum anexo enviado até o momento.
                </p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Arquivo</th>
                                <th>Enviado em</th>
                                <th class="text-end" style="width: 150px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($program->attachments as $attachment)
                                <tr>
                                    <td>
                                        <i class="bi bi-paperclip"></i>
                                        {{ $attachment->original_name }}
                                    </td>
                                    <td>
                                        {{ $attachment->created_at?->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="text-end">
                                        {{-- Download sempre permitido --}}
                                        <a href="{{ route('motorista.motoristaPrograms.attachments.download', $attachment->id) }}"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-download"></i>
                                        </a>

                                        {{-- Deletar APENAS SE NÃO CONCLUÍDO --}}
                                        @if ($program->status !== 'concluido')
                                            <form
                                                action="{{ route('motorista.motoristaPrograms.attachments.delete', $attachment->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Deseja realmente remover este anexo?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
