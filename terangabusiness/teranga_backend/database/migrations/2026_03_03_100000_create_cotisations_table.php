<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotisations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('montant', 10, 2)->default(20000.00);
            $table->enum('statut', ['EN_ATTENTE', 'VALIDÉ', 'REJETÉ'])->default('EN_ATTENTE');
            $table->string('periode', 7)->comment('Format: YYYY-MM ex: 2026-01');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotisations');
    }
};
