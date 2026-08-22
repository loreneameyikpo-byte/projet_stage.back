<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('utilisateurs', function (Blueprint $table) {
            // Nullable : les comptes déjà existants (dont le tout premier
            // super administrateur) n'ont pas de créateur connu.
            $table->uuid('cree_par')->nullable()->after('id_specialite');

            $table->foreign('cree_par')
                ->references('id_utilisateur')->on('utilisateurs')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->dropForeign(['cree_par']);
            $table->dropColumn('cree_par');
        });
    }
};