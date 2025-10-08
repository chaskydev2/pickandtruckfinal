<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewChatMessage extends Notification
{
    use Queueable;

    protected $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
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
        $chat = $this->message->chat;
        $sender = $this->message->user;
        $shortContent = substr($this->message->content, 0, 30) . (strlen($this->message->content) > 30 ? '...' : '');
        
        return [
            'type' => 'chat_message',
            'message' => "Nuevo mensaje de {$sender->name}: {$shortContent}",
            'chat_id' => (string)$chat->id, // Aseguramos que sea string para la comparación
            'url' => route('chats.show', $chat->id),
            'sender_name' => $sender->name,
            'sender_id' => $sender->id,
            'content_preview' => $shortContent,
            'timestamp' => now()->toIso8601String()
        ];
    }
}
