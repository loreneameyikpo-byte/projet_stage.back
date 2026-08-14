<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class ActivitePlateformeNotification extends Notification
{
    /**
     * @param string $type Identifiant du type d'événement (ex: 'projet_cree', 'statut_change', ...)
     * @param string $message Message lisible affiché dans le menu de notifications
     * @param string|null $lien Chemin frontend vers lequel rediriger au clic (optionnel)
     */
    public function __construct(
        public string $type,
        public string $message,
        public ?string $lien = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'message' => $this->message,
            'lien' => $this->lien,
        ];
    }
}