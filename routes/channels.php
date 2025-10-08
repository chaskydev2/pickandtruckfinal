<?php

use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Canal para actualizaciones de estado de ofertas (bids)
Broadcast::channel('bid.{bidId}', function (User $user, $bidId) {
    \Log::info('Solicitud de autenticación para canal de bid', [
        'user_id' => $user->id,
        'bid_id' => $bidId,
        'ip' => request()->ip()
    ]);
    
    try {
        $bid = \App\Models\Bid::with(['user', 'bideable.user'])->findOrFail($bidId);
        
        // Verificar que el usuario sea el propietario de la oferta o el propietario del servicio
        $isAuthorized = $user->id === $bid->user_id || 
                       ($bid->bideable && $user->id === $bid->bideable->user_id);
        
        if (!$isAuthorized) {
            \Log::warning('Intento de acceso no autorizado al canal de bid', [
                'user_id' => $user->id,
                'bid_id' => $bidId,
                'bid_user_id' => $bid->user_id,
                'bideable_user_id' => $bid->bideable ? $bid->bideable->user_id : null
            ]);
            return false;
        }
        
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'bid_id' => $bid->id
        ];
        
        \Log::info('Autenticación exitosa para canal de bid', [
            'user_id' => $user->id,
            'bid_id' => $bidId,
            'user_data' => $userData
        ]);
        
        return $userData;
        
    } catch (\Exception $e) {
        \Log::error('Error al autorizar el canal de bid', [
            'error' => $e->getMessage(),
            'bid_id' => $bidId ?? 'unknown',
            'user_id' => $user->id
        ]);
        return false;
    }
});

// Canal para chats
Broadcast::channel('chat.{chatId}', function (User $user, $chatId) {
    try {
        $chat = Chat::with(['bid.user', 'bid.bideable.user'])->findOrFail($chatId);
        
        // Verificar que el chat tenga una oferta válida
        if (!$chat->bid) {
            \Log::error('El chat no tiene una oferta asociada', ['chat_id' => $chatId]);
            return false;
        }
        
        // Verificar que el usuario sea parte del chat
        $isAuthorized = $user->id === $chat->bid->user_id || 
                       ($chat->bid->bideable && $user->id === $chat->bid->bideable->user_id);
        
        if (!$isAuthorized) {
            \Log::warning('Intento de acceso no autorizado al chat', [
                'user_id' => $user->id,
                'chat_id' => $chatId,
                'bid_user_id' => $chat->bid->user_id,
                'bideable_user_id' => $chat->bid->bideable ? $chat->bid->bideable->user_id : null
            ]);
        }
        
        return $isAuthorized;
    } catch (\Exception $e) {
        \Log::error('Error al autorizar el canal de chat', [
            'user_id' => $user->id,
            'chat_id' => $chatId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return false;
    }
});
