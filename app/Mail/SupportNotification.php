<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupportNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $data;

    public function __construct(string $subject, array $data)
    {
        $this->subject = $subject;
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject . ' - Pick & Truck',
            from: config('mail.from.address', 'soporte@pickntruck.com'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.support-notification',
            with: [
                'subject' => $this->subject,
                'data' => $this->data,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
