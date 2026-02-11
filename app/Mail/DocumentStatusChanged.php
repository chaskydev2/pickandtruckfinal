<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $document;

    /**
     * Create a new message instance.
     */
    public function __construct($document)
    {
        $this->document = $document;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->document->status === 'aprobado' 
            ? 'Tu documento ha sido aprobado - PICK N TRUCK' 
            : 'Actualización en tu documento - PICK N TRUCK';

        return new Envelope(
            subject: $subject,
            from: config('mail.from.address', 'soporte@pickntruck.com'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.document-status-changed',
            with: ['document' => $this->document],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
