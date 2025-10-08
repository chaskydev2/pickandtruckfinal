<?php

namespace App\Notifications;

use App\Models\Bid;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class BidReceived extends Notification
{
    use Queueable;

    protected $bid;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Bid $bid)
    {
        $this->bid = $bid;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // Añadir logs para debug
        Log::info("Enviando notificación BidReceived a usuario #{$notifiable->id} a través de 'database'");
        
        // Asegurar que se envía a la base de datos
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
        try {
            $bideable = $this->bid->bideable;
            $route = $this->bid->bideable_type === 'App\Models\OfertaCarga' ? 'ofertas_carga.show' : 'ofertas.show';
            
            Log::info("Generando datos para notificación BidReceived #{$this->bid->id} para la ruta {$route}", [
                'bideable_id' => $bideable->id,
                'bideable_type' => get_class($bideable),
                'user_id' => $notifiable->id
            ]);
            
            $data = [
                'message' => "Has recibido una oferta de $" . number_format($this->bid->monto, 2) . " en tu publicación",
                'bid_id' => $this->bid->id,
                'bideable_id' => $bideable->id,
                'bideable_type' => $this->bid->bideable_type,
                'from_user_id' => $this->bid->user_id,
                'from_user_name' => $this->bid->user->name,
                'url' => route($route, $bideable->id),
                'icon' => 'fas fa-hand-holding-usd text-success'
            ];
            
            Log::info("Datos de notificación generados correctamente", $data);
            
            return $data;
        } catch (\Exception $e) {
            Log::error("Error en BidReceived toArray: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            // Devolver datos básicos para que al menos llegue algo
            return [
                'message' => "Has recibido una nueva oferta",
                'bid_id' => $this->bid->id,
                'url' => route('bids.received'),
                'icon' => 'fas fa-bell text-warning'
            ];
        }
    }
}
