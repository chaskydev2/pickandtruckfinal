<?php

namespace App\Mail;

use App\Models\User;
use App\Models\DemoRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DemoRequestReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $demoRequest;

    public function __construct(User $user, DemoRequest $demoRequest)
    {
        $this->user = $user;
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
                'user' => $this->user,
                'demoRequest' => $this->demoRequest,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
