<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewChatMessage;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $chats = Chat::whereHas('bid', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhereHas('bideable', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
        })->with(['bid.bideable', 'bid.user', 'messages'])->get();

        $chats = $chats->filter(function ($chat) {
            return $chat->bid && $chat->bid->bideable;
        });

        return view('chats.index', compact('chats'));
    }

    public function show(Chat $chat)
    {
        $user = Auth::user();
        
        if (!$chat->bid || !$chat->bid->bideable) {
            return redirect()->route('chats.index')
                ->with('error', 'El chat no está disponible porque la oferta relacionada ya no existe.');
        }

        if (!in_array($user->id, [$chat->bid->user_id, $chat->bid->bideable->user_id])) {
            abort(403, 'No autorizado para acceder a este chat.');
        }

        $chat->load(['bid.bideable', 'bid.user', 'messages.user']);
        
        $chat->messages()
            ->where('user_id', '!=', $user->id)
            ->where('read', false)
            ->update(['read' => true]);

        return view('chats.show', compact('chat'));
    }

    public function message(Request $request, Chat $chat)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        try {
            if (!$chat->bid) {
                throw new \Exception('El chat no está asociado a una oferta');
            }

            $chat->load(['bid.user', 'bid.bideable']);
            $bid = $chat->bid;

            $currentUserId = Auth::id();
            $isAuthorized = $currentUserId === $bid->user_id || 
                            ($bid->bideable && $currentUserId === $bid->bideable->user_id);

            if (!$isAuthorized) {
                if ($request->ajax() || $request->wantsJson() || $request->has('_ajax')) {
                    return response()->json(['error' => 'No autorizado para enviar mensajes en este chat'], 403);
                }
                return back()->with('error', 'No autorizado para enviar mensajes en este chat');
            }

            $message = new Message([
                'chat_id' => $chat->id,
                'user_id' => $currentUserId,
                'content' => $request->message,
                'read' => false
            ]);
            $message->save();
            
            $message->load('user');

            broadcast(new \App\Events\NewChatMessage($message))->toOthers();
            
            $otherUserId = $currentUserId === $bid->user_id 
                ? $bid->bideable->user_id 
                : $bid->user_id;

            $otherUser = \App\Models\User::find($otherUserId);
            if ($otherUser) {
                try {
                    $otherUser->notify(new \App\Notifications\NewChatMessage($message));
                } catch (\Exception $e) {
                    \Log::error('Error al enviar notificación de chat', [
                        'error' => $e->getMessage(),
                        'user_id' => $otherUserId,
                        'message_id' => $message->id
                    ]);
                }
            }

            Log::info('Mensaje enviado correctamente', [
                'message_id' => $message->id,
                'chat_id' => $chat->id
            ]);
            
            $messageData = [
                'id' => $message->id,
                'content' => $message->content,
                'user_id' => $message->user_id,
                'chat_id' => $message->chat_id,
                'created_at' => $message->created_at,
                'user' => [
                    'id' => $message->user->id,
                    'name' => $message->user->name,
                    'email' => $message->user->email
                ]
            ];
            
            // Manejar respuesta para peticiones AJAX o normales
            if ($request->ajax() || $request->wantsJson() || $request->has('_ajax')) {
                return response()->json([
                    'success' => true,
                    'message' => $messageData
                ]);
            }
            
            // Para peticiones normales, redirigir de vuelta
            return back()->with('success', 'Mensaje enviado correctamente');

        } catch (\Exception $e) {
            Log::error('Error al enviar mensaje en ChatController::message', [
                'error' => $e->getMessage(),
                'chat_id' => $chat->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Manejar errores para peticiones AJAX o normales
            if ($request->ajax() || $request->wantsJson() || $request->has('_ajax')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al enviar el mensaje: ' . $e->getMessage(),
                    'message' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error al enviar el mensaje');
        }
    }

    public function getNewMessages(Request $request, Chat $chat)
    {
        try {
            $lastId = $request->input('last_id', 0);
            
            $user = Auth::user();
            if (!$chat->bid || 
                ($chat->bid->user_id !== $user->id && $chat->bid->bideable->user_id !== $user->id)) {
                return response()->json(['error' => 'No autorizado para acceder a estos mensajes'], 403);
            }
            
            $messages = $chat->messages()
                ->where('id', '>', $lastId)
                ->with('user')
                ->orderBy('created_at', 'asc')
                ->get();
                
            return response()->json([
                'success' => true,
                'messages' => $messages,
                'userId' => $user->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en ChatController::getNewMessages: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener mensajes: ' . $e->getMessage(),
                'message' => $e->getMessage()
            ], 500);
        }
    }
}