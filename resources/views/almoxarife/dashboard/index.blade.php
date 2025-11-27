@extends('layouts.almoxarife')

@section('title', 'Dashboard Almoxarife')
@section('page-title', 'Dashboard do Almoxarife')
@section('page-subtitle', 'Resumo de estoque, requisições e movimentações')

@section('page-actions')
    <form method="GET" class="d-flex align-items-center gap-2">
        @php
            $meses = [
                1 => 'Janeiro',
                2 => 'Fevereiro',
                3 => 'Março',
                4 => 'Abril',
                5 => 'Maio',
                6 => 'Junho',
                7 => 'Julho',
                8 => 'Agosto',
                9 => 'Setembro',
                10 => 'Outubro',
                11 => 'Novembro',
                12 => 'Dezembro',
            ];
        @endphp

        <select name="month" class="form-select form-select-sm">
            @foreach ($meses as $num => $nome)
                <option value="{{ $num }}" {{ $num == $selectedMonth ? 'selected' : '' }}>
                    {{ $nome }}
                </option>
            @endforeach
        </select>

        <input type="number" name="year" class="form-control form-control-sm" value="{{ $selectedYear }}"
            style="width: 90px;">

        <button class="btn btn-sm btn-primary">
            <i class="bi bi-funnel me-1"></i> Filtrar
        </button>
    </form>
@endsection

@section('content')
    {{-- LINHA 1: CARDS RESUMO REQUISIÇÕES / ITENS / MOVIMENTAÇÕES --}}
    <div class="row g-3 mb-3">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Requisições no período</div>
                            <div class="fs-4 fw-bold">{{ $totalRequisicoes }}</div>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                            <i class="bi bi-file-earmark-text fs-4 text-primary"></i>
                        </div>
                    </div>
                    <div class="mt-2 d-flex justify-content-between small text-muted">
                        <span>Pendentes: {{ $requisicoesPendentes }} </span>
                        <span>Aprovadas: {{ $requisicoesAprovadas }} </span>
                        <span>Parcial: {{ $requisicoesParcial }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Itens em requisições</div>
                            <div class="fs-4 fw-bold">{{ $totalItensSolicitados }}</div>
                        </div>
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="bi bi-list-check fs-4 text-success"></i>
                        </div>
                    </div>
                    <div class="mt-2 small text-muted">
                        Entregues: <span class="fw-semibold">{{ $totalItensEntregues }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Entradas no período</div>
                            <div class="fs-4 fw-bold">{{ $totalEntradas }}</div>
                        </div>
                        <div class="rounded-circle bg-info bg-opacity-10 p-3">
                            <i class="bi bi-box-arrow-in-down fs-4 text-info"></i>
                        </div>
                    </div>
                    <div class="mt-2 small text-muted">
                        Usuário: {{ auth()->user()->name }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Saídas no período</div>
                            <div class="fs-4 fw-bold">{{ $totalSaidas }}</div>
                        </div>
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                            <i class="bi bi-box-arrow-up fs-4 text-danger"></i>
                        </div>
                    </div>
                    <div class="mt-2 small text-muted">
                        Controle de consumo
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- LINHA 2: CARDS DE ESTOQUE --}}
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <div class="text-muted small">Produtos da empresa</div>
                            <div class="fs-4 fw-bold">{{ $totalProdutosEmpresa }}</div>
                        </div>
                        <div class="rounded-circle bg-secondary bg-opacity-10 p-3">
                            <i class="bi bi-boxes fs-4 text-secondary"></i>
                        </div>
                    </div>
                    <div class="small text-muted">
                        Quantidade total em estoque:
                        <span class="fw-semibold">{{ $totalQuantidadeEmpresa }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status do estoque --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <div class="text-muted small">Status de estoque</div>
                            <div class="fs-6 fw-bold">Distribuição</div>
                        </div>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                            <i class="bi bi-activity fs-4 text-warning"></i>
                        </div>
                    </div>

                    <ul class="list-unstyled small mb-0">
                        <li class="d-flex justify-content-between">
                            <span>Normal</span>
                            <span class="fw-semibold">{{ $estoqueStatusCount['normal'] ?? 0 }}</span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span>Crítico</span>
                            <span class="fw-semibold text-danger">{{ $estoqueStatusCount['critico'] ?? 0 }}</span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span>Excesso</span>
                            <span class="fw-semibold text-info">{{ $estoqueStatusCount['excesso'] ?? 0 }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Total de produtos cadastrados geral --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <div class="text-muted small">Produtos cadastrados (catálogo)</div>
                            <div class="fs-4 fw-bold">{{ $totalProdutos }}</div>
                        </div>
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="bi bi-box2-heart fs-4 text-success"></i>
                        </div>
                    </div>
                    <div class="small text-muted">
                        Produtos disponíveis para vincular à empresa e clientes.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- LINHA 3: TOP SAÍDAS + ÚLTIMAS REQUISIÇÕES --}}
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-box-arrow-up me-1"></i>
                        Top 5 produtos mais saíram
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if ($topSaidas->isEmpty())
                        <div class="p-3 small text-muted">
                            Nenhuma saída registrada no período.
                        </div>
                    @else
                        <table class="table mb-0 table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Produto</th>
                                    <th class="text-end">Quantidade saída</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topSaidas as $linha)
                                    @php
                                        $produto = $linha->entityProduct?->product;
                                    @endphp
                                    <tr>
                                        <td>
                                            @if ($produto)
                                                <strong>{{ $produto->name }}</strong><br>
                                                <small class="text-muted">{{ $produto->code }}</small>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="text-end">{{ $linha->total_saiu }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-file-earmark-text me-1"></i>
                        Últimas requisições
                    </h6>
                    <a href="{{ route('almoxarife.requisitions.index') }}" class="small text-decoration-none">
                        Ver todas
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($ultimasRequisicoes->isEmpty())
                        <div class="p-3 small text-muted">
                            Nenhuma requisição encontrada para o período.
                        </div>
                    @else
                        <table class="table mb-0 table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Solicitante</th>
                                    <th>Status</th>
                                    <th class="text-end">Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ultimasRequisicoes as $req)
                                    <tr>
                                        <td>{{ $req->id }}</td>
                                        <td>
                                            {{ $req->requester_name ?? ($req->user?->name ?? '—') }}
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match ($req->status) {
                                                    'aprovada' => 'success',
                                                    'rejeitada' => 'danger',
                                                    'pendente' => 'warning',
                                                    default => 'secondary',
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">
                                                {{ ucfirst($req->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end small text-muted">
                                            {{ $req->created_at->format('d/m/Y H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
