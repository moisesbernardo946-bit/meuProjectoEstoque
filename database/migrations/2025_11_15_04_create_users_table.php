<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Empresa filha à qual o usuário pertence (diretor, comprador, etc.)
            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->nullOnDelete();

            $table->string('name');
            $table->string('phone');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();

            $table->string('password');

            // Perfil / papel do usuário no sistema (diretor, financeiro, almoxarife, comprador, etc.)
            $table->string('role')->default('user');

            // Se quiseres limitar acesso por nível numérico também:
            // $table->unsignedTinyInteger('level')->default(1);

            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
