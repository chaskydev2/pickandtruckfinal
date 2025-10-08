@extends('layouts.app')

@section('content')
<div class="container py-4">
    @if(!$chat->bid || !$chat->bid->bideable)
        <div class="alert alert-warning">
            <h4 class="alert-heading">Oferta no disponible</h4>
            <p>La oferta relacionada con este chat ya no está disponible.</p>
            <hr>
            <p class="mb-0">
                <a href="{{ route('chats.index') }}" class="btn btn-outline-primary">
                    Volver a mis chats
                </a>
            </p>
        </div>
    @else
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    Chat con {{ Auth::id() === $chat->bid->user_id ? 
                        $chat->bid->bideable->user->name : 
                        $chat->bid->user->name }}
                </h5>
                <a href="{{ route('chats.index') }}" class="btn btn-primary btn-sm">
                    Volver a chats
                </a>
            </div>
            <div class="card-body">
                <div id="chat-messages" class="chat-messages mb-4" style="height: 500px; overflow-y: auto;">
                    @foreach($chat->messages as $message)
                        <div class="message mb-3 {{ $message->user_id === Auth::id() ? 'text-end' : '' }}" data-id="{{ $message->id }}">
                            @if(isset($message->is_system) && $message->is_system)
                                <div class="d-inline-block p-2 rounded bg-light text-center w-100 text-muted small">
                                    <i class="fas fa-info-circle me-1"></i>{{ $message->content }}
                                </div>
                            @else
                                <div class="d-inline-block p-2 rounded {{ $message->user_id === Auth::id() ? 'bg-primary text-white' : 'bg-light' }}" style="max-width: 80%;">
                                    <div class="small fw-bold mb-1">{{ $message->user->name }}</div>
                                    <div class="message-content">{{ $message->content }}</div>
                                    <div class="small text-{{ $message->user_id === Auth::id() ? 'light' : 'muted' }}">
                                        {{ $message->created_at->format('H:i') }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <form id="message-form" action="{{ route('chats.message', $chat) }}" method="POST" class="mt-3">
                    @csrf
                    <input type="hidden" name="_ajax" value="true">
                    <div class="input-group">
                        <input type="text" name="message" id="message-input" class="form-control" placeholder="Escribe un mensaje..." required>
                        <button type="submit" id="send-message-btn" class="btn btn-primary">
                            <span class="button-text">Enviar</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pequeño retraso para asegurar que el DOM esté completamente listo
    setTimeout(function() {
        const chatElement = document.querySelector('#chat-messages');
        if (!chatElement) {
            console.warn('Elemento del chat no encontrado en chats/show');
            return;
        }
        
        if (typeof window.ChatHandler === 'undefined') {
            console.error('Error: La clase ChatHandler no está disponible. Asegúrate de que app.js o un script similar la cargue.');
            return;
        }
        
        try {
            const chat = new window.ChatHandler({
                chatContainerSelector: '#chat-messages',
                messageFormSelector: '#message-form',
                messageInputSelector: '#message-input',
                sendButtonSelector: '#send-message-btn',
                chatId: {{ $chat->id }},
                userId: {{ Auth::id() }},
                isCompact: false
            });
            
            chat.init().catch(error => {
                console.error('Error al inicializar el chat en chats/show:', error);
            });
            
            window.mainChat = chat; // Para depuración
            
        } catch (error) {
            console.error('Error general al inicializar el chat:', error);
        }
    }, 500);
});
</script>
@endpush