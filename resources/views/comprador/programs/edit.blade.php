{{-- resources/views/comprador/programs/edit.blade.php --}}
@extends('layouts.comprador')

@section('title', 'Editar Programação de Compra')
@section('page-title', 'Editar Programação')
@section('page-subtitle', 'Alterar detalhes da programação de compra')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Ops!</strong> Verifique os erros abaixo.
        </div>
    @endif

    @php
        $req = $program->requisition;
        $client = $req?->client;
    @endphp

    <div class="mb-3">
        <a href="{{ route('comprador.programs.show', $program->id) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <form action="{{ route('comprador.programs.update', $program->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <h6 class="text-muted mb-1">Código da programação</h6>
                        <p class="mb-0 fw-semibold">{{ $program->code }}</p>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted mb-1">Requisição</h6>
                        <p class="mb-0 fw-semibold">
                            {{ $req?->code ?? '—' }}
                        </p>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted mb-1">Status atual</h6>
                        <span class="badge bg-warning-subtle text-warning">
                            {{ ucfirst($program->status) }}
                        </span>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted mb-1">Data da programação</h6>
                        <p class="mb-0">{{ $program->created_at?->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <hr>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Responsável pela programação</label>
                        <input type="text" class="form-control form-control-sm" value="{{ $program->buyer_name }}"
                            disabled>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Telefone</label>
                        <input type="text" name="buyer_phone"
                            class="form-control form-control-sm @error('buyer_phone') is-invalid @enderror"
                            value="{{ old('buyer_phone', $program->buyer_phone) }}">
                        @error('buyer_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="buyer_email"
                            class="form-control form-control-sm @error('buyer_email') is-invalid @enderror"
                            value="{{ old('buyer_email', $program->buyer_email) }}">
                        @error('buyer_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Observações da programação</label>
                    <textarea name="buyer_notes" rows="2"
                        class="form-control form-control-sm @error('buyer_notes') is-invalid @enderror">{{ old('buyer_notes', $program->notes) }}</textarea>
                    @error('buyer_notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

        {{-- ITENS --}}
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
                                <th>Obs</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($program->items as $idx => $item)
                                @php
                                    $reqItem = $item->requisitionItem;
                                    $product = $reqItem?->product;
                                    $unit = $product?->unit;
                                    $qty = $reqItem?->requested_quantity ?? 0;
                                    $rowTotal = $item->budget_total_value;
                                    $totalGeral += $rowTotal;
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

                                        <input type="hidden" name="items[{{ $idx }}][id]"
                                            value="{{ $item->id }}">
                                        <input type="hidden" name="items[{{ $idx }}][requisition_item_id]"
                                            value="{{ $item->requisition_item_id }}">
                                    </td>
                                    <td class="text-center">{{ $qty }}</td>
                                    <td>{{ $unit?->symbol ?? ($unit?->name ?? '—') }}</td>
                                    <td>
                                        <input type="text" name="items[{{ $idx }}][payment_method]"
                                            class="form-control form-control-sm @error("items.$idx.payment_method") is-invalid @enderror"
                                            value="{{ old("items.$idx.payment_method", $item->payment_method) }}">
                                        @error("items.$idx.payment_method")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="text" name="items[{{ $idx }}][supplier_name]"
                                            class="form-control form-control-sm @error("items.$idx.supplier_name") is-invalid @enderror"
                                            value="{{ old("items.$idx.supplier_name", $item->supplier_name) }}">
                                        @error("items.$idx.supplier_name")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="text-end">
                                        <input type="number" step="0.01" min="0"
                                            name="items[{{ $idx }}][budget_unit_value]"
                                            class="form-control form-control-sm text-end @error("items.$idx.budget_unit_value") is-invalid @enderror"
                                            value="{{ old("items.$idx.budget_unit_value", $item->budget_unit_value) }}">
                                        @error("items.$idx.budget_unit_value")
                                            <div class="invalid-feedback text-start">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($rowTotal, 2, ',', '.') }}
                                    </td>
                                    <td>
                                        <input type="text" name="items[{{ $idx }}][notes]"
                                            class="form-control form-control-sm @error("items.$idx.notes") is-invalid @enderror"
                                            value="{{ old("items.$idx.notes", $item->notes) }}">
                                        @error("items.$idx.notes")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        @if ($program->items->count() > 0)
                            <tfoot>
                                <tr class="table-light">
                                    <th colspan="6" class="text-end">Total Geral (atual):</th>
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

    </form>

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('comprador.programs.show', $program->id) }}" class="btn btn-sm btn-secondary">
            Cancelar
        </a>
        <button type="submit" class="btn btn-sm btn-primary">
            <i class="bi bi-check-circle"></i> Guardar Alterações
        </button>
    </div>
@endsection
