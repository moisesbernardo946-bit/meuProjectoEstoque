<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisitions', function (Blueprint $table) {
            $table->id();

            // Quem registrou a requisição
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Para quem é a requisição (cliente interno ou externo)
            $table->unsignedBigInteger('client_id')->nullable();

            // De qual empresa está saindo o material
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->string('requester_name'); // nome livre de quem solicitou
            $table->string('code'); // código interno tipo REQ-0001

            $table->string('priority');// ['baixa', 'media', 'alta', 'urgente']

            // Ex.: 'pendente', 'aprovada', 'parcial', 'concluida', 'rejeitada'
            $table->string('status')->default('pendente');

            $table->string('purpose')->nullable(); // finalidade
            $table->text('notes')->nullable();     // observações gerais

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisitions');
    }
};
