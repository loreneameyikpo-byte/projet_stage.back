<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarifs', function (Blueprint $table) {
            $table->uuid('id_tarif')->primary();
            $table->decimal('montant', 10, 2);
            $table->foreignUuid('id_promotion')->unique()->constrained('niveaux', 'id_niveau')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarifs');
    }
};