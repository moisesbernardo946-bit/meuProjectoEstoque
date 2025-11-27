<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('financial_movements', function (Blueprint $table) {
            $table->id();

            // Centro de custo
            $table->unsignedBigInteger('cost_center_id');

            // Tipo: receita, custo, despesa
            $table->enum('type', ['receita', 'custo', 'despesa']);

            // Data do movimento (usado para agrupar por mês/ano)
            $table->date('movement_date');

            // Valor (sempre positivo, o sinal lógico vem do type)
            $table->decimal('amount', 15, 2);

            // Descrição básica
            $table->string('description')->nullable();

            // Opcional: referência (ex.: código da programação, fatura, etc.)
            $table->string('reference')->nullable();

            $table->timestamps();

            $table->foreign('cost_center_id')
                ->references('id')
                ->on('cost_centers')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_movements');
    }
};