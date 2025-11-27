<?php
// database/migrations/2025_01_01_000001_create_purchase_program_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_program_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('purchase_program_id')
                ->constrained()
                ->cascadeOnDelete();

            // Link direto com o item da requisição
            $table->foreignId('requisition_item_id')
                ->constrained()
                ->cascadeOnDelete();

            // Redundância controlada para facilitar consultas / relatórios
            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            // Como vai ser pago cada produto (dinheiro, transferência, cartão, etc.)
            $table->string('payment_method', 100)->nullable();

            // Fornecedor
            $table->string('supplier_name', 255)->nullable();

            // Valor orçado unitário
            $table->decimal('budget_unit_value', 15, 2)->nullable();

            // Total orçado (qtd requisitada * valor_unit)
            $table->decimal('budget_total_value', 15, 2)->nullable();

            // Status desse item dentro da programação:
            // pendente, concluido, faltando
            $table->string('status', 20)->default('pendente');

            // Obs específica desse item (comprador ou motorista)
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_program_items');
    }
};
