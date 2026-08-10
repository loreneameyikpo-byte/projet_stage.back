<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parametre;

class ParametreSeeder extends Seeder
{
    public function run(): void
    {
        $defauts = [
            // Généraux
            'nom_plateforme' => 'Projetis',
            'annee_academique_defaut' => '2025-2026',
            'taille_max_pdf_mo' => '30',
            'delai_inactivite_minutes' => '30',

            // Soutenance
            'duree_soutenance_minutes' => '90',
            'jury_membres_min' => '2',
            'jury_membres_max' => '4',
            'soutenances_a_distance' => 'false',

            // Notifications
            'notif_nouvelle_soumission' => 'true',
            'notif_projet_valide' => 'true',
            'notif_soutenance_planifiee' => 'true',
            'notif_note_publiee' => 'true',
            'notif_paiement_recu' => 'false',

            // Email
            'smtp_host' => 'smtp.univ-lome.tg',
            'smtp_port' => '1025',
            'mail_from_address' => 'contact.projetis@gmail.com',
            'mail_from_name' => 'PROJETIS',
        ];

        foreach ($defauts as $cle => $valeur) {
            Parametre::firstOrCreate(['cle' => $cle], ['valeur' => $valeur]);
        }
    }
}