{{-- resources/views/comprador/programs/create.blade.php --}}
@extends('layouts.comprador')

@section('title', 'Nova Programação de Compra')
@section('page-title', 'Nova Programação de Compra')
@section('page-subtitle', 'Programação baseada na requisição ' . $requisition->code)

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Ops!</strong> Verifique os erros abaixo.
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('comprador.programs.store') }}" method="POST">
                @csrf

                {{-- CABEÇALHO DA PROGRAMAÇÃO --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Código da Programação</label>
                        <input type="text" class="form-control form-control-sm"
                               value="(será gerado automaticamente)" disabled>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Requisição</label>
                        <input type="text" class="form-control form-control-sm"
                               value="{{ $requisition->code }}" disabled>
                        {{-- somente este hidden é necessário para o store --}}
                        <input type="hidden" name="requisition_id" value="{{ $requisition->id }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Data da Programação</label>
                        <input type="text" class="form-control form-control-sm"
                               value="{{ now()->format('d/m/Y') }}" disabled>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <input type="text" class="form-control form-control-sm"
                               value="Pendente" disabled>
                    </div>
                </div>

                {{-- DADOS DO COMPRADOR / RESPONSÁVEL --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Responsável pela programação</label>
                        <input type="text" class="form-control form-control-sm"
                               value="{{ $user->name }}" disabled>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">E-mail</label>
                        <input type="text" class="form-control form-control-sm"
                               value="{{ $user->email }}" disabled>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Telefone</label>
                        <input type="tel" class="form-control form-control-sm"
                               value="{{ $user->phone }}" disabled>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Empresa</label>
                        <input type="text" class="form-control form-control-sm"
                               value="{{ $company->code }} - {{ $company->name }}" disabled>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Cliente / Destinatário</label>
                        <input type="text" class="form-control form-control-sm"
                               value="{{ $requisition->client?->code ? $requisition->client->code . ' - ' . $requisition->client->name : 'PRÓPRIA EMPRESA' }}"
                               disabled>
                    </div>
                </div>

                <hr>

                {{-- ITENS DA PROGRAMACAO (HERDADOS DA REQUISICAO) --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">
                        Itens da programação (baseados na requisição {{ $requisition->code }})
                    </h6>
                    <small class="text-muted">
                        O comprador não altera descrição, unidade, quantidade, etc. Apenas complementa dados de compra.
                    </small>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th style="width: 5%">#</th>
                                <th style="width: 10%">Cód. Req.</th>
                                <th style="width: 10%">Cód. Produto</th>
                                <th style="width: 20%">Descrição do Material</th>
                                <th style="width: 5%">Und.</th>
                                <th style="width: 8%">Qtd</th>
                                <th style="width: 10%">Prioridade</th>
                                <th style="width: 12%">Fornecedor</th>
                                <th style="width: 10%">Forma de Pagamento</th>
                                <th style="width: 12%">Obs</th>
                                <th style="width: 10%">Valor Orçado</th>
                                <th style="width: 10%">Total Orçado</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $reqItems = $requisition->items->whereIn('item_status', [
                                    'aprovado',
                                    'em curso',
                                    'pendente',
                                ]);
                            @endphp

                            @forelse ($reqItems as $idx => $item)
                                <tr>
                                    <td class="text-center">
                                        {{ $idx + 1 }}
                                        <input type="hidden"
                                               name="items[{{ $idx }}][requisition_item_id]"
                                               value="{{ $item->id }}">
                                    </td>
                                    <td class="text-center">
                                        {{ $requisition->code }}
                                    </td>
                                    <td class="text-center">
                                        {{ $item->product->code }}
                                        <input type="hidden"
                                               name="items[{{ $idx }}][product_id]"
                                               value="{{ $item->product_id }}">
                                    </td>
                                    <td>
                                        {{ $item->product->name }}
                                        @if ($requisition->purpose)
                                            <div class="small text-muted">
                                                Finalidade: {{ $requisition->purpose }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ $item->product->unit?->symbol ?? '-' }}
                                    </td>
                                    <td class="text-end">
                                        {{ $item->requested_quantity }}
                                        <input type="hidden"
                                               name="items[{{ $idx }}][quantity]"
                                               value="{{ $item->requested_quantity }}">
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge 
                                            @if ($requisition->priority === 'urgente') bg-danger
                                            @elseif($requisition->priority === 'alta') bg-warning text-dark
                                            @elseif($requisition->priority === 'media') bg-info text-dark
                                            @else bg-secondary @endif
                                        ">
                                            {{ strtoupper($requisition->priority) }}
                                        </span>
                                        <input type="hidden"
                                               name="items[{{ $idx }}][priority]"
                                               value="{{ $requisition->priority }}">
                                    </td>

                                    {{-- Fornecedor --}}
                                    <td>
                                        <input type="text"
                                               name="items[{{ $idx }}][supplier_name]"
                                               class="form-control form-control-sm"
                                               value="{{ old("items.$idx.supplier_name") }}">
                                        @error("items.$idx.supplier_name")
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </td>

                                    {{-- Forma de Pagamento --}}
                                    <td>
                                        <input type="text"
                                               name="items[{{ $idx }}][payment_method]"
                                               class="form-control form-control-sm"
                                               value="{{ old("items.$idx.payment_method") }}">
                                        @error("items.$idx.payment_method")
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </td>

                                    {{-- Observações --}}
                                    <td>
                                        <input type="text"
                                               name="items[{{ $idx }}][notes]"
                                               class="form-control form-control-sm"
                                               value="{{ old("items.$idx.notes") }}">
                                        @error("items.$idx.notes")
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </td>

                                    {{-- Valor Orçado unitário --}}
                                    <td>
                                        <input type="number" step="0.01" min="0"
                                               name="items[{{ $idx }}][budget_unit_value]"
                                               class="form-control form-control-sm text-end budget-unit"
                                               data-index="{{ $idx }}"
                                               value="{{ old("items.$idx.budget_unit_value") }}">
                                        @error("items.$idx.budget_unit_value")
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </td>

                                    {{-- Total Orçado (display) --}}
                                    <td>
                                        <input type="text"
                                               class="form-control form-control-sm text-end budget-total"
                                               data-index="{{ $idx }}" value="" readonly>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center text-muted">
                                        Nenhum item elegível para programação de compra nesta requisição.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                        <tfoot>
                            <tr>
                                <th colspan="10" class="text-end">
                                    Total Geral Orçado:
                                </th>
                                <th colspan="2">
                                    <input type="text" id="grand_total"
                                           class="form-control form-control-sm text-end"
                                           readonly>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- TOTAIS POR MÉTODO DE PAGAMENTO --}}
                <div class="mt-3">
                    <h6>Totais por forma de pagamento</h6>
                    <div id="totals_by_method" class="row g-2">
                        {{-- JS injeta os cards aqui --}}
                    </div>

                    <div class="mt-2">
                        <label class="form-label">Total Geral (todas as formas de pagamento)</label>
                        <input type="text" id="grand_total_by_methods"
                               class="form-control form-control-sm text-end" readonly>
                    </div>
                </div>

                <hr>

                {{-- OBS GERAIS --}}
                <div class="mb-3">
                    <label class="form-label">Observações gerais da programação</label>
                    <textarea name="general_notes" rows="3"
                        class="form-control form-control-sm @error('general_notes') is-invalid @enderror">{{ old('general_notes') }}</textarea>
                    @error('general_notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-3 d-flex justify-content-end gap-2">
                    <a href="{{ route('comprador.requisitions.index') }}" class="btn btn-sm btn-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-check-circle"></i> Criar Programação (status: pendente)
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            function formatMoney(value) {
                return value.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function recalcTotals() {
                let grandTotal = 0;
                let methodTotals = {};

                document.querySelectorAll('.budget-unit').forEach(function(input) {
                    const idx = input.dataset.index;
                    const quantityInput = document.querySelector(
                        `input[name="items[${idx}][quantity]"]`
                    );
                    const totalField = document.querySelector(
                        `.budget-total[data-index="${idx}"]`
                    );

                    const qty = quantityInput ? parseFloat(quantityInput.value) || 0 : 0;
                    const unit = parseFloat(input.value) || 0;
                    const total = qty * unit;

                    if (totalField) {
                        totalField.value = total > 0 ? formatMoney(total) : '';
                    }

                    grandTotal += total;

                    // total por método
                    const paymentMethodInput = document.querySelector(
                        `input[name="items[${idx}][payment_method]"]`
                    );
                    let method = paymentMethodInput ? paymentMethodInput.value.trim() : '';

                    if (method === '') {
                        method = 'SEM DEFINIÇÃO';
                    }

                    const key = method.toUpperCase();

                    if (!methodTotals[key]) {
                        methodTotals[key] = 0;
                    }
                    methodTotals[key] += total;
                });

                // total geral da tabela
                const grandTotalField = document.getElementById('grand_total');
                if (grandTotalField) {
                    grandTotalField.value = grandTotal > 0 ? formatMoney(grandTotal) : '';
                }

                // renderizar cards por método
                const container = document.getElementById('totals_by_method');
                if (container) {
                    container.innerHTML = '';
                    Object.keys(methodTotals).forEach(function(method) {
                        const total = methodTotals[method];
                        if (total <= 0) return;

                        const col = document.createElement('div');
                        col.className = 'col-md-3';

                        col.innerHTML = `
                            <div class="border rounded p-2 small bg-light">
                                <strong>${method}:</strong>
                                <span class="float-end">${formatMoney(total)}</span>
                            </div>
                        `;

                        container.appendChild(col);
                    });
                }

                // total geral baseado nos métodos
                const grandTotalByMethodsField = document.getElementById('grand_total_by_methods');
                if (grandTotalByMethodsField) {
                    const sumMethods = Object.values(methodTotals).reduce((sum, val) => sum + val, 0);
                    grandTotalByMethodsField.value = sumMethods > 0 ? formatMoney(sumMethods) : '';
                }
            }

            document.querySelectorAll('.budget-unit').forEach(function(input) {
                input.addEventListener('input', recalcTotals);
            });

            document.querySelectorAll('input[name^="items["][name$="[payment_method]"]').forEach(function(input) {
                input.addEventListener('input', recalcTotals);
            });

            recalcTotals();
        });
    </script>
@endpush
