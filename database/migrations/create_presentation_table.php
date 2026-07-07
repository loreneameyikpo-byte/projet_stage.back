<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('presentation', function (Blueprint $table) {
        $table->id('id_presentation');
        $table->date('date_presentation');
        $table->time('heure_presentation');
        $table->string('libelle')->nullable();
        $table->decimal('note_finale', 4, 2)->nullable();
        $table->foreignId('id_utilisateur')->constrained('utilisateur', 'id_utilisateur');
        $table->foreignId('id_projet')->constrained('projet', 'id_projet');
        $table->foreignId('id_salle')->constrained('salle', 'id_salle');
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('presentation');
    }
};