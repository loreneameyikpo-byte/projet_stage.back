<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;   
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projet', function (Blueprint $table) {
            $table->id('id_projet');
            $table->string('titre');
            $table->text('description');
            $table->enum('statut', [
            'en_attente',
            'corrections',
            'valide',
        ])->default('en_attente');
            $table->foreignId('id_utilisateur')->constrained('utilisateur', 'id_utilisateur');
            $table->foreignId('id_encadreur')->nullable()->constrained('utilisateur', 'id_utilisateur');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projet');
    }
};