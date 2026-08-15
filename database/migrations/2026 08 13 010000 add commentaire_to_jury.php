<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jury_utilisateur', function (Blueprint $table) {
            // Nullable au niveau base de données (pour ne pas casser les
            // notes déjà saisies avant cette mise à jour) — le caractère
            // obligatoire est imposé au niveau de la validation applicative
            // (SaisirNoteRequest), pas de la base.
            $table->text('commentaire')->nullable()->after('note_saisie');
        });
    }

    public function down(): void
    {
        Schema::table('jury_utilisateur', function (Blueprint $table) {
            $table->dropColumn('commentaire');
        });
    }
};