@extends('layouts.financeiro')

@section('title', 'Centro de Custo ' . $costCenter->code)
@section('page-title', 'Centro de Custo: ' . $costCenter->name)
@section('page-subtitle', 'Código: ' . $costCenter->code)

@section('content')
    {{-- Info básica do CC (já existia) --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-light">
            <strong>Informações do Centro de Custo</strong>
        </div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Código</dt>
                <dd class="col-sm-9">{{ $costCenter->code }}</dd>

                <dt class="col-sm-3">Nome</dt>
                <dd class="col-sm-9">{{ $costCenter->name }}</dd>

                <dt class="col-sm-3">Tipo</dt>
                <dd class="col-sm-9">{{ strtoupper($costCenter->type) }}</dd>

                <dt class="col-sm-3">Grupo</dt>
                <dd class="col-sm-9">{{ $costCenter->group?->name ?? '-' }}</dd>

                <dt class="col-sm-3">Empresa</dt>
                <dd class="col-sm-9">{{ $costCenter->company?->name ?? '-' }}</dd>

                <dt class="col-sm-3">Cliente</dt>
                <dd class="col-sm-9">{{ $costCenter->client?->name ?? '-' }}</dd>

                <dt class="col-sm-3">Diretor / Responsável</dt>
                <dd class="col-sm-9">{{ $costCenter->director_name ?? '-' }}</dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    @if ($costCenter->is_active)
                        <span class="badge bg-success">Ativo</span>
                    @else
                        <span class="badge bg-secondary">Inativo</span>
                    @endif
                </dd>
            </dl>
        </div>
    </div>

    {{-- FILTROS DE PERÍODO --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form class="row g-2" method="GET">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Ano</label>
                    <select name="year" class="form-select form-select-sm">
                        @foreach ($availableYears as $y)
                            <option value="{{ $y }}" {{ (int) $y === (int) $year ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small mb-1">Mês (opcional)</label>
                    <select name="month" class="form-select form-select-sm">
                        <option value="">Ano todo</option>
                        @for ($m = 1; $m <= 12; $m++)
                            @php
                                $val = str_pad($m, 2, '0', STR_PAD_LEFT);
                                $label = \Carbon\Carbon::create()->month($m)->locale('pt_BR')->translatedFormat('F');
                            @endphp
                            <option value="{{ $val }}" {{ $month == $val ? 'selected' : '' }}>
                                {{ ucfirst($label) }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-3 align-self-end">
                    <button class="btn btn-sm btn-outline-primary">
                        Aplicar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- RESUMO DO PERÍODO SELECIONADO (tua primeira tabela: RECEITA, CUSTOS, MG, DESPESAS, RESULTADO) --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <strong>Resumo do Período Selecionado</strong>
            <span class="small text-muted">
                @if ($month)
                    {{ $costCenter->name }} — {{ $month }}/{{ $year }}
                @else
                    {{ $costCenter->name }} — Ano {{ $year }}
                @endif
            </span>
        </div>
        <div class="card-body">
            <div class="table-responsive mb-0">
                <table class="table table-sm table-bordered w-auto mb-0">
                    <tbody>
                        <tr>
                            <th>RECEITA</th>
                            <td class="text-end">{{ number_format($receita, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>(-) CUSTOS</th>
                            <td class="text-end text-danger">{{ number_format($custos, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>(=) MG LÍQUIDA</th>
                            <td class="text-end fw-semibold">{{ number_format($mgLiquida, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>(-) DESPESAS</th>
                            <td class="text-end text-danger">{{ number_format($despesas, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>(=) RESULTADO</th>
                            <td class="text-end fw-bold
                                {{ $resultado >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($resultado, 2, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="small text-muted mt-2 mb-0">
                RECEITA: Orçamento geral do projeto (quanto o cliente decidiu pagar ou quanto a empresa decidiu
                investir).<br>
                CUSTOS: Itens diretamente ligados à produção/conclusão do projeto (madeira, pregos, chapas, etc.).<br>
                DESPESAS: Gastos não ligados diretamente ao projeto (papel higiénico, salários, etc.).<br>
                MG LÍQUIDA = RECEITA − CUSTOS. RESULTADO = MG LÍQUIDA − DESPESAS.
            </p>
        </div>
    </div>

    {{-- VISÃO MENSAL NO ANO (12 colunas se quiser, mas vou fazer por linhas) --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <strong>Resumo Mensal - Ano {{ $year }}</strong>
        </div>
        <div class="card-body">
            @if ($monthlySummary->isEmpty())
                <p class="text-muted mb-0">
                    Nenhum movimento financeiro lançado para este centro de custo no ano selecionado.
                </p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th style="width: 10%">Mês</th>
                                <th style="width: 15%">RECEITA</th>
                                <th style="width: 15%">(-) CUSTOS</th>
                                <th style="width: 15%">(=) MG LÍQUIDA</th>
                                <th style="width: 15%">(-) DESPESAS</th>
                                <th style="width: 15%">(=) RESULTADO</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($monthlySummary as $row)
                                @php
                                    $mLabel = \Carbon\Carbon::create()->month($row['month'])->locale('pt_BR')->translatedFormat('F');
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        {{ sprintf('%02d', $row['month']) }} - {{ ucfirst($mLabel) }}
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($row['receita'], 2, ',', '.') }}
                                    </td>
                                    <td class="text-end text-danger">
                                        {{ number_format($row['custos'], 2, ',', '.') }}
                                    </td>
                                    <td class="text-end fw-semibold">
                                        {{ number_format($row['mg_liquida'], 2, ',', '.') }}
                                    </td>
                                    <td class="text-end text-danger">
                                        {{ number_format($row['despesas'], 2, ',', '.') }}
                                    </td>
                                    <td class="text-end fw-bold {{ $row['resultado'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($row['resultado'], 2, ',', '.') }}
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
