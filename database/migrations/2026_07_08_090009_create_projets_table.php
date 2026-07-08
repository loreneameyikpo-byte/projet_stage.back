<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projets', function (Blueprint $table) {
            $table->uuid('id_projet')->primary();
            $table->string('titre');
            $table->text('description');
            $table->enum('statut', [
                'en_attente',
                'corrections',
                'valide',
                'presentation_planifiee',
                'presente',
            ])->default('en_attente');
            $table->foreignUuid('id_utilisateur')->constrained('utilisateurs', 'id_utilisateur');
            $table->foreignUuid('id_encadreur')->nullable()->constrained('utilisateurs', 'id_utilisateur');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projets');
    }
};