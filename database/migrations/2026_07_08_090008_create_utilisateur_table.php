<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->uuid('id_utilisateur')->primary();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('mot_de_passe');
            $table->string('contacts')->nullable();
            $table->string('adresse')->nullable();
            $table->foreignUuid('id_role')->constrained('roles', 'id_role');
            $table->foreignUuid('id_promotion')->nullable()->constrained('promotions', 'id_promotion');
            $table->foreignUuid('id_filiere')->nullable()->constrained('filieres', 'id_filiere');
            $table->foreignUuid('id_specialite')->nullable()->constrained('specialites', 'id_specialite');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utilisateurs');
    }
};