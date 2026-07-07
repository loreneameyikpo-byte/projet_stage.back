<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Specialite;

class SpecialiteSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Génie Logiciel', 'Réseaux', 'Intelligence Artificielle', 'Cybersécurité', 'Data Science'] as $libelle) {
            Specialite::firstOrCreate(['libelle' => $libelle]);
        }
    }
}