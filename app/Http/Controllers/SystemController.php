<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SystemController extends Controller
{
    /**
     * Vérifie le secret partagé (même secret pour toutes les actions
     * système sensibles de ce contrôleur).
     */
    private function autorise(Request $request): bool
    {
        $secretAttendu = config('app.backup_trigger_secret');
        $secretRecu = $request->header('X-Backup-Secret');

        return $secretAttendu && $secretRecu && hash_equals($secretAttendu, $secretRecu);
    }

    /**
     * Déclenche la sauvegarde (nettoyage + création) à distance, protégée
     * par un secret partagé — destiné à être appelé par un service de
     * planification externe gratuit (cron-job.org ou équivalent).
     */
    public function declencherSauvegarde(Request $request): JsonResponse
    {
        if (! $this->autorise($request)) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        Artisan::call('backup:clean');
        Artisan::call('backup:run');

        return response()->json([
            'message' => 'Sauvegarde déclenchée avec succès.',
            'sortie' => Artisan::output(),
        ]);
    }

    /**
     * Lance les migrations en attente, à distance — utile sur les
     * plateformes (comme Railway) où l'accès shell/SSH n'est pas
     * disponible ou pose problème. Protégée par le même secret partagé.
     *
     * ATTENTION : à utiliser ponctuellement pour la mise en place initiale
     * ou après un déploiement modifiant le schéma, pas comme mécanisme
     * permanent. Peut être supprimée une fois le déploiement stabilisé.
     */
    public function lancerMigrations(Request $request): JsonResponse
    {
        if (! $this->autorise($request)) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        Artisan::call('migrate', ['--force' => true]);

        return response()->json([
            'message' => 'Migrations exécutées.',
            'sortie' => Artisan::output(),
        ]);
    }

    /**
     * Affiche l'état des migrations (appliquées / en attente), à distance.
     * Protégée par le même secret partagé.
     */
    public function statutMigrations(Request $request): JsonResponse
    {
        if (! $this->autorise($request)) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        Artisan::call('migrate:status');

        return response()->json([
            'sortie' => Artisan::output(),
        ]);
    }
}