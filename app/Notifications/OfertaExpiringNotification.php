<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfertaExpiringNotification extends Notification
{
    use Queueable;

    protected $oferta;
    protected $tipo;

    public function __construct($oferta, $tipo)
    {
        $this->oferta = $oferta;
        $this->tipo = $tipo;
    }

    public function via($notifiable)
    {
        // Dejamos solo 'database' para enfocarnos en la interfaz
        return ['database'];
    }

    // El método toMail() se puede mantener o eliminar. No lo usaremos para la UI.

    public function toDatabase($notifiable)
    {
        $esCarga = $this->tipo === 'carga';
        $routeNamePrefix = $esCarga ? 'ofertas_carga' : 'ofertas';

        return [
            'icon' => 'fas fa-clock text-warning',
            'title' => 'Tu publicación está por vencer',
            'message' => "La publicación #{$this->oferta->id} ({$this->oferta->origen} a {$this->oferta->destino}) vence en menos de 24 horas.",
            
            // <-- CAMBIO CLAVE: Añadimos la estructura para los botones de acción
            'actions' => [
                [
                    'text' => 'Modificar',
                    'url' => route($routeNamePrefix . '.edit', $this->oferta->id),
                    'class' => 'btn-warning'
                ],
                [
                    'text' => 'Eliminar',
                    'url' => route($routeNamePrefix . '.destroy', $this->oferta->id),
                    'class' => 'btn-danger',
                    'is_delete' => true // Bandera especial para el botón de eliminar
                ]
            ],
            // URL por defecto si hacen clic en el cuerpo de la notificación
            'url' => route($routeNamePrefix . '.show', $this->oferta->id)
        ];
    }
}
