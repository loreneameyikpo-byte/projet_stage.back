<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Utilisateur;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('libelle', 'super_administrateur')->first();

        Utilisateur::firstOrCreate(
            ['email' => 'lorene.ameyikpo@gmail.com'],
            [
                'nom' => 'AMEYIKPO',
                'prenom' => 'Lorene',
                'mot_de_passe' => Hash::make('password123'), // à changer en prod
                'id_role' => $role->id_role,
            ]
        );
    }
}