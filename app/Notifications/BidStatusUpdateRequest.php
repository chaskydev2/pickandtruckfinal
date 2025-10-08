<?php

namespace App\Notifications;

use App\Models\Bid;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BidStatusUpdateRequest extends Notification
{
    use Queueable;

    protected $bid;
    protected $status;

    /**
     * Create a new notification instance.
     *
     * @param Bid $bid
     * @param string $status
     * @return void
     */
    public function __construct(Bid $bid, $status)
    {
        $this->bid = $bid;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $messageText = $this->status == 'en_proceso'
            ? 'Tu confirmación es necesaria para marcar el trabajo como "en proceso"'
            : 'Tu confirmación es necesaria para finalizar el trabajo';
        
        $bideable = $this->bid->bideable;
        
        return [
            'message' => $messageText,
            'bid_id' => $this->bid->id,
            'status_requested' => $this->status,
            'bideable_id' => $bideable->id,
            'bideable_type' => $this->bid->bideable_type,
            'url' => route('work.show', $this->bid),
        ];
    }
}
