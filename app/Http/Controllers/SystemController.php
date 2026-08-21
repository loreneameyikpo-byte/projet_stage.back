<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SystemController extends Controller
{
    /**
     * Déclenche la sauvegarde (nettoyage + création) à distance, protégée
     * par un secret partagé — destiné à être appelé par un service de
     * planification externe gratuit (cron-job.org ou équivalent), puisque
     * l'hébergeur ne propose pas de tâche planifiée gratuite.
     *
     * Le secret est transmis via l'en-tête "X-Backup-Secret" et comparé à
     * la variable d'environnement BACKUP_TRIGGER_SECRET.
     */
    public function declencherSauvegarde(Request $request): JsonResponse
    {
        $secretAttendu = config('app.backup_trigger_secret');
        $secretRecu = $request->header('X-Backup-Secret');

        if (! $secretAttendu || ! $secretRecu || ! hash_equals($secretAttendu, $secretRecu)) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        Artisan::call('backup:clean');
        Artisan::call('backup:run');

        return response()->json([
            'message' => 'Sauvegarde déclenchée avec succès.',
            'sortie' => Artisan::output(),
        ]);
    }
}