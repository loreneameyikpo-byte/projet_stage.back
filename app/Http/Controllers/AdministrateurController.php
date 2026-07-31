<?php

namespace App\Http\Controllers;

use App\Http\Requests\Utilisateur\CreerAdministrateurRequest;
use App\Http\Requests\Utilisateur\ModifierUtilisateurRequest;
use App\Http\Resources\UtilisateurResource;
use App\Mail\CompteCreeMail;
use App\Models\Role;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdministrateurController extends Controller
{
    /**
     * Liste des administrateurs (rôle administrateur uniquement, pas super_administrateur),
     * avec les indicateurs affichés sur la page de gestion.
     */
    public function index(): JsonResponse
    {
        $administrateurs = Utilisateur::whereHas('role', fn ($q) => $q->where('libelle', 'administrateur'))
            ->with('role')
            ->orderBy('nom')
            ->get();

        return response()->json([
            'administrateurs' => UtilisateurResource::collection($administrateurs),
            'stats' => [
                'total' => $administrateurs->count(),
                'actifs' => $administrateurs->where('actif', true)->count(),
                'inactifs' => $administrateurs->where('actif', false)->count(),
                'recents' => $administrateurs->where('created_at', '>=', now()->subYear())->count(),
            ],
        ]);
    }

    public function store(CreerAdministrateurRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $motDePasseTemporaire = Str::password(12);
        $roleAdmin = Role::where('libelle', 'administrateur')->firstOrFail();

        $utilisateur = DB::transaction(function () use ($validated, $motDePasseTemporaire, $roleAdmin) {
        $utilisateur = Utilisateur::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'mot_de_passe' => Hash::make($motDePasseTemporaire),
            'contacts' => $validated['contacts'] ?? null,
            'adresse' => $validated['adresse'] ?? null,
            'id_role' => $roleAdmin->id_role,
            'actif' => true,
            'mot_de_passe_a_changer' => true,
        ]);
// Si l'envoi échoue, l'exception remonte et DB::transaction annule automatiquement la création du compte genre un rollback quoi.
        Mail::to($utilisateur->email)->send(new CompteCreeMail($motDePasseTemporaire, $utilisateur));

        return $utilisateur;
        });

        return response()->json([
            'message' => 'Administrateur créé avec succès.',
            // 'mot_de_passe_temporaire' => $motDePasseTemporaire,
            'administrateur' => new UtilisateurResource($utilisateur->load('role')),
        ], 201);
    }

    public function update(ModifierUtilisateurRequest $request, Utilisateur $utilisateur): JsonResponse
    {
        $utilisateur->update($request->validated());

        return response()->json([
            'message' => 'Administrateur modifié avec succès.',
            'administrateur' => new UtilisateurResource($utilisateur->fresh('role')),
        ]);
    }

    /**
     * Basculer le statut actif/inactif (désactivation plutôt que suppression).
     */
    public function toggleActif(Utilisateur $utilisateur): JsonResponse
    {
        $utilisateur->update(['actif' => ! $utilisateur->actif]);

        return response()->json([
            'message' => $utilisateur->actif ? 'Compte réactivé.' : 'Compte désactivé.',
            'administrateur' => new UtilisateurResource($utilisateur->fresh()),
        ]);
    }

    public function destroy(Utilisateur $utilisateur): JsonResponse
    {
        $utilisateur->delete();

        return response()->json(['message' => 'Administrateur supprimé avec succès.']);
    }

    /**
     * Régénère un mot de passe temporaire et le renvoie par email,
     * utile si l'administrateur a perdu ses identifiants avant sa première connexion.
     */
    public function renvoyerIdentifiants(Utilisateur $utilisateur): JsonResponse
    {
        $motDePasseTemporaire = Str::password(12);

        $utilisateur->update([
            'mot_de_passe' => Hash::make($motDePasseTemporaire),
            'mot_de_passe_a_changer' => true,
        ]);

        Mail::to($utilisateur->email)->send(new CompteCreeMail($motDePasseTemporaire, $utilisateur));

        return response()->json([
            'message' => 'Identifiants renvoyés par email avec succès.',
        ]);
    }
}