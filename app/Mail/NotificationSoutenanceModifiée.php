<?php

namespace App\Mail;

use App\Models\Presentation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SoutenanceModifieeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Presentation $presentation,
        public string $destinataireNom
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Modification de votre soutenance - '.$this->presentation->projet->titre,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.soutenance-modifiee',
        );
    }
}