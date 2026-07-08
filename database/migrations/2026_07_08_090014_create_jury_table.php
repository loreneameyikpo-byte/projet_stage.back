<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jury', function (Blueprint $table) {
            $table->uuid('id_jury')->primary();
            $table->foreignUuid('id_presentation')->constrained('presentations', 'id_presentation')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jury');
    }
};