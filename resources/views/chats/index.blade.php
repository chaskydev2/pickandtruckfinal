@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Mis Chats</h1>
    
    <div class="list-group">
        @forelse($chats as $chat)
            @if($chat->bid && $chat->bid->bideable)
                <a href="{{ route('chats.show', $chat) }}" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">
                                Chat con {{ Auth::id() === $chat->bid->user_id ? 
                                    $chat->bid->bideable->user->name : 
                                    $chat->bid->user->name }}
                            </h6>
                            <small>
                                {{ $chat->bid->bideable->origen }} → {{ $chat->bid->bideable->destino }}
                            </small>
                        </div>
                        @if($unread = $chat->unreadCount(Auth::id()))
                            <span class="badge bg-primary rounded-pill">{{ $unread }}</span>
                        @endif
                    </div>
                </a>
            @endif
        @empty
            <div class="text-center text-muted">
                No tienes chats activos
            </div>
        @endforelse
    </div>
</div>
@endsection
