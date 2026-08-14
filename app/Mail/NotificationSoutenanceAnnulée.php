<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SoutenanceAnnuleeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $donnees,
        public string $destinataireNom
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Annulation de la soutenance - '.$this->donnees['titre_projet'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.soutenance-annulee',
        );
    }
}