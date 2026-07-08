<?php

namespace App\Http\Controllers;

use App\Http\Requests\Utilisateur\CreerUtilisateurRequest;
use App\Http\Requests\Utilisateur\ModifierUtilisateurRequest;
use App\Http\Resources\UtilisateurResource;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        $utilisateur = Utilisateur::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'mot_de_passe' => Hash::make($validated['password']),
            'contacts' => $validated['contacts'] ?? null,
            'adresse' => $validated['adresse'] ?? null,
            'id_role' => $validated['id_role'],
            'id_promotion' => $validated['id_promotion'] ?? null,
            'id_filiere' => $validated['id_filiere'] ?? null,
            'id_specialite' => $validated['id_specialite'] ?? null,
        ]);

        return response()->json([
            'message' => 'Utilisateur créé avec succès.',
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
        $utilisateur->delete();

        return response()->json([
            'message' => 'Utilisateur supprimé avec succès.',
        ]);
    }
}