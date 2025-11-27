@extends('layouts.almoxarife') {{-- ou o layout que estás a usar --}}

@section('title', 'Associar Produtos à Entidade')
@section('page-title', 'Associar Produtos')
@section('page-subtitle', 'Vincular produtos a um cliente ou à empresa')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Nova associação de produtos</h5>
            <a href="{{ route('almoxarife.entity_products.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Ops!</strong> Verifique os erros abaixo:<br><br>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('almoxarife.entity_products.store') }}" method="POST" id="entityProductForm">
                @csrf

                {{-- Tipo de entidade --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tipo de entidade</label>
                    <select name="entity_type" id="entity_type" class="form-select" required>
                        <option value="">-- Selecione --</option>
                        <option value="client" {{ old('entity_type') === 'client' ? 'selected' : '' }}>Cliente</option>
                        <option value="company" {{ old('entity_type') === 'company' ? 'selected' : '' }}>Empresa</option>
                    </select>
                </div>

                {{-- Para CLIENTE: escolhe o client_id, mas vai ser copiado pra entity_id --}}
                <div class="col-md-4" id="client_select_wrapper">
                    <label class="form-label fw-semibold">Cliente</label>
                    <select id="client_id" class="form-select">
                        <option value="">-- Selecione o cliente --</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}"
                                {{ old('entity_id') == $client->id && old('entity_type') === 'client' ? 'selected' : '' }}>
                                {{ $client->code }} - {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Para EMPRESA: só mostra, mas entity_id fica como id da empresa --}}
                <div class="col-md-4 d-none" id="company_info_wrapper">
                    <label class="form-label fw-semibold">Empresa</label>
                    <input type="text" class="form-control"
                        value="{{ $currentCompany->code }} - {{ $currentCompany->name }}" disabled>
                </div>

                {{-- HIDDEN QUE VAI PRO BACKEND --}}
                <input type="hidden" name="entity_id" id="entity_id" value="{{ old('entity_id') }}">

                <hr>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Produtos</label>
                    <small class="text-muted d-block mb-2">
                        Selecione um ou mais produtos para associar a esta entidade.
                    </small>

                    <div id="products_container">
                        {{-- Linha base (clonável) --}}
                        <div class="row g-2 mb-2 product-row">
                            <div class="col-md-4">
                                <select name="product_ids[]" class="form-select product-select" required>
                                    <option value="">-- Escolha o produto --</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->code }} - {{ $product->name }} ({{ $product->measure }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- CAMPOS PARA CLIENTE --}}
                            <div class="col-md-3 client-fields">
                                <input type="number" class="form-control requested-qty-input" name="requested_quantity[]"
                                    placeholder="Qtd solicitada" min="1">
                            </div>

                            {{-- CAMPOS PARA EMPRESA --}}
                            <div class="col-md-2 company-fields d-none">
                                <input type="number" class="form-control min-stock-input" name="min_stock[]"
                                    placeholder="Min" min="0">
                            </div>

                            <div class="col-md-2 company-fields d-none">
                                <input type="number" class="form-control max-stock-input" name="max_stock[]"
                                    placeholder="Max" min="0">
                            </div>

                            <div class="col-md-1 d-flex align-items-center">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-row-btn" disabled>
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add_product_row">
                        <i class="bi bi-plus-circle"></i> Adicionar outro produto
                    </button>
                </div>

                <hr>

                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Salvar associação
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const entityTypeSelect = document.getElementById('entity_type');
            const clientWrapper = document.getElementById('client_select_wrapper');
            const companyWrapper = document.getElementById('company_info_wrapper');
            const clientSelect = document.getElementById('client_id');
            const entityIdInput = document.getElementById('entity_id');
            const productsContainer = document.getElementById('products_container');
            const addRowBtn = document.getElementById('add_product_row');
            const currentCompanyId = {{ $currentCompany->id }}; // empresa do almoxarife
            const form = document.getElementById('entityProductForm');

            function syncEntityId() {
                const type = entityTypeSelect.value;
                if (type === 'client') {
                    entityIdInput.value = clientSelect.value || '';
                } else if (type === 'company') {
                    entityIdInput.value = currentCompanyId;
                } else {
                    entityIdInput.value = '';
                }
            }

            function updateVisibilityByEntityType() {
                const type = entityTypeSelect.value;

                if (type === 'client') {
                    clientWrapper.classList.remove('d-none');
                    companyWrapper.classList.add('d-none');

                    document.querySelectorAll('.product-row').forEach(row => {
                        row.querySelectorAll('.client-fields').forEach(el => el.classList.remove('d-none'));
                        row.querySelectorAll('.company-fields').forEach(el => el.classList.add('d-none'));

                        // Limpamos campos de empresa
                        row.querySelectorAll('.min-stock-input, .max-stock-input').forEach(inp => inp
                            .value = '');
                    });

                } else if (type === 'company') {
                    clientWrapper.classList.add('d-none');
                    companyWrapper.classList.remove('d-none');

                    document.querySelectorAll('.product-row').forEach(row => {
                        row.querySelectorAll('.client-fields').forEach(el => el.classList.add('d-none'));
                        row.querySelectorAll('.company-fields').forEach(el => el.classList.remove(
                            'd-none'));

                        // Limpamos qtd solicitada
                        row.querySelectorAll('.requested-qty-input').forEach(inp => inp.value = '');
                    });
                } else {
                    clientWrapper.classList.add('d-none');
                    companyWrapper.classList.add('d-none');
                }

                syncEntityId();
            }

            entityTypeSelect.addEventListener('change', updateVisibilityByEntityType);
            clientSelect.addEventListener('change', syncEntityId);

            updateVisibilityByEntityType();

            // Adicionar nova linha de produto
            addRowBtn.addEventListener('click', function() {
                const firstRow = productsContainer.querySelector('.product-row');
                const newRow = firstRow.cloneNode(true);

                // Limpa valores da linha clonada
                newRow.querySelectorAll('select, input').forEach(input => {
                    if (input.tagName.toLowerCase() === 'select') {
                        input.selectedIndex = 0;
                    } else {
                        input.value = '';
                    }
                });

                const removeBtn = newRow.querySelector('.remove-row-btn');
                removeBtn.disabled = false;
                removeBtn.addEventListener('click', function() {
                    newRow.remove();
                });

                productsContainer.appendChild(newRow);

                // Aplica regra de visibilidade aos novos campos
                updateVisibilityByEntityType();
            });

            // habilitar remoção na primeira linha apenas se tiver mais de uma
            document.querySelectorAll('.remove-row-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const row = btn.closest('.product-row');
                    if (row && productsContainer.querySelectorAll('.product-row').length > 1) {
                        row.remove();
                    }
                });
            });

            // Validação Min/Max no front para empresa
            form.addEventListener('submit', function(e) {
                const type = entityTypeSelect.value;

                // garantir entity_id preenchido
                syncEntityId();
                if (!entityIdInput.value) {
                    e.preventDefault();
                    alert('Selecione a entidade (cliente ou empresa) antes de salvar.');
                    return;
                }

                if (type === 'company') {
                    let hasError = false;
                    let messages = [];

                    document.querySelectorAll('.product-row').forEach((row, idx) => {
                        const minInput = row.querySelector('.min-stock-input');
                        const maxInput = row.querySelector('.max-stock-input');

                        const minVal = minInput && minInput.value !== '' ? parseInt(minInput.value,
                            10) : null;
                        const maxVal = maxInput && maxInput.value !== '' ? parseInt(maxInput.value,
                            10) : null;

                        if (minVal !== null && maxVal !== null && minVal > maxVal) {
                            hasError = true;
                            messages.push(
                                `Na linha ${idx + 1}, o estoque mínimo não pode ser maior que o estoque máximo.`
                                );
                        }
                    });

                    if (hasError) {
                        e.preventDefault();
                        alert(messages.join('\n'));
                        return;
                    }

                    // Como é empresa, podemos limpar requested_quantity
                    document.querySelectorAll('.requested-qty-input').forEach(inp => {
                        inp.value = ''; // vai como null
                    });

                } else if (type === 'client') {
                    // Como é cliente, podemos limpar min/max
                    document.querySelectorAll('.min-stock-input, .max-stock-input').forEach(inp => {
                        inp.value = ''; // vai como null
                    });
                }
            });

        });
    </script>
@endpush
