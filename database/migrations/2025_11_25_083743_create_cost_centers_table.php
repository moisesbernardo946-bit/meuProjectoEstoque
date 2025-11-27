<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cost_centers', function (Blueprint $table) {
            $table->id();

            // Para saber a qual grupo / empresa / cliente pertence
            $table->unsignedBigInteger('company_group_id')->nullable(); // grupo (ex.: Terra Interior)
            $table->unsignedBigInteger('company_id')->nullable();       // empresa filha
            $table->unsignedBigInteger('client_id')->nullable();        // cliente

            // Código e nome do centro de custo
            $table->string('code', 50)->unique();   // ex.: 0001, 0002, 0003
            $table->string('name', 255);           // ex.: Terra Interior - Home Decor

            // Tipo (empresa_filha ou cliente)
            $table->enum('type', ['empresa', 'cliente']);

            // Campo opcional para diretor / responsável
            $table->string('director_name', 255)->nullable();

            // Flags
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // FKs (sem cascade para não dar ruim se apagar coisas)
            $table->foreign('company_group_id')->references('id')->on('company_groups');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_centers');
    }
};