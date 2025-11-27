@extends('layouts.almoxarife')

@section('title', 'Editar Associação')
@section('page-title', 'Editar Associação de Produto')
@section('page-subtitle', 'Atualize apenas os campos permitidos')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Editar Associação</h5>
            <small class="text-muted">ID: {{ $entityProduct->id }}</small>
        </div>
        <div>
            <a href="{{ route('almoxarife.entity_products.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Ops!</strong> Verifique os erros abaixo:
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('almoxarife.entity_products.update', $entityProduct->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Linha principal: tipo fixo (não alterável) --}}
            <div class="mb-3">
                <label class="form-label">Tipo de Associação</label>
                <input type="text" class="form-control" value="{{ ucfirst($entityProduct->entity_type) }}" disabled>
                {{-- manter o type no server (não enviamos input entity_type para não permitir alteração) --}}
            </div>

            {{-- Entidade: se client -> escolhe cliente; se company -> mostra empresa fixa --}}
            @if($isClient)
                <div class="mb-3">
                    <label class="form-label">Cliente</label>
                    <select name="entity_id" class="form-select" required>
                        <option value="">-- selecione cliente --</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ $entityProduct->entity_id == $c->id ? 'selected' : '' }}>
                                {{ $c->code }} — {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Podes trocar para outro cliente da tua empresa.</div>
                </div>
            @else
                <div class="mb-3">
                    <label class="form-label">Empresa</label>
                    <input type="text" class="form-control" value="{{ $company->code }} — {{ $company->name }}" disabled>
                    <input type="hidden" name="entity_id" value="{{ $company->id }}">
                    <div class="form-text">A empresa é fixa — não é possível alterar.</div>
                </div>
            @endif

            {{-- Produto (pode ser alterado em ambos os casos) --}}
            <div class="mb-3">
                <label class="form-label">Produto</label>
                <select name="product_id" class="form-select" required>
                    <option value="">-- selecione produto --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ $entityProduct->product_id == $p->id ? 'selected' : '' }}>
                            {{ $p->code }} — {{ $p->name }} ({{ $p->measure }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Campos condicionalmente editáveis --}}
            @if($isClient)
                <div class="mb-3">
                    <label class="form-label">Quantidade solicitada</label>
                    <input type="number" name="requested_quantity" min="1" step="1" class="form-control"
                        value="{{ old('requested_quantity', $entityProduct->requested_quantity) }}">
                    <div class="form-text">Quantidade que o cliente requisitou.</div>
                </div>
            @else
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label">Estoque mínimo</label>
                        <input type="number" name="min_stock" min="0" step="1" class="form-control"
                            value="{{ old('min_stock', $entityProduct->min_stock) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Estoque máximo</label>
                        <input type="number" name="max_stock" min="0" step="1" class="form-control"
                            value="{{ old('max_stock', $entityProduct->max_stock) }}">
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Quantidade atual (somente leitura)</label>
                    <input type="text" class="form-control" value="{{ number_format($entityProduct->quantity ?? 0, 0, ',', '.') }}" disabled>
                    <div class="form-text">Quantidade controlada por entradas/saídas.</div>
                </div>
            @endif

            <div class="mt-4 d-flex justify-content-end gap-2">
                <a href="{{ route('almoxarife.entity_products.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Salvar alterações</button>
            </div>
        </form>
    </div>
</div>
@endsection
