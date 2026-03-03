<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historique', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['COTISATION', 'PAIEMENT']);
            $table->string('description', 255);
            $table->decimal('montant', 10, 2);
            $table->enum('statut', ['EN_ATTENTE', 'VALIDÉ', 'REJETÉ'])->default('EN_ATTENTE');
            $table->timestamp('date')->useCurrent();
            $table->timestamps();

            $table->index(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historique');
    }
};
