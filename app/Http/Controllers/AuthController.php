<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UtilisateurResource;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $utilisateur = Utilisateur::where('email', $credentials['email'])->first();

        if (! $utilisateur || ! Hash::check($credentials['password'], $utilisateur->mot_de_passe)) {
            return response()->json([
                'message' => 'Identifiants invalides.',
            ], 401);
        }

        $token = $utilisateur->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'utilisateur' => new UtilisateurResource(
                $utilisateur->load(['role', 'promotion.niveau', 'filiere', 'specialite'])
            ),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $utilisateur = $request->user()->load(['role', 'promotion.niveau', 'filiere', 'specialite']);

        return response()->json([
            'utilisateur' => new UtilisateurResource($utilisateur),
        ]);
    }
}