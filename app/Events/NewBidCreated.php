<?php

namespace App\Events;

use App\Models\Bid;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewBidCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bid;

    /**
     * Create a new event instance.
     */
    public function __construct(Bid $bid)
    {
        // Cargar las relaciones necesarias
        $this->bid = $bid->load(['user', 'bideable.user']);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast al usuario que recibe la oferta (dueño de la publicación)
        $ownerId = $this->bid->bideable->user_id;
        
        return [
            new PrivateChannel('App.Models.User.' . $ownerId),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'NewBidCreated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'bid_id' => $this->bid->id,
            'monto' => $this->bid->monto,
            'fecha_hora' => $this->bid->fecha_hora,
            'comentario' => $this->bid->comentario,
            'estado' => $this->bid->estado,
            'user' => [
                'id' => $this->bid->user->id,
                'name' => $this->bid->user->name,
            ],
            'bideable_type' => class_basename($this->bid->bideable_type),
            'bideable_id' => $this->bid->bideable_id,
            'created_at' => $this->bid->created_at->toISOString(),
        ];
    }
}
