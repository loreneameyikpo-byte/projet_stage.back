<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('version_projets', function (Blueprint $table) {
            $table->uuid('id_version')->primary();
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
            $table->foreignUuid('id_projet')->constrained('projets', 'id_projet')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('version_projets');
    }
};