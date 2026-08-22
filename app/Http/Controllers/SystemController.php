<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

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
     * par un secret partagé.
     */
    public function declencherSauvegarde(Request $request): JsonResponse
    {
        if (! $this->autorise($request)) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        Artisan::call('backup:clean');
        Artisan::call('backup:run');

        return response()->json(['message' => 'Sauvegarde déclenchée avec succès.']);
    }

    /**
     * Lance les migrations en attente, à distance.
     */
    public function lancerMigrations(Request $request): JsonResponse
    {
        if (! $this->autorise($request)) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $code = Artisan::call('migrate', ['--force' => true]);

        return response()->json([
            'message' => $code === 0 ? 'Migrations exécutées avec succès.' : 'La commande a retourné un code d\'erreur.',
            'code_retour' => $code,
            'etat' => $this->calculerEtatMigrations(),
        ]);
    }

    /**
     * Affiche l'état des migrations (appliquées / en attente), à distance
     * — en interrogeant directement la table migrations plutôt que de
     * capturer la sortie console d'Artisan (peu fiable hors contexte CLI).
     */
    public function statutMigrations(Request $request): JsonResponse
    {
        if (! $this->autorise($request)) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        return response()->json($this->calculerEtatMigrations());
    }

    private function calculerEtatMigrations(): array
    {
        // Migrations déjà appliquées, selon la table "migrations".
        $appliquees = DB::table('migrations')->pluck('migration')->all();

        // Tous les fichiers de migration présents dans le code déployé.
        $fichiers = collect(File::files(database_path('migrations')))
            ->map(fn ($f) => pathinfo($f->getFilename(), PATHINFO_FILENAME))
            ->sort()
            ->values()
            ->all();

        $enAttente = array_values(array_diff($fichiers, $appliquees));

        return [
            'nb_appliquees' => count($appliquees),
            'nb_en_attente' => count($enAttente),
            'en_attente' => $enAttente,
        ];
    }
}