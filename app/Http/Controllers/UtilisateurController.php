<?php

namespace App\Http\Controllers;

use App\Http\Requests\Utilisateur\CreerUtilisateurRequest;
use App\Http\Requests\Utilisateur\ModifierUtilisateurRequest;
use App\Http\Resources\UtilisateurResource;
use App\Models\Utilisateur;
use App\Mail\CompteCreeMail;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UtilisateurController extends Controller
{
    /**
     * UC4 - Liste des utilisateurs, filtrable par rôle via ?role=etudiant
     */
    public function index(Request $request): JsonResponse
    {
        $query = Utilisateur::with(['role', 'promotion.niveau', 'filiere', 'specialite']);

        if ($request->filled('role')) {
            $query->whereHas('role', fn ($q) => $q->where('libelle', $request->query('role')));
        }

        return response()->json([
            'utilisateurs' => UtilisateurResource::collection($query->orderBy('nom')->get()),
        ]);
    }

    /**
     * UC4 - Créer un étudiant ou un encadreur (ou tout autre rôle).
     */
    public function store(CreerUtilisateurRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $motDePasseTemporaire = Str::password(12);
        
        $utilisateur = Utilisateur::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'mot_de_passe' => Hash::make($motDePasseTemporaire),
            'contacts' => $validated['contacts'] ?? null,
            'adresse' => $validated['adresse'] ?? null,
            'id_role' => $validated['id_role'],
            'id_promotion' => $validated['id_promotion'] ?? null,
            'id_filiere' => $validated['id_filiere'] ?? null,
            'id_specialite' => $validated['id_specialite'] ?? null,
            'actif' => true,
            'mot_de_passe_a_changer' => true,
        ]);

        Mail::to($utilisateur->email)->send(new CompteCreeMail($motDePasseTemporaire, $utilisateur));

        return response()->json([
            'message' => 'Utilisateur créé avec succès.',
            // 'mot_de_passe_temporaire' => $motDePasseTemporaire,
            'utilisateur' => new UtilisateurResource($utilisateur->load(['role', 'promotion.niveau', 'filiere', 'specialite'])),
        ], 201);
    }

    public function show(Utilisateur $utilisateur): JsonResponse
    {
        return response()->json([
            'utilisateur' => new UtilisateurResource($utilisateur->load(['role', 'promotion.niveau', 'filiere', 'specialite'])),
        ]);
    }

    /**
     * UC4 - Modifier un étudiant ou un encadreur.
     */
    public function update(ModifierUtilisateurRequest $request, Utilisateur $utilisateur): JsonResponse
    {
        $utilisateur->update($request->validated());

        return response()->json([
            'message' => 'Utilisateur modifié avec succès.',
            'utilisateur' => new UtilisateurResource($utilisateur->fresh(['role', 'promotion.niveau', 'filiere', 'specialite'])),
        ]);
    }

    /**
     * UC4 - Supprimer un étudiant ou un encadreur.
     */
    public function destroy(Utilisateur $utilisateur): JsonResponse
    {
         /**$roleConnecte = $request->user()?->role?->libelle;
        $roleCible = $utilisateur->role?->libelle;

        if (in_array($roleCible, ['administrateur', 'super_administrateur'], true) && $roleConnecte !== 'super_administrateur') {
            return response()->json([
                'message' => 'Seul le super administrateur peut supprimer un compte administrateur.',
            ], 403);
        }*/
        
        $utilisateur->delete();

        return response()->json([
            'message' => 'Utilisateur supprimé avec succès.',
        ]);
    }
}