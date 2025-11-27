@extends('layouts.financeiro')

@section('title', 'Novo Centro de Custo')
@section('page-title', 'Novo Centro de Custo')
@section('page-subtitle', 'Criação de centro de custo para empresa ou cliente')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Ops!</strong> Verifique os erros abaixo.
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('financeiro.cost_centers.store') }}" method="POST">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Tipo de Centro de Custo</label>
                        <select name="type" id="cc_type"
                            class="form-select form-select-sm @error('type') is-invalid @enderror">
                            <option value="">Selecione...</option>
                            <option value="empresa" {{ old('type') === 'empresa' ? 'selected' : '' }}>Empresa filha
                            </option>
                            <option value="cliente" {{ old('type') === 'cliente' ? 'selected' : '' }}>Cliente</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-5">
                        <label class="form-label">Diretor / Responsável (opcional)</label>
                        <input type="text" name="director_name" value="{{ old('director_name') }}"
                            class="form-control form-control-sm @error('director_name') is-invalid @enderror">
                        @error('director_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Pré-visualização (Código / Nome)</label>
                        <input type="text" id="cc_preview" class="form-control form-control-sm" disabled>
                        <div class="form-text">
                            O código e o nome serão gerados automaticamente de acordo com a seleção.
                        </div>
                    </div>
                </div>

                <hr>

                {{-- Seleção empresa filha --}}
                <div id="empresa_block" class="mb-3" style="display: none;">
                    <h6>Centro de Custo para Empresa Filha</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Empresa</label>
                            <select name="company_id" id="company_id"
                                class="form-select form-select-sm @error('company_id') is-invalid @enderror">
                                <option value="">Selecione uma empresa...</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}" data-code="{{ $company->code }}"
                                        data-name="{{ $company->name }}"
                                        data-group="{{ $company->group?->name }}"
                                        {{ (string) $company->id === old('company_id') ? 'selected' : '' }}>
                                        {{ $company->group?->name ? $company->group->name . ' - ' : '' }}{{ $company->name }}
                                        ({{ $company->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('company_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Seleção cliente --}}
                <div id="cliente_block" class="mb-3" style="display: none;">
                    <h6>Centro de Custo para Cliente</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Cliente</label>
                            <select name="client_id" id="client_id"
                                class="form-select form-select-sm @error('client_id') is-invalid @enderror">
                                <option value="">Selecione um cliente...</option>
                                @foreach ($companies as $company)
                                    @foreach ($company->clients as $client)
                                        <option value="{{ $client->id }}" data-code="{{ $client->code }}"
                                            data-name="{{ $client->name }}"
                                            data-company="{{ $company->name }}"
                                            data-group="{{ $company->group?->name }}"
                                            {{ (string) $client->id === old('client_id') ? 'selected' : '' }}>
                                            {{ $company->group?->name ? $company->group->name . ' - ' : '' }}
                                            {{ $company->name }} → {{ $client->name }} ({{ $client->code }})
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                            @error('client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-3 d-flex justify-content-end gap-2">
                    <a href="{{ route('financeiro.cost_centers.index') }}" class="btn btn-sm btn-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-check-circle"></i> Salvar Centro de Custo
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updatePreview() {
            const type = document.getElementById('cc_type').value;
            const preview = document.getElementById('cc_preview');

            if (!preview) return;

            if (type === 'empresa') {
                const select = document.getElementById('company_id');
                const opt = select ? select.options[select.selectedIndex] : null;

                if (opt && opt.dataset.code && opt.dataset.name) {
                    const groupName = opt.dataset.group || '';
                    const companyName = opt.dataset.name;
                    const code = opt.dataset.code;

                    let name = groupName ? (groupName + ' - ' + companyName) : companyName;
                    preview.value = code + ' | ' + name;
                } else {
                    preview.value = '';
                }
            } else if (type === 'cliente') {
                const select = document.getElementById('client_id');
                const opt = select ? select.options[select.selectedIndex] : null;

                if (opt && opt.dataset.code && opt.dataset.name) {
                    const name = opt.dataset.name;
                    const code = opt.dataset.code;
                    preview.value = code + ' | ' + name;
                } else {
                    preview.value = '';
                }
            } else {
                preview.value = '';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('cc_type');
            const empresaBlock = document.getElementById('empresa_block');
            const clienteBlock = document.getElementById('cliente_block');

            function toggleBlocks() {
                const type = typeSelect.value;

                if (type === 'empresa') {
                    empresaBlock.style.display = '';
                    clienteBlock.style.display = 'none';
                } else if (type === 'cliente') {
                    empresaBlock.style.display = 'none';
                    clienteBlock.style.display = '';
                } else {
                    empresaBlock.style.display = 'none';
                    clienteBlock.style.display = 'none';
                }

                updatePreview();
            }

            typeSelect.addEventListener('change', toggleBlocks);

            const companySelect = document.getElementById('company_id');
            const clientSelect = document.getElementById('client_id');

            if (companySelect) {
                companySelect.addEventListener('change', updatePreview);
            }
            if (clientSelect) {
                clientSelect.addEventListener('change', updatePreview);
            }

            // Inicializa baseado no old()
            toggleBlocks();
        });
    </script>
@endpush
