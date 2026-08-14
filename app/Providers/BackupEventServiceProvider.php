<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Spatie\Backup\Events\BackupWasSuccessful;
use Spatie\Backup\Events\BackupHasFailed;
use Spatie\Backup\Events\CleanupWasSuccessful;
use Spatie\Backup\Events\CleanupHasFailed;
use App\Services\NotificationService;
use App\Models\Utilisateur;
use App\Mail\NotificationSauvegardeReussie;
use App\Mail\NotificationSauvegardeEchouee;

class BackupEventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(BackupWasSuccessful::class, function () {
            $maintenant = now()->format('d/m/Y à H:i');

            NotificationService::notifierSuperAdmins(
                'sauvegarde_reussie',
                'La sauvegarde automatique quotidienne (base de données + fichiers) s\'est terminée avec succès.',
                null
            );

            $this->envoyerAuxSuperAdmins(
                fn ($email) => Mail::to($email)->send(new NotificationSauvegardeReussie($maintenant))
            );
        });

        Event::listen(BackupHasFailed::class, function (BackupHasFailed $event) {
            $maintenant = now()->format('d/m/Y à H:i');
            $messageErreur = $event->exception->getMessage();

            NotificationService::notifierSuperAdmins(
                'sauvegarde_echouee',
                'La sauvegarde automatique a échoué : ' . $messageErreur,
                null
            );

            $this->envoyerAuxSuperAdmins(
                fn ($email) => Mail::to($email)->send(new NotificationSauvegardeEchouee($maintenant, $messageErreur))
            );
        });

        Event::listen(CleanupHasFailed::class, function (CleanupHasFailed $event) {
            NotificationService::notifierSuperAdmins(
                'nettoyage_sauvegarde_echoue',
                'Le nettoyage des anciennes sauvegardes a échoué : ' . $event->exception->getMessage(),
                null
            );
        });
    }

    /**
     * Envoie un email à chaque super administrateur. Les erreurs d'envoi
     * (ex: serveur SMTP momentanément indisponible) sont ignorées une à
     * une pour ne pas empêcher les autres destinataires de recevoir le
     * leur.
     */
    private function envoyerAuxSuperAdmins(\Closure $envoi): void
    {
        $superAdmins = Utilisateur::whereHas('role', fn ($q) => $q->where('libelle', 'super_administrateur'))->get();

        foreach ($superAdmins as $admin) {
            try {
                $envoi($admin->email);
            } catch (\Throwable $e) {
                // On n'interrompt pas la boucle si un envoi échoue pour
                // un destinataire — les autres doivent quand même recevoir le leur.
            }
        }
    }
}