<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_group_id')
                  ->constrained('company_groups')
                  ->onDelete('cascade'); // se apagar o grupo, apaga as filhas

            $table->string('name');                // nome da empresa filha (ex.: Home Decor)
            $table->string('code')->unique();      // código (ex.: 0001, 0002...)
            $table->string('nif')->nullable();     // se quiser ter NIF diferente por filial
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
