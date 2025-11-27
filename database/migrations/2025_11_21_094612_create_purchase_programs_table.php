<?php

// database/migrations/2025_01_01_000000_create_purchase_programs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_programs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('requisition_id')
                ->constrained()
                ->cascadeOnDelete();

            // comprador (usuário logado que cria a programação)
            $table->foreignId('buyer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('code')->unique(); // PPG-000001

            // pendente, aprovado, concluido
            $table->string('status', 20)->default('pendente');

            // Dados do comprador no momento da programação
            $table->string('buyer_name');
            $table->string('buyer_phone', 100)->nullable();
            $table->string('buyer_email')->nullable();

            // empresa filha (se for diferente de company_id; se não usar, pode deixar assim mesmo)
            $table->foreignId('buyer_company_id')
                ->nullable()
                ->constrained('companies')
                ->nullOnDelete();

            // Observações gerais da programação
            $table->text('notes')->nullable();

            // (Opcional) valor total orçado da programação
            $table->decimal('total_budget_value', 15, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_programs');
    }
};
