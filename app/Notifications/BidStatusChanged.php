<?php

namespace App\Notifications;

use App\Models\Bid;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BidStatusChanged extends Notification
{
    use Queueable;

    protected $bid;
    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(Bid $bid, string $status)
    {
        $this->bid = $bid;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $bideable = $this->bid->bideable;
        
        // Construir la ruta adecuada basada en el estado de la bid
        $url = $this->status === 'aceptado'
            ? route('work.show', $this->bid)
            : route('bids.index');
            
        return [
            'message' => $this->status === 'aceptado' 
                ? "Tu oferta de $" . number_format($this->bid->monto, 2) . " ha sido aceptada."
                : "Tu oferta de $" . number_format($this->bid->monto, 2) . " ha sido rechazada.",
            'bid_id' => $this->bid->id,
            'status' => $this->status,
            'url' => $url
        ];
    }
}
