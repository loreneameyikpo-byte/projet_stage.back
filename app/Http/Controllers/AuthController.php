<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ChangerMotDePasseRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UtilisateurResource;
use App\Models\Utilisateur;
use App\Http\Requests\Auth\ModifierProfilRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Mail\NotificationMotDePasseReinitialise;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\RateLimiter;


class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        // Limitation des tentatives : 3 essais max par email, blocage
        // temporaire de 15 minutes en cas de dépassement.
        $cleLimitation = 'connexion:' . Str::lower($credentials['email']);
        $tentativesMax = 3;
        $dureeBlocageSecondes = 15 * 60;

        if (RateLimiter::tooManyAttempts($cleLimitation, $tentativesMax)) {
            $secondesRestantes = RateLimiter::availableIn($cleLimitation);
            $minutesRestantes = (int) ceil($secondesRestantes / 60);

            return response()->json([
                'message' => "Trop de tentatives échouées pour cette adresse email. Veuillez réessayer dans {$minutesRestantes} minute" . ($minutesRestantes > 1 ? 's' : '') . '.',
            ], 429);
        }

        $utilisateur = Utilisateur::where('email', $credentials['email'])->first();

        if (! $utilisateur || ! Hash::check($credentials['password'], $utilisateur->mot_de_passe)) {
            RateLimiter::hit($cleLimitation, $dureeBlocageSecondes);

            $tentativesRestantes = max(0, $tentativesMax - RateLimiter::attempts($cleLimitation));

            return response()->json([
                'message' => $tentativesRestantes > 0
                    ? "Identifiants invalides. Il vous reste {$tentativesRestantes} tentative" . ($tentativesRestantes > 1 ? 's' : '') . ' avant blocage temporaire.'
                    : 'Identifiants invalides. Cette adresse email est temporairement bloquée suite à plusieurs échecs.',
            ], 401);
        }

        // Connexion réussie : on efface le compteur de tentatives échouées.
        RateLimiter::clear($cleLimitation);

        if (! $utilisateur->actif) {
            return response()->json([
                'message' => 'Ce compte a été désactivé. Contactez un administrateur.',
            ], 403);
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
    /**
     * L'utilisateur connecté modifie ses propres informations personnelles.
     */
    public function modifierProfil(ModifierProfilRequest $request): JsonResponse
    {
        $utilisateur = $request->user();
        $utilisateur->update($request->validated());

        return response()->json([
            'message' => 'Profil mis à jour avec succès.',
            'utilisateur' => new UtilisateurResource($utilisateur->fresh(['role', 'promotion.niveau', 'filiere', 'specialite'])),
        ]);
    }

    /**
     * Changement de mot de passe — utilisé notamment pour forcer le changement
     * du mot de passe temporaire à la première connexion.
     */
    public function changerMotDePasse(ChangerMotDePasseRequest $request): JsonResponse
    {
        $utilisateur = $request->user();

        $utilisateur->update([
            'mot_de_passe' => Hash::make($request->validated('mot_de_passe')),
            'mot_de_passe_a_changer' => false,
        ]);

        return response()->json([
            'message' => 'Mot de passe modifié avec succès.',
            'utilisateur' => new UtilisateurResource($utilisateur->fresh(['role', 'promotion.niveau', 'filiere', 'specialite'])),
        ]);
    }
    // quand l'utilisateur oublie son mot de passe
    public function motDePasseOublie(Request $request): JsonResponse
{
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    if ($status === Password::RESET_LINK_SENT) {
        return response()->json([
            'message' => 'Un lien de réinitialisation a été envoyé à votre adresse email.',
        ]);
    }

    return response()->json([
        'message' => 'Impossible d\'envoyer le lien de réinitialisation.',
    ], 422);
}

    public function reinitialiserMotDePasse(Request $request): JsonResponse
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($utilisateur, $password) {
            $utilisateur->forceFill([
                'password' => Hash::make($password),
            ])->save();

            event(new PasswordReset($utilisateur));

            Mail::to($utilisateur->email)->send(
                new NotificationMotDePasseReinitialise($utilisateur, $password)
            );
        }
    );

    if ($status === Password::PASSWORD_RESET) {
        return response()->json([
            'message' => 'Mot de passe réinitialisé avec succès.',
        ]);
    }

    return response()->json([
        'message' => 'Le lien de réinitialisation est invalide ou a expiré.',
    ], 422);
}

}