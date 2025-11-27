<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exit_products', function (Blueprint $table) {
            $table->id();

            // Quem registrou a saída
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Saída sempre vinculada a um entity_product
            $table->foreignId('entity_product_id')
                ->constrained('entity_products')
                ->cascadeOnDelete();

            $table->string('type')->default('saida');
            // Ex.: 'consumo_interno', 'entrega_cliente', 'ajuste_negativo'

            $table->integer('quantity');
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exit_products');
    }
};
