<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationSauvegardeReussie extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $dateHeure,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sauvegarde Projetis effectuée avec succès',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.sauvegarde-reussie',
        );
    }
}