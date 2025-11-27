<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome do grupo (ex.: TERRA INTERIOR)
            $table->string('nif')->unique(); // NIF da empresa mãe
            $table->string('code')->nullable(); // Sigla/código (ex.: TI, TF, MDT)
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_groups');
    }
};
