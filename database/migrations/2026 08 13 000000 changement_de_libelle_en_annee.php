<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Renommage direct en SQL brut : évite d'avoir à installer le paquet
        // doctrine/dbal, normalement requis par Laravel pour renommer une
        // colonne via Schema::table(...)->renameColumn(...).
        DB::statement('ALTER TABLE promotions CHANGE annee libelle VARCHAR(255) NOT NULL');

        Schema::table('promotions', function (Blueprint $table) {
            // De simples entiers (ex: 2025, 2028) — pas de date complète,
            // le jour/mois exact n'a pas de sens ici.
            $table->unsignedSmallInteger('annee_debut')->nullable()->after('libelle');
            $table->unsignedSmallInteger('annee_fin')->nullable()->after('annee_debut');
        });
    }

    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn(['annee_debut', 'annee_fin']);
        });

        DB::statement('ALTER TABLE promotions CHANGE libelle annee VARCHAR(255) NOT NULL');
    }
};