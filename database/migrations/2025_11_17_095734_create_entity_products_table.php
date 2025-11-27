<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('entity_products', function (Blueprint $table) {
            $table->id();

            // Produto
            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Morph: entity_type (company / client) e entity_id (id da empresa/cliente)
            $table->string('entity_type'); // App\Models\Company ou App\Models\Client (por exemplo)
            $table->unsignedBigInteger('entity_id');

            // Empresa dona do estoque (separado da entity morph)
            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Quantidades
            $table->integer('quantity')->default(0);
            $table->integer('requested_quantity')->nullable();
            $table->integer('min_stock')->nullable();
            $table->integer('max_stock')->nullable();

            $table->timestamps();

            // Evitar produto repetido para a mesma entity (company/client)
            $table->unique(
                ['product_id', 'entity_type', 'entity_id', 'company_id'],
                'entity_products_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_products');
    }
};
