<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('version_projet', function (Blueprint $table) {
        $table->id('id_version');
        $table->unsignedInteger('numero_version');
        $table->string('rapport_pdf');
        $table->string('depot_github')->nullable();
        $table->timestamp('date_depot')->useCurrent();
        $table->enum('statut_version', [
            'en_attente',
            'validee',
            'corrections_demandees',
            'remplacee',
        ])->default('en_attente');
        $table->foreignId('id_projet')->constrained('projet', 'id_projet')->onDelete('cascade');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('version_projet');
    }
};
