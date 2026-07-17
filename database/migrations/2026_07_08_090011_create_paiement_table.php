<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->uuid('id_paiement')->primary();
            $table->decimal('montant', 10, 2);
            $table->enum('methode', ['flooz', 'tmoney'])->nullable();
            $table->string('numero_telephone')->nullable();
            $table->string('reference_transaction')->unique()->nullable();
            $table->enum('statut', ['en_attente', 'reussi', 'echoue'])->default('en_attente');
            $table->timestamp('date_paiement')->nullable();
            $table->foreignUuid('id_projet')->constrained('projets', 'id_projet')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};