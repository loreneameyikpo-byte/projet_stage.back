<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;   
use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('jury', function (Blueprint $table) {
                $table->id('id_jury');
                $table->foreignId('id_utilisateur')->constrained('utilisateur', 'id_utilisateur');
                $table->foreignId('id_presentation')->constrained('presentation', 'id_presentation');
                $table->timestamps();
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('jury');
        }
    };