<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observations', function (Blueprint $table) {
            $table->uuid('id_observation')->primary();
            $table->text('contenu');
            $table->date('date');
            $table->foreignUuid('id_utilisateur')->constrained('utilisateurs', 'id_utilisateur');
            $table->foreignUuid('id_projet')->constrained('projets', 'id_projet')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observations');
    }
};