<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bid;
use App\Models\Chat;
use App\Models\Message;
use App\Notifications\NewChatMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Lista los chats del usuario autenticado.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $user = Auth::user();
            
            $chats = Chat::whereHas('bid', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('bideable', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
            })->with(['bid.bideable', 'bid.user', 'messages'])->get();
            
            // Filtrar solo chats válidos
            $chats = $chats->filter(function ($chat) {
                return $chat->bid && $chat->bid->bideable;
            });
            
            return response()->json([
                'success' => true,
                'data' => $chats->values()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al listar chats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar los chats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra un chat específico con sus mensajes.
     * Acepta tanto el ID del chat como el ID de la oferta (bid_id).
     * 
     * @param int $chatId Puede ser el ID del chat o el bid_id
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Muestra un chat específico con sus mensajes.
     * Acepta tanto el ID del chat como el ID de la oferta (bid_id).
     * 
     * @param int|string $chatId Puede ser el ID del chat o el bid_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($chatId)
    {
        try {
            $user = Auth::user();
            
            // Primero intentar encontrar el chat por ID
            $chat = Chat::with(['bid', 'bid.bideable', 'bid.user', 'messages.user'])->find($chatId);
            
            // Si no se encuentra, buscar por bid_id
            if (!$chat) {
                $chat = Chat::with(['bid', 'bid.bideable', 'bid.user', 'messages.user'])
                    ->where('bid_id', $chatId)
                    ->first();
                
                if (!$chat) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se encontró el chat para esta oferta.'
                    ], 404);
                }
            }
            
            // Verificar que el bid y su oferta relacionada existan
            if (!$chat->bid || !$chat->bid->bideable) {
                return response()->json([
                    'success' => false,
                    'message' => 'El chat no está disponible porque la oferta relacionada ya no existe.'
                ], 404);
            }
            
            // Verificar que el usuario sea parte del chat
            if (!in_array($user->id, [$chat->bid->user_id, $chat->bid->bideable->user_id])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para ver este chat.'
                ], 403);
            }
            
            // Cargar relaciones necesarias si no se cargaron antes
            if (!$chat->relationLoaded('bid') || !$chat->relationLoaded('messages')) {
                $chat->load(['bid', 'bid.bideable', 'bid.user', 'messages.user']);
            }
            
            // Marcar mensajes como leídos
            $unreadMessages = $chat->messages()
                ->where('user_id', '!=', $user->id)
                ->where('read', false);
                
            if ($unreadMessages->exists()) {
                $unreadMessages->update(['read' => true]);
                // Recargar los mensajes para asegurar que tenemos los estados actualizados
                $chat->load('messages');
            }
            
            return response()->json([
                'success' => true,
                'data' => $chat
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al mostrar chat #' . $chatId . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar el chat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Envía un mensaje en un chat existente.
     * 
     * @param Request $request
     * @param Chat|int $chat Puede ser un modelo Chat o un ID de chat
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Envía un mensaje en un chat existente.
     * 
     * @param Request $request
     * @param mixed $chat Puede ser un modelo Chat, un ID de chat o un ID de oferta (bid_id)
     * @return \Illuminate\Http\JsonResponse
     */
    public function message(Request $request, $chat)
    {
        try {
            // Si $chat es un ID, buscar el modelo Chat
            if (!($chat instanceof Chat)) {
                $chat = Chat::find($chat);
                
                // Si no se encuentra por ID, intentar buscar por bid_id
                if (!$chat) {
                    $chat = Chat::where('bid_id', $chat)->first();
                    
                    if (!$chat) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No se encontró el chat especificado.'
                        ], 404);
                    }
                }
            }
            
            $user = Auth::user();
            $lastMessageId = $request->query('last_message_id', 0);
            
            // Cargar las relaciones necesarias
            $chat->load(['bid', 'bid.bideable']);
            
            // Verificar que el chat tenga una oferta válida
            if (!$chat->bid || !$chat->bid->bideable) {
                return response()->json([
                    'success' => false,
                    'message' => 'El chat no está disponible porque la oferta relacionada ya no existe.'
                ], 404);
            }
            
            // Verificar que el usuario tenga permiso para ver este chat
            if (!in_array($user->id, [$chat->bid->user_id, $chat->bid->bideable->user_id])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para ver este chat.'
                ], 403);
            }
            
            $request->validate([
                'message' => 'required|string|max:1000',
            ]);
            
            // Crear el mensaje
            $message = new Message([
                'user_id' => $user->id,
                'content' => $request->message,
                'read' => false
            ]);
            
            // Guardar el mensaje en la base de datos
            $chat->messages()->save($message);
            
            // Cargar la relación de usuario para la respuesta
            $message->load('user');
            
            // Disparar el evento de nuevo mensaje
            broadcast(new \App\Events\NewChatMessage($message))->toOthers();
            
            // Notificar al otro usuario del chat si existe
            try {
                $otherUserId = $user->id === $chat->bid->user_id 
                    ? $chat->bid->bideable->user_id 
                    : $chat->bid->user_id;
                
                $otherUser = \App\Models\User::find($otherUserId);
                if ($otherUser) {
                    $otherUser->notify(new NewChatMessage($message));
                }
            } catch (\Exception $e) {
                // Registrar el error pero no fallar la solicitud
                \Log::error('Error al enviar notificación de mensaje', [
                    'message_id' => $message->id ?? null,
                    'chat_id' => $chat->id ?? null,
                    'user_id' => $user->id ?? null,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Error al enviar mensaje en chat #' . $chat->id . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el mensaje',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene los mensajes nuevos desde un ID específico.
     * 
     * @param Request $request
     * @param Chat|int $chat Puede ser un modelo Chat o un ID de chat
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Obtiene los mensajes nuevos desde un ID específico.
     * 
     * @param Request $request
     * @param mixed $chat Puede ser un modelo Chat, un ID de chat o un ID de oferta (bid_id)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNewMessages(Request $request, $chat)
    {
        try {
            $user = Auth::user();
            $lastId = (int)$request->input('last_id', 0);
            
            // Si $chat es un ID, buscar el modelo Chat
            if (!($chat instanceof Chat)) {
                $chat = Chat::with(['bid', 'bid.bideable', 'bid.user', 'bid.bideable.user'])
                    ->find($chat);
                
                // Si no se encuentra por ID, intentar buscar por bid_id
                if (!$chat) {
                    $chat = Chat::with(['bid', 'bid.bideable', 'bid.user', 'bid.bideable.user'])
                        ->where('bid_id', $chat)
                        ->first();
                    
                    if (!$chat) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No se encontró el chat especificado.'
                        ], 404);
                    }
                }
            } else {
                // Cargar relaciones necesarias si $chat ya es un modelo
                $chat->load(['bid', 'bid.bideable', 'bid.user', 'bid.bideable.user']);
            }
            
            // Verificar que el chat tenga una oferta válida
            if (!$chat->bid || !$chat->bid->bideable) {
                return response()->json([
                    'success' => false,
                    'message' => 'El chat no está disponible porque la oferta relacionada ya no existe.'
                ], 404);
            }
            
            // Verificar que el usuario tenga permiso para ver este chat
            if (!in_array($user->id, [$chat->bid->user_id, $chat->bid->bideable->user_id])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para ver este chat.'
                ], 403);
            }
            
            // Obtener mensajes nuevos
            $query = $chat->messages()
                ->where('id', '>', $lastId)
                ->with('user')
                ->orderBy('created_at', 'asc');
            
            $messages = $query->get();
            
            // Marcar mensajes como leídos si el usuario actual no es el remitente
            $unreadMessages = $messages->filter(function($message) use ($user) {
                return $message->user_id !== $user->id && !$message->read;
            });
            
            if ($unreadMessages->isNotEmpty()) {
                Message::whereIn('id', $unreadMessages->pluck('id'))->update(['read' => true]);
                // Recargar los mensajes para asegurar que tenemos los estados actualizados
                $messages->load('user');
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'chat_id' => $chat->id,
                    'bid_id' => $chat->bid_id,
                    'messages' => $messages,
                    'current_user_id' => $user->id,
                    'other_user' => [
                        'id' => $user->id === $chat->bid->user_id 
                            ? $chat->bid->bideable->user_id 
                            : $chat->bid->user_id,
                        'name' => $user->id === $chat->bid->user_id 
                            ? ($chat->bid->bideable->user->name ?? 'Usuario')
                            : ($chat->bid->user->name ?? 'Usuario')
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en getNewMessages: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener mensajes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
