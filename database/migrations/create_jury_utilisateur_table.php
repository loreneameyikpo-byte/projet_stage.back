<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jury_utilisateur', function (Blueprint $table) {
        $table->foreignId('id_jury')->constrained('jury', 'id_jury')->onDelete('cascade');
        $table->foreignId('id_utilisateur')->constrained('utilisateur', 'id_utilisateur');
        $table->string('role_jury');
        $table->decimal('note_saisie', 4, 2)->nullable();
        $table->primary(['id_jury', 'id_utilisateur']);
        $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jury_utilisateur');
    }
};