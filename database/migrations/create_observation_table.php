<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observation', function (Blueprint $table) {
        $table->id('id_observation');
        $table->text('contenu');
        $table->date('date');
        $table->foreignId('id_utilisateur')->constrained('utilisateur', 'id_utilisateur');
        $table->foreignId('id_projet')->constrained('projet', 'id_projet')->onDelete('cascade');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('observation');
    }
}