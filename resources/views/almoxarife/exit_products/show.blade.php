@extends('layouts.almoxarife')

@section('title', 'Detalhes da Saída')
@section('page-title', 'Detalhes da Saída')
@section('page-subtitle', 'Visualização do lançamento de saída')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <h5 class="mb-0">
                        Saída #{{ $exit->id }}
                    </h5>
                    <small class="text-muted">
                        Registrada em {{ $exit->created_at->format('d/m/Y H:i') }}
                        por {{ $exit->user?->name ?? '-' }}
                    </small>
                </div>
                <div>
                    <a href="{{ route('almoxarife.exit_products.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <h6 class="mb-2">Produto</h6>
                        <p class="mb-1">
                            <strong>{{ $exit->entityProduct->product->name ?? '-' }}</strong>
                        </p>
                        <p class="mb-0 small text-muted">
                            Código: {{ $exit->entityProduct->product->code ?? '-' }}<br>
                            Estoque da associação: {{ $exit->entityProduct->quantity }}
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <h6 class="mb-2">Entidade</h6>
                        <p class="mb-1">
                            @if($exit->entityProduct->entity_type === 'client')
                                <span class="badge bg-success">Cliente</span>
                            @else
                                <span class="badge bg-primary">Empresa</span>
                            @endif
                        </p>
                        <p class="mb-0 small">
                            {{ $exit->entityProduct->entity?->code }} -
                            {{ $exit->entityProduct->entity?->name }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <div class="border rounded p-3 text-center">
                        <div class="small text-muted">Tipo</div>
                        <div class="fw-bold">{{ $exit->type }}</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="border rounded p-3 text-center">
                        <div class="small text-muted">Quantidade</div>
                        <div class="fw-bold">{{ number_format($exit->quantity) }}</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="border rounded p-3 text-center">
                        <div class="small text-muted">Data</div>
                        <div class="fw-bold">{{ $exit->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>

            @if($exit->notes)
                <div class="mt-3">
                    <h6>Observações</h6>
                    <p class="mb-0">{{ $exit->notes }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection
