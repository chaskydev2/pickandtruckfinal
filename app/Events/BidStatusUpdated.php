<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Bid;

class BidStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bid;
    public $newStatus;
    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Bid $bid, $newStatus, $message = null)
    {
        $this->bid = $bid;
        $this->newStatus = $newStatus;
        $this->message = $message;
        
        // No incluir relaciones en los datos de transmisión para evitar problemas de serialización
        $this->bid = $bid->withoutRelations();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('bid.' . $this->bid->id);
    }
    
    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'bid' => [
                'id' => $this->bid->id,
                'estado' => $this->newStatus,
                'confirmacion_usuario_a' => $this->bid->confirmacion_usuario_a,
                'confirmacion_usuario_b' => $this->bid->confirmacion_usuario_b,
                'user_id' => $this->bid->user_id,
                'bideable_user_id' => $this->bid->bideable ? $this->bid->bideable->user_id : null,
            ],
            'message' => [
                'content' => $this->message,
                'is_system' => true
            ]
        ];
    }
    
    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'BidStatusUpdated';
    }
}
