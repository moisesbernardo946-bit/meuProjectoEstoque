@extends('layouts.almoxarife')

@section('title', 'Nova Requisição')
@section('page-title', 'Nova Requisição')
@section('page-subtitle', 'Cadastro de requisição com múltiplos produtos')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Ops!</strong> Verifique os erros abaixo.
        </div>
    @endif

    {{-- erro caso o cliente não esteja assossiado a um produto na minha tabela entity_products --}}
    @error('items')
        <div class="text-danger small mb-2">{{ $message }}</div>
    @enderror

    @error('items_missing')
        <div class="text-danger small mb-2">
            {!! nl2br(e($message)) !!}
        </div>
    @enderror

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('almoxarife.requisitions.store') }}" method="POST">
                @csrf

                {{-- CABEÇALHO --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Empresa</label>
                        <input type="text" class="form-control form-control-sm"
                               value="{{ $company->code ?? '' }} - {{ $company->name ?? '' }}" disabled>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Cliente / Destinatário <span class="text-danger">*</span></label>
                        <select name="client_selector"
                                class="form-select form-select-sm @error('client_selector') is-invalid @enderror"
                                required>
                            <option value="">-- Selecione --</option>

                            {{-- Cliente é a própria empresa --}}
                            <option value="company:{{ $company->id }}"
                                {{ old('client_selector') == 'company:' . $company->id ? 'selected' : '' }}>
                                {{ $company->code ?? 'EMP' }} - {{ $company->name }} (PRÓPRIA EMPRESA)
                            </option>

                            {{-- Clientes da empresa --}}
                            @foreach ($clients as $client)
                                <option value="client:{{ $client->id }}"
                                    {{ old('client_selector') == 'client:' . $client->id ? 'selected' : '' }}>
                                    {{ $client->code }} - {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_selector')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Requisitante <span class="text-danger">*</span></label>
                        <input type="text" name="requester_name"
                               class="form-control form-control-sm @error('requester_name') is-invalid @enderror"
                               value="{{ old('requester_name') }}" required>
                        @error('requester_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Prioridade <span class="text-danger">*</span></label>
                        <select name="priority"
                                class="form-select form-select-sm @error('priority') is-invalid @enderror"
                                required>
                            <option value="">-- Selecione --</option>
                            <option value="baixa"   {{ old('priority') == 'baixa'   ? 'selected' : '' }}>Baixa</option>
                            <option value="media"   {{ old('priority') == 'media'   ? 'selected' : '' }}>Média</option>
                            <option value="alta"    {{ old('priority') == 'alta'    ? 'selected' : '' }}>Alta</option>
                            <option value="urgente" {{ old('priority') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- MOTIVO / OBS --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Finalidade / Propósito</label>
                        <input type="text" name="purpose"
                               class="form-control form-control-sm @error('purpose') is-invalid @enderror"
                               value="{{ old('purpose') }}">
                        @error('purpose')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Observações</label>
                        <textarea name="notes" rows="2"
                                  class="form-control form-control-sm @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>

                {{-- ITENS DA REQUISIÇÃO --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Itens da requisição <span class="text-danger">*</span></h6>
                    {{-- Removemos o botão de adicionar manual, os produtos virão do cliente --}}
                    {{-- Se quiser manter, depois a gente adapta --}}
                </div>

                @error('items')
                    <div class="text-danger small mb-2">{{ $message }}</div>
                @enderror

                <div class="table-responsive">
                    <table class="table table-sm align-middle" id="items-table">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%; text-align:center;">#</th>
                                <th style="width: 40%">Produto</th>
                                <th style="width: 10%; text-align:center;">Unid.</th>
                                <th style="width: 15%" class="text-end">Qtd solicitada</th>
                                <th style="width: 20%">Observações</th>
                                <th style="width: 10%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $oldItems = old('items', []);
                            @endphp

                            @if (count($oldItems))
                                {{-- Se voltou com erro de validação, repopula os itens antigos --}}
                                @foreach ($oldItems as $idx => $oldItem)
                                    <tr>
                                        <td class="text-center align-middle row-index">
                                            {{ $idx + 1 }}
                                        </td>
                                        <td>
                                            {{-- Modo "antigo": select de produtos --}}
                                            <select name="items[{{ $idx }}][product_id]"
                                                    class="form-select form-select-sm @error("items.$idx.product_id") is-invalid @enderror"
                                                    required>
                                                <option value="">-- Selecione --</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        {{ $oldItem['product_id'] == $product->id ? 'selected' : '' }}>
                                                        {{ $product->code }} - {{ $product->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error("items.$idx.product_id")
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td class="text-center align-middle">
                                            {{-- Não conseguimos montar unidade aqui sem mais lógica,
                                                 então deixamos em branco mesmo (não é crítico) --}}
                                        </td>
                                        <td class="text-end">
                                            <input type="number"
                                                   name="items[{{ $idx }}][requested_quantity]"
                                                   class="form-control form-control-sm text-end @error("items.$idx.requested_quantity") is-invalid @enderror"
                                                   value="{{ $oldItem['requested_quantity'] }}"
                                                   min="1" required>
                                            @error("items.$idx.requested_quantity")
                                                <div class="invalid-feedback text-start">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="text"
                                                   name="items[{{ $idx }}][notes]"
                                                   class="form-control form-control-sm @error("items.$idx.notes") is-invalid @enderror"
                                                   value="{{ $oldItem['notes'] }}">
                                            @error("items.$idx.notes")
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td class="text-center align-middle">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                {{-- Sem old: tbody começa vazio e será preenchido via AJAX quando escolher o cliente --}}
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Mensagem caso o cliente não tenha produtos associados --}}
                <div id="no-products-message" class="text-danger small mt-2" style="display:none;">
                    Este cliente não tem nenhum produto associado na tabela de estoque (entity_products).
                </div>

                <div class="mt-3 d-flex justify-content-end gap-2">
                    <a href="{{ route('almoxarife.requisitions.index') }}" class="btn btn-sm btn-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary" id="btn-submit">
                        <i class="bi bi-check-circle"></i> Salvar Requisição
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
        let clientSelector = document.querySelector('select[name="client_selector"]');
        let noProductsMsg  = document.querySelector('#no-products-message');
        let btnSubmit      = document.querySelector('#btn-submit');

        // Função para limpar tabela
        function clearItemsTable() {
            itemsTableBody.innerHTML = '';
        }

        // Habilita/desabilita submit
        function setSubmitEnabled(enabled) {
            if (btnSubmit) {
                btnSubmit.disabled = !enabled;
            }
        }

        // Re-numera a coluna "#"
        function renumberRows() {
            let rows = itemsTableBody.querySelectorAll('tr');
            rows.forEach(function (row, idx) {
                let cell = row.querySelector('.row-index');
                if (cell) {
                    cell.textContent = (idx + 1).toString();
                }
            });
        }

        // Evento ao mudar o cliente
        if (clientSelector) {
            clientSelector.addEventListener('change', function () {
                let value = this.value;

                clearItemsTable();
                noProductsMsg.style.display = 'none';
                setSubmitEnabled(false); // até carregar

                if (!value) {
                    return;
                }

                let url = "{{ route('almoxarife.requisitions.client-products') }}";
                url += '?client_selector=' + encodeURIComponent(value);

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro ao carregar produtos do cliente.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        let items = data.items || [];

                        if (items.length === 0) {
                            noProductsMsg.style.display = 'block';
                            setSubmitEnabled(false);
                            return;
                        }

                        items.forEach(function (item, index) {
                            let row = document.createElement('tr');

                            row.innerHTML = `
                                <td class="text-center align-middle row-index">
                                    ${index + 1}
                                </td>
                                <td>
                                    <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                                    <div class="fw-bold" style="font-size: 0.85rem;">
                                        ${(item.product_code ?? '')} - ${(item.product_name ?? '')}
                                    </div>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        Estoque atual: ${(item.current_quantity ?? 0)}
                                        ${item.min_stock !== null ? ' | Min: ' + item.min_stock : ''}
                                        ${item.max_stock !== null ? ' | Max: ' + item.max_stock : ''}
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    ${item.unit_name ?? ''}
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
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            `;

                            itemsTableBody.appendChild(row);
                        });

                        setSubmitEnabled(true);
                    })
                    .catch(error => {
                        console.error(error);
                        noProductsMsg.textContent = 'Erro ao carregar produtos deste cliente. Tente novamente.';
                        noProductsMsg.style.display = 'block';
                        setSubmitEnabled(false);
                    });
            });
        }

        // Remover linha individual
        itemsTableBody.addEventListener('click', function (e) {
            if (e.target.closest('.btn-remove-item')) {
                let row = e.target.closest('tr');
                row.remove();
                renumberRows();

                // se remover tudo, bloqueia submit
                if (itemsTableBody.querySelectorAll('tr').length === 0) {
                    setSubmitEnabled(false);
                }
            }
        });
    });
</script>
@endpush
