<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisition_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('requisition_id')->constrained('requisitions')->cascadeOnDelete();

            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();

            $table->integer('requested_quantity');
            $table->integer('delivered_quantity')->default(0);

            $table->text('notes')->nullable();

            // Ex.: 'pendente', 'aprovado', 'rejeitado'
            $table->string('item_status')->default('pendente');

            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisition_items');
    }
};
