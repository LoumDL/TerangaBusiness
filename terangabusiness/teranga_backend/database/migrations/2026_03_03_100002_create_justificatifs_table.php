<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('justificatifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paiement_id')->constrained()->onDelete('cascade');
            $table->string('file_url', 500);
            $table->string('file_type', 50);
            $table->string('original_name', 255);
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('justificatifs');
    }
};
