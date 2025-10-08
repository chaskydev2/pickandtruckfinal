<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class GenericNotification extends Notification
{
    use Queueable;

    protected $message;
    protected $url;
    protected $icon;
    
    /**
     * Create a new notification instance.
     *
     * @param string $message El mensaje de la notificación
     * @param string $url La URL a la que dirigir al hacer clic
     * @param string $icon El icono de Font Awesome (ej: 'fas fa-bell')
     * @return void
     */
    public function __construct(string $message, string $url, string $icon = 'fas fa-bell')
    {
        $this->message = $message;
        $this->url = $url;
        $this->icon = $icon;
        
        Log::info("GenericNotification creada: {$message}");
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
        return [
            'message' => $this->message,
            'url' => $this->url,
            'icon' => $this->icon
        ];
    }
}
