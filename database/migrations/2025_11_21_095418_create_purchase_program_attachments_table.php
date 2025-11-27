<?php
// database/migrations/2025_01_01_000002_create_purchase_program_attachments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_program_attachments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('purchase_program_id')
                ->constrained()
                ->cascadeOnDelete();

            // caminho do ficheiro no storage
            $table->string('path');

            // nome original do ficheiro
            $table->string('original_name');

            // (Opcional) tipo MIME, se quiser
            $table->string('mime_type', 100)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_program_attachments');
    }
};
