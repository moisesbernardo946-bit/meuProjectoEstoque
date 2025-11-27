<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entry_products', function (Blueprint $table) {
            $table->id();

            // Quem registrou a entrada
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Entrada sempre vinculada a um entity_product
            $table->foreignId('entity_product_id')
                ->constrained('entity_products')
                ->cascadeOnDelete();

            // Dados básicos
            $table->string('supplier')->nullable(); // fornecedor (texto livre)
            $table->string('type')->default('entrada'); 
            // pode ser: 'compra', 'ajuste_positivo', 'devolucao', etc

            $table->integer('quantity');
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entry_products');
    }
};
