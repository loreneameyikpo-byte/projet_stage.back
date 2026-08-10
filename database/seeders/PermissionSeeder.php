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
            ['code' => 'manage_students', 'libelle' => 'Gérer les étudiants', 'description' => 'Créer, modifier et supprimer des comptes étudiants'],
            ['code' => 'manage_supervisors', 'libelle' => 'Gérer les encadreurs', 'description' => 'Créer, modifier et supprimer des comptes encadreurs'],
            ['code' => 'manage_jury', 'libelle' => 'Gérer le jury', 'description' => 'Gérer les membres de jury externes'],
            ['code' => 'manage_projects', 'libelle' => 'Gérer les projets', 'description' => 'Voir et gérer tous les projets, affecter des encadreurs'],
            ['code' => 'plan_defenses', 'libelle' => 'Planifier les soutenances', 'description' => 'Créer et modifier les plannings de soutenance'],
            ['code' => 'manage_promotions', 'libelle' => 'Gérer les promotions', 'description' => 'Créer et modifier les promotions'],
            ['code' => 'manage_specialties', 'libelle' => 'Gérer les filières', 'description' => 'Ajouter et modifier les filières/spécialités'],
            ['code' => 'manage_rooms', 'libelle' => 'Gérer les salles', 'description' => 'Gérer les salles de soutenance'],
           // ['code' => 'manage_pricing', 'libelle' => 'Gérer les tarifs', 'description' => "Définir les tarifs d'encadrement"],
            ['code' => 'manage_admins', 'libelle' => 'Gérer les administrateurs', 'description' => 'Créer et gérer les comptes administrateurs'],
            ['code' => 'manage_roles', 'libelle' => 'Gérer les rôles', 'description' => 'Configurer les permissions par rôle'],
            ['code' => 'system_settings', 'libelle' => 'Paramètres système', 'description' => 'Modifier la configuration globale'],
           // ['code' => 'view_reports', 'libelle' => 'Voir les rapports', 'description' => 'Accéder aux rapports consolidés'],
            // ['code' => 'export_data', 'libelle' => 'Exporter les données', 'description' => 'Exporter les données en CSV/PDF'],
            
            // Permissions liées aux projets (étudiant / encadreur)
            ['code' => 'projet.creer', 'libelle' => 'Créer un projet', 'description' => 'Soumettre un nouveau projet de fin de formation'],
            ['code' => 'projet.consulter', 'libelle' => 'Consulter un projet', 'description' => "Voir le détail et l'historique d'un projet"],
            ['code' => 'projet.valider', 'libelle' => 'Valider un projet', 'description' => 'Approuver un projet ou demander des corrections'],
            ['code' => 'utilisateur.gerer', 'libelle' => 'Gérer les utilisateurs', 'description' => 'Créer, modifier et supprimer des comptes utilisateurs'],
            ['code' => 'presentation.planifier', 'libelle' => 'Planifier une présentation', 'description' => 'Programmer une soutenance et composer le jury'],
            ['code' => 'jury.noter', 'libelle' => 'Saisir une note de jury', 'description' => "Attribuer une note lors d'une soutenance"],
        ];


        foreach ($permissions as $perm) {
            Permission::updateOrCreate(['code' => $perm['code']], $perm);
        }

        $codesAdmin = [
            'manage_students', 'manage_supervisors', 'manage_jury', 'manage_projects',
            'plan_defenses', 'manage_promotions', 'manage_specialties', 'manage_rooms', 'manage_pricing',
        ];

        $administrateur = Role::where('libelle', 'administrateur')->first();
        $administrateur->permissions()->sync(
            Permission::whereIn('code', $codesAdmin)->pluck('id_permission')
        );

        $superAdministrateur = Role::where('libelle', 'super_administrateur')->first();
        $superAdministrateur->permissions()->sync(
            Permission::all()->pluck('id_permission')
        );
    }
}