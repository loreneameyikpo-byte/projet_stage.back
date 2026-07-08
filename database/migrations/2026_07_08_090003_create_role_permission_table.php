<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_permission', function (Blueprint $table) {
            $table->foreignUuid('id_role')->constrained('roles', 'id_role')->onDelete('cascade');
            $table->foreignUuid('id_permission')->constrained('permissions', 'id_permission')->onDelete('cascade');
            $table->primary(['id_role', 'id_permission']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permission');
    }
};