<?php
 use Illuminate\Database\Migrations\Migration;
 use Illuminate\Database\Schema\Blueprint;
 use Illuminate\Support\Facades\Schema;

 return new class extends Migration
 {
     public function up(): void
     {
         Schema::create('utilisateurs', function (Blueprint $table) {
             $table->id('id_utilisateur');
             $table->string('nom');
             $table->string('prenom');
             $table->string('email')->unique();
             $table->string('mot_de_passe');
             $table->string('contacts')->nullable();
             $table->string('adresse')->nullable();
             $table->foreignId('id_role')->constrained('roles', 'id_role');
             $table->foreignId('id_promotion')->nullable()->constrained('promotions', 'id_promotion');
             $table->foreignId('id_filiere')->nullable()->constrained('filieres', 'id_filiere');
             $table->foreignId('id_specialite')->nullable()->constrained('specialites', 'id_specialite');
             $table->rememberToken();
             $table->timestamps();

        });
     }
 
     /**
      * Reverse the migrations.
      */
     public function down(): void
     {
         Schema::dropIfExists('utilisateurs');
     }
 };