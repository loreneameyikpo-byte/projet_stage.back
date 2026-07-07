<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Niveau;

class NiveauSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['L1', 'L2', 'L3', 'M1', 'M2'] as $libelle) {
            Niveau::firstOrCreate(['libelle' => $libelle]);
        }
    }
}