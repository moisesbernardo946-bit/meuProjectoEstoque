@extends('layouts.almoxarife')

@section('title', 'Editar Requisição')
@section('page-title', 'Editar Requisição')
@section('page-subtitle', 'Alteração de requisição pendente')

@section('page-actions')
    <a href="{{ route('almoxarife.requisitions.show', $requisition->id) }}" class="btn btn-sm btn-secondary">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
@endsection

@section('content')
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Ops!</strong> Verifique os erros abaixo.
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('almoxarife.requisitions.update', $requisition->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- CABEÇALHO --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Código</label>
                        <input type="text" class="form-control form-control-sm" value="{{ $requisition->code }}" disabled>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <input type="text" class="form-control form-control-sm text-capitalize" value="{{ $requisition->status }}" disabled>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Cliente <span class="text-danger">*</span></label>
                        <select name="client_id" class="form-select form-select-sm @error('client_id') is-invalid @enderror" required>
                            <option value="">-- Selecione --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}"
                                    {{ old('client_id', $requisition->client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->code }} - {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Prioridade <span class="text-danger">*</span></label>
                        <select name="priority" class="form-select form-select-sm @error('priority') is-invalid @enderror" required>
                            <option value="">-- Selecione --</option>
                            @php
                                $priorityValue = old('priority', $requisition->priority);
                            @endphp
                            <option value="baixa"   {{ $priorityValue == 'baixa' ? 'selected' : '' }}>Baixa</option>
                            <option value="media"   {{ $priorityValue == 'media' ? 'selected' : '' }}>Média</option>
                            <option value="alta"    {{ $priorityValue == 'alta' ? 'selected' : '' }}>Alta</option>
                            <option value="urgente" {{ $priorityValue == 'urgente' ? 'selected' : '' }}>Urgente</option>
                        </select>
                        @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Requisitante <span class="text-danger">*</span></label>
                        <input type="text"
                               name="requester_name"
                               class="form-control form-control-sm @error('requester_name') is-invalid @enderror"
                               value="{{ old('requester_name', $requisition->requester_name) }}"
                               required>
                        @error('requester_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Finalidade / Propósito</label>
                        <input type="text"
                               name="purpose"
                               class="form-control form-control-sm @error('purpose') is-invalid @enderror"
                               value="{{ old('purpose', $requisition->purpose) }}">
                        @error('purpose')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label">Observações</label>
                        <textarea name="notes"
                                  rows="2"
                                  class="form-control form-control-sm @error('notes') is-invalid @enderror">{{ old('notes', $requisition->notes) }}</textarea>
                        @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>

                {{-- ITENS --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Itens da requisição <span class="text-danger">*</span></h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-item">
                        <i class="bi bi-plus-circle"></i> Adicionar item
                    </button>
                </div>

                @error('items')
                    <div class="text-danger small mb-2">{{ $message }}</div>
                @enderror

                @php
                    // Se houve erro de validação, usa old('items'); senão, usa itens da requisição
                    $oldItems = old('items');
                    if ($oldItems === null) {
                        $oldItems = $requisition->items->map(function ($item) {
                            return [
                                'id'                 => $item->id,
                                'product_id'         => $item->product_id,
                                'requested_quantity' => $item->requested_quantity,
                                'notes'              => $item->notes,
                            ];
                        })->toArray();
                    }
                @endphp

                <div class="table-responsive">
                    <table class="table table-sm align-middle" id="items-table">
                        <thead class="table-light">
                        <tr>
                            <th style="width: 40%">Produto</th>
                            <th style="width: 15%" class="text-end">Qtd solicitada</th>
                            <th style="width: 35%">Observações</th>
                            <th style="width: 10%"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($oldItems as $idx => $item)
                            <tr>
                                {{-- Se o item já existe, guarda o ID para o update --}}
                                @if(!empty($item['id']))
                                    <input type="hidden" name="items[{{ $idx }}][id]" value="{{ $item['id'] }}">
                                @endif

                                <td>
                                    <select name="items[{{ $idx }}][product_id]"
                                            class="form-select form-select-sm @error("items.$idx.product_id") is-invalid @enderror"
                                            required>
                                        <option value="">-- Selecione --</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ $item['product_id'] == $product->id ? 'selected' : '' }}>
                                                {{ $product->code }} - {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error("items.$idx.product_id")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-end">
                                    <input type="number"
                                           name="items[{{ $idx }}][requested_quantity]"
                                           class="form-control form-control-sm text-end @error("items.$idx.requested_quantity") is-invalid @enderror"
                                           value="{{ $item['requested_quantity'] }}"
                                           min="1"
                                           required>
                                    @error("items.$idx.requested_quantity")
                                    <div class="invalid-feedback text-start">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text"
                                           name="items[{{ $idx }}][notes]"
                                           class="form-control form-control-sm @error("items.$idx.notes") is-invalid @enderror"
                                           value="{{ $item['notes'] ?? '' }}">
                                    @error("items.$idx.notes")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex justify-content-end gap-2">
                    <a href="{{ route('almoxarife.requisitions.show', $requisition->id) }}" class="btn btn-sm btn-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-check-circle"></i> Atualizar Requisição
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let itemsTableBody = document.querySelector('#items-table tbody');
        let btnAddItem     = document.querySelector('#btn-add-item');
        let index          = {{ count($oldItems) }};

        btnAddItem.addEventListener('click', function () {
            let row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <select name="items[${index}][product_id]"
                            class="form-select form-select-sm" required>
                        <option value="">-- Selecione --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">
                                {{ $product->code }} - {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td class="text-end">
                    <input type="number"
                           name="items[${index}][requested_quantity]"
                           class="form-control form-control-sm text-end"
                           min="1" required>
                </td>
                <td>
                    <input type="text"
                           name="items[${index}][notes]"
                           class="form-control form-control-sm">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            itemsTableBody.appendChild(row);
            index++;
        });

        itemsTableBody.addEventListener('click', function (e) {
            if (e.target.closest('.btn-remove-item')) {
                let row = e.target.closest('tr');
                // só remove se tiver mais de 1 linha
                if (itemsTableBody.querySelectorAll('tr').length > 1) {
                    row.remove();
                }
            }
        });
    });
</script>
@endpush
