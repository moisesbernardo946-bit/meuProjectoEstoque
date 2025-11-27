@extends('layouts.almoxarife')

@section('title', 'Detalhes da Associação')
@section('page-title', 'Detalhes da Associação')
@section('page-subtitle', 'Visão detalhada e profissional')

@section('content')
    <div class="row g-4">
        {{-- Left: Product details --}}
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="mb-1">{{ $entityProduct->product->name }}</h5>
                            <div class="text-muted small">{{ $entityProduct->product->code }}</div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary">{{ ucfirst($entityProduct->entity_type) }}</span>
                            <div class="small text-muted mt-1">
                                Associação ID: {{ $entityProduct->id }}
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="small text-muted">Categoria</div>
                            <div class="fw-semibold">{{ $entityProduct->product->category?->name ?? '-' }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="small text-muted">Unidade</div>
                            <div class="fw-semibold">{{ $entityProduct->product->unit?->symbol ?? '-' }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="small text-muted">Medida</div>
                            <div class="fw-semibold">{{ $entityProduct->product->measure ?? '-' }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="small text-muted">Zona</div>
                            <div class="fw-semibold">{{ $entityProduct->product->zone?->name ?? '-' }}</div>
                        </div>

                        <div class="col-12 mt-2">
                            <div class="small text-muted">Descrição</div>
                            <div class="text-muted">{{ $entityProduct->product->description ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Stock / Entity --}}
        <div class="col-lg-5">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="mb-3">Entidade</h6>

                    @if ($entityProduct->entity_type === 'client')
                        @php $ent = $entityProduct->entity; @endphp
                        <div class="fw-semibold">{{ $ent?->code ?? '-' }} — {{ $ent?->name ?? 'Cliente não encontrado' }}
                        </div>
                        <div class="text-muted small mb-2">Tipo: Cliente</div>
                    @else
                        <div class="fw-semibold">{{ $company->code }} — {{ $company->name }}</div>
                        <div class="text-muted small mb-2">Tipo: Empresa</div>
                    @endif

                    <hr>

                    <h6 class="mb-2">Estoque / Solicitação</h6>

                    <div class="d-flex justify-content-between">
                        <div class="small text-muted">Quantidade</div>
                        <div class="fw-semibold">{{ number_format($entityProduct->quantity ?? 0, 0, ',', '.') }}</div>
                    </div>

                    @if ($entityProduct->entity_type === 'client')
                        <div class="d-flex justify-content-between mt-2">
                            <div class="small text-muted">Qtd solicitada</div>
                            <div class="fw-semibold">{{ $entityProduct->requested_quantity ?? '-' }}</div>
                        </div>
                    @endif

                    @if ($entityProduct->entity_type === 'company')
                        <div class="d-flex justify-content-between mt-2">
                            <div class="small text-muted">Estoque mínimo</div>
                            <div class="fw-semibold">{{ $entityProduct->min_stock ?? '-' }}</div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <div class="small text-muted">Estoque máximo</div>
                            <div class="fw-semibold">{{ $entityProduct->max_stock ?? '-' }}</div>
                        </div>
                    @endif

                    <div class="mt-3">
                        <div class="small text-muted">Status</div>
                        @php
                            $status = $entityProduct->computed_status ?? null;

                            $badge = 'secondary';

                            if (in_array($status, ['critico', 'vazio'])) {
                                $badge = 'danger';
                            } elseif (in_array($status, ['normal', 'entregue', 'concluido'])) {
                                $badge = 'success';
                            } elseif (in_array($status, ['excesso', 'faltando'])) {
                                $badge = 'warning text-dark';
                            }
                        @endphp

                        <div class="mt-1">
                            <span
                                class="badge bg-{{ str_contains($badge, 'text-') ? strtok($badge, ' ') : $badge }}
            {{ str_contains($badge, 'text-') ? ' ' . explode(' ', $badge)[1] : '' }}">
                                {{ $status ? ucfirst($status) : 'Desconhecido' }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <a href="{{ route('almoxarife.entity_products.edit', $entityProduct->id) }}"
                            class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        <a href="{{ route('almoxarife.entity_products.index') }}" class="btn btn-sm btn-outline-secondary">
                            Voltar
                        </a>
                    </div>
                </div>
            </div>

            {{-- Observações / histórico (espaço para futuro) --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Observações</h6>
                    <div class="text-muted small">
                        <!-- podes adicionar notas, último movimento, histórico etc -->
                        {{ $entityProduct->notes ?? 'Sem observações.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
