<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Même correctif que pour notifications.notifiable_id : la table
        // activity_log a été créée avec causer_id et subject_id en BIGINT
        // (via morphs()), incompatibles avec nos identifiants UUID.

        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropIndex('subject');
            $table->dropIndex('causer');
        });

        DB::statement('ALTER TABLE activity_log MODIFY subject_id CHAR(36) NULL');
        DB::statement('ALTER TABLE activity_log MODIFY causer_id CHAR(36) NULL');

        Schema::table('activity_log', function (Blueprint $table) {
            $table->index(['subject_type', 'subject_id'], 'subject');
            $table->index(['causer_type', 'causer_id'], 'causer');
        });
    }

    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropIndex('subject');
            $table->dropIndex('causer');
        });

        DB::statement('ALTER TABLE activity_log MODIFY subject_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE activity_log MODIFY causer_id BIGINT UNSIGNED NULL');

        Schema::table('activity_log', function (Blueprint $table) {
            $table->index(['subject_type', 'subject_id'], 'subject');
            $table->index(['causer_type', 'causer_id'], 'causer');
        });
    }
};