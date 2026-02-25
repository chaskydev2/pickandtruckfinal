<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MembershipCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $membership;

    public function __construct(User $user, Membership $membership)
    {
        $this->user = $user;
        $this->membership = $membership;
    }

    public function envelope(): Envelope
    {
        $tierNames = [
            'pioneros' => 'Pioneros',
            'visionarios' => 'Visionarios',
            'conservadores' => 'Conservadores',
        ];
        
        $tierName = $tierNames[$this->membership->tier] ?? 'Pick & Truck';

        return new Envelope(
            subject: 'Bienvenido a Pick & Truck - Membresía ' . $tierName,
            from: config('mail.from.address', 'soporte@pickntruck.com'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.membership-created',
            with: [
                'user' => $this->user,
                'membership' => $this->membership,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
