<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['code' => 'projet.creer', 'libelle' => 'Créer un projet'],
            ['code' => 'projet.consulter', 'libelle' => 'Consulter un projet'],
            ['code' => 'projet.valider', 'libelle' => 'Valider un projet'],
            ['code' => 'utilisateur.gerer', 'libelle' => 'Gérer les utilisateurs'],
            ['code' => 'presentation.planifier', 'libelle' => 'Planifier une présentation'],
            ['code' => 'jury.noter', 'libelle' => 'Saisir une note de jury'],
            ['code' => 'administrateur.gerer', 'libelle' => 'Gérer les administrateurs'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['code' => $perm['code']], $perm);
        }

        // Attribution de base par rôle
        $etudiant = Role::where('libelle', 'etudiant')->first();
        $etudiant->permissions()->syncWithoutDetaching(
            Permission::whereIn('code', ['projet.creer', 'projet.consulter'])->pluck('id_permission')
        );

        $encadreur = Role::where('libelle', 'encadreur')->first();
        $encadreur->permissions()->syncWithoutDetaching(
            Permission::whereIn('code', ['projet.consulter', 'projet.valider'])->pluck('id_permission')
        );

        $admin = Role::where('libelle', 'administrateur')->first();
        $admin->permissions()->syncWithoutDetaching(
            Permission::whereIn('code', ['projet.consulter', 'utilisateur.gerer', 'presentation.planifier'])->pluck('id_permission')
        );

        $superAdmin = Role::where('libelle', 'super_administrateur')->first();
        $superAdmin->permissions()->syncWithoutDetaching(
            Permission::all()->pluck('id_permission') // tous les droits
        );

        $juryExterne = Role::where('libelle', 'jury_externe')->first();
        $juryExterne->permissions()->syncWithoutDetaching(
            Permission::whereIn('code', ['jury.noter'])->pluck('id_permission')
        );
    }
}