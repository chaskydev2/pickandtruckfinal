<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OfertaExpiredNotification extends Notification
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
        return ['database']; // Solo para la UI por ahora
    }

    public function toDatabase($notifiable)
    {
        $esCarga = $this->tipo === 'carga';
        $routeNamePrefix = $esCarga ? 'ofertas_carga' : 'ofertas';

        return [
            'icon' => 'fas fa-exclamation-circle text-danger',
            'title' => 'Tu publicación ha expirado',
            'message' => "La publicación #{$this->oferta->id} ({$this->oferta->origen} a {$this->oferta->destino}) ha vencido.",
            'actions' => [
                [
                    'text' => 'Volver a Publicar',
                    'url' => route($routeNamePrefix . '.edit', $this->oferta->id),
                    'class' => 'btn-success' // Botón para "re-publicar" (que lleva a editar)
                ],
                [
                    'text' => 'Eliminar',
                    'url' => route($routeNamePrefix . '.destroy', $this->oferta->id),
                    'class' => 'btn-danger',
                    'is_delete' => true
                ]
            ],
            'url' => route($routeNamePrefix . '.show', $this->oferta->id)
        ];
    }
}