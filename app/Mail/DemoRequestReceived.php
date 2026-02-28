<?php

namespace App\Mail;

use App\Models\DemoRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DemoRequestReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $demoRequest;

    public function __construct(DemoRequest $demoRequest)
    {
        $this->demoRequest = $demoRequest;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu solicitud de demo ha sido recibida - Pick & Truck',
            from: config('mail.from.address', 'soporte@pickntruck.com'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.demo-request-received',
            with: [
                'demoRequest' => $this->demoRequest,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
