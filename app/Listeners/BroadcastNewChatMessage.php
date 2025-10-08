<?php

namespace App\Listeners;

use App\Events\NewChatMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BroadcastNewChatMessage
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewChatMessage $event): void
    {
        // El evento ya está configurado para broadcast, no es necesario hacer nada aquí
        // solo queremos asegurarnos de que el evento se maneje correctamente
    }
}
