<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'etudiant',
            'encadreur',
            'administrateur',
            'super_administrateur',
            'jury_externe',
        ];

        foreach ($roles as $libelle) {
            Role::firstOrCreate(['libelle' => $libelle]);
        }
    }
}