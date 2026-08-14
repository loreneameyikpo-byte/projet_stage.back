<?php

namespace App\Services;

use App\Models\Utilisateur;
use App\Notifications\ActivitePlateformeNotification;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Notifie tous les comptes super_administrateur d'un événement survenu
     * sur la plateforme. Silencieux si aucun super admin n'existe.
     */
    public static function notifierSuperAdmins(string $type, string $message, ?string $lien = null): void
    {
        $superAdmins = Utilisateur::whereHas('role', fn ($q) => $q->where('libelle', 'super_administrateur'))->get();

        if ($superAdmins->isEmpty()) {
            return;
        }

        Notification::send($superAdmins, new ActivitePlateformeNotification($type, $message, $lien));
    }
}