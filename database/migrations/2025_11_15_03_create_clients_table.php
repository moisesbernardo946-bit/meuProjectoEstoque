<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                  ->constrained('companies')
                  ->onDelete('cascade'); // se apagar empresa filha, apaga clientes

            $table->string('code')->unique();
            $table->string('name');                // nome do cliente
            $table->string('nif')->nullable();     // NIF do cliente
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
