<?php

namespace App\Notifications;

use App\Models\UserDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class DocumentStatusChanged extends Notification
{
    use Queueable;

    protected $document;

    /**
     * Create a new notification instance.
     */
    public function __construct(UserDocument $document)
    {
        $this->document = $document;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $documentName = $this->document->requiredDocument->name ?? 'Documento';
        
        if ($this->document->status === 'aprobado') {
            $title   = '✓ Documento Aprobado';
            $message = "✓ Tu documento '{$documentName}' ha sido aprobado.";
            $icon = 'check-circle';
            $type = 'success';
        } elseif ($this->document->status === 'rechazado') {
            $title   = '✗ Documento Rechazado';
            $message = "✗ Tu documento '{$documentName}' ha sido rechazado.";
            $icon = 'times-circle';
            $type = 'danger';
        } else {
            $title   = '📄 Actualización de Documento';
            $message = "Tu documento '{$documentName}' ha sido actualizado.";
            $icon = 'file-alt';
            $type = 'info';
        }
        
        return [
            'title'   => $title,
            'message' => $message,
            'document_id' => $this->document->id,
            'document_name' => $documentName,
            'status' => $this->document->status,
            'icon' => $icon,
            'type' => $type,
            'url' => route('profile.documents'),
            'comments' => $this->document->comments,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
