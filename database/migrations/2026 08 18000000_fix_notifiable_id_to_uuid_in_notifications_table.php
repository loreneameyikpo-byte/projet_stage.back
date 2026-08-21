<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // La table notifications a été créée avec morphs() (notifiable_id en
        // BIGINT), mais nos utilisateurs ont des identifiants UUID (texte).
        // On repasse notifiable_id en CHAR(36) pour qu'il puisse réellement
        // contenir un UUID, sans troncature.

        // 1. On retire l'index composite existant (nécessaire avant de
        //    modifier le type d'une colonne indexée).
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_notifiable_type_notifiable_id_index');
        });

        // 2. On change réellement le type de la colonne, en SQL brut (évite
        //    d'avoir à installer doctrine/dbal, requis par Laravel pour
        //    modifier un type de colonne via Schema::table()->change()).
        DB::statement('ALTER TABLE notifications MODIFY notifiable_id CHAR(36) NOT NULL');

        // 3. On recrée l'index composite.
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_notifiable_type_notifiable_id_index');
        });

        DB::statement('ALTER TABLE notifications MODIFY notifiable_id BIGINT UNSIGNED NOT NULL');

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }
};