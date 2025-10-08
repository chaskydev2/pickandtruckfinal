<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewPublication implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public string $type; // 'ruta' | 'carga'
    public array $publication;

    public function __construct(string $type, array $publication)
    {
        $this->type = $type;
        $this->publication = $publication;
    }

    public function broadcastOn()
    {
        return new Channel('publications'); // canal público
    }

    public function broadcastAs()
    {
        return 'publication.created';
    }

    public function broadcastWith(): array
    {
        return [
            'type' => $this->type,
            'publication' => $this->publication,
        ];
    }
}
