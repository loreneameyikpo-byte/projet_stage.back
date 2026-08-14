<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationSauvegardeEchouee extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $dateHeure,
        public string $messageErreur,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠ Échec de la sauvegarde Projetis',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.sauvegarde-echouee',
        );
    }
}