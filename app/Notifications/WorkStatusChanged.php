<?php

namespace App\Notifications;

use App\Models\Bid;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkStatusChanged extends Notification
{
    use Queueable;

    protected $bid;
    protected $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Bid $bid, string $newStatus)
    {
        $this->bid = $bid;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        $statusText = [
            'en_proceso' => 'en proceso',
            'en_camino' => 'en camino',
            'entregado' => 'entregado',
            'completado' => 'completado',
            'cancelado' => 'cancelado'
        ][$this->newStatus] ?? $this->newStatus;
        
        return [
            'message' => "El estado del servicio ha cambiado a " . $statusText,
            'bid_id' => $this->bid->id,
            'new_status' => $this->newStatus,
            'url' => route('work.show', $this->bid)
        ];
    }
}
