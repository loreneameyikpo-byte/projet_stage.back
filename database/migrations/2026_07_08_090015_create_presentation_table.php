<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presentations', function (Blueprint $table) {
            $table->uuid('id_presentation')->primary();
            $table->date('date_presentation');
            $table->time('heure_presentation');
            $table->string('libelle')->nullable();
            $table->decimal('note_finale', 4, 2)->nullable();
            $table->foreignUuid('id_utilisateur')->constrained('utilisateurs', 'id_utilisateur');
            $table->foreignUuid('id_projet')->constrained('projets', 'id_projet');
            $table->foreignUuid('id_salle')->constrained('salles', 'id_salle');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presentations');
    }
};