<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Filiere;

class FiliereSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['GLSI', 'Réseaux et Télécoms', 'MTWI'] as $libelle) {
            Filiere::firstOrCreate(['libelle' => $libelle]);
        }
    }
}