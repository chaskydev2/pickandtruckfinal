<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Events\BidStatusUpdated;
use App\Events\NewChatMessage;
use App\Notifications\GenericNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkProgressController extends Controller
{
    public function show(Bid $bid)
    {
        $bid->load(['user', 'bideable.user', 'chat']);
        
        if (!$bid->bideable) {
            return redirect()->back()->with('error', 'No se encontró la información del trabajo.');
        }

        if (Auth::id() !== $bid->user_id && Auth::id() !== $bid->bideable->user_id) {
            abort(403, 'No tienes permiso para ver este trabajo');
        }

        if (!in_array($bid->estado, ['aceptado', 'terminado', 'pendiente_confirmacion'])) {
            return redirect()->back()->with('error', 'El trabajo no ha sido iniciado o no está disponible.');
        }

        $chat = $bid->chat ?? Chat::create([
            'bid_id' => $bid->id,
            'user_a_id' => $bid->user_id,
            'user_b_id' => $bid->bideable->user_id
        ]);
        
        // Determinar si el usuario actual y el otro usuario han confirmado
        $usuarioActual = Auth::user();
        $usuarioActualConfirmo = $usuarioActual->id === $bid->user_id 
            ? $bid->confirmacion_usuario_a 
            : $bid->confirmacion_usuario_b;
            
        $otroUsuarioConfirmo = $usuarioActual->id === $bid->user_id 
            ? $bid->confirmacion_usuario_b 
            : $bid->confirmacion_usuario_a;

        return view('work-progress.show', compact(
            'bid', 
            'chat',
            'usuarioActualConfirmo',
            'otroUsuarioConfirmo'
        ));
    }

    public function requestCompletion(Request $request, Bid $bid)
    {
        $user = Auth::user();
        $bid->load(['bideable.user']);

        if (!$bid->bideable) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false,'message' => 'No se encontró la información del trabajo.'], 404);
            }
            return redirect()->back()->with('error', 'No se encontró la información del trabajo.');
        }

        if ($user->id !== $bid->user_id && $user->id !== $bid->bideable->user_id) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false,'message' => 'No tienes permiso para realizar esta acción.'], 403);
            }
            return redirect()->back()->with('error', 'No tienes permiso para realizar esta acción.');
        }

        if ($bid->estado === 'terminado') {
            if ($request->wantsJson()) {
                return response()->json(['success' => false,'message' => 'Este trabajo ya ha sido marcado como terminado.'], 400);
            }
            return redirect()->back()->with('error', 'Este trabajo ya ha sido marcado como terminado.');
        }

        if ($bid->estado === 'pendiente_confirmacion') {
            if ($request->wantsJson()) {
                return response()->json(['success' => false,'message' => 'Ya hay una solicitud de finalización pendiente.'], 400);
            }
            return redirect()->back()->with('error', 'Ya hay una solicitud de finalización pendiente.');
        }

        $isUserA = $user->id === $bid->user_id;
        $otherUser = $isUserA ? $bid->bideable->user : $bid->user;

        DB::beginTransaction();

        try {
            $chat = $bid->chat ?? Chat::create([
                'bid_id' => $bid->id,
                'user_a_id' => $bid->user_id,
                'user_b_id' => $bid->bideable->user_id
            ]);

            $bid->estado = 'pendiente_confirmacion';
            $bid->confirmacion_usuario_a = $isUserA;
            $bid->confirmacion_usuario_b = !$isUserA;
            $bid->save();

            $notificationMessage = $isUserA
                ? "Se ha solicitado finalizar el trabajo."
                : "Se ha solicitado finalizar el trabajo.";

            // Emitir evento de actualización de estado
            event(new BidStatusUpdated(
                $bid,
                'pendiente_confirmacion',
                $notificationMessage
            ));

            // Mensaje detallado para el chat
            $messageContent = "{$user->name} ha solicitado finalizar el trabajo. Esperando confirmación.";
            
            // Crear mensaje en el chat
            $message = new Message([
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'content' => $messageContent,
                'is_system' => true,
                'read' => false
            ]);
            $message->save();
            $message->load('user');
            
            // Emitir evento de nuevo mensaje de chat
            event(new NewChatMessage($message));

            // Notificar al otro usuario
            $otherUser->notify(new GenericNotification(
                $messageContent,
                route('work.show', $bid),
                'fas fa-handshake'
            ));

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'bid' => $bid->fresh()
                ]);
            }
            // fallback no-ajax
            return redirect()
                ->route('work.show', $bid)
                ->with('success', 'Solicitud de finalización enviada.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al solicitar finalización: ' . $e->getMessage(), [
                'exception' => $e,
                'bid_id' => $bid->id,
                'user_id' => $user->id
            ]);

            if ($request->wantsJson()) { return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
            } return redirect()->back()->with('error', 'Ocurrió un error al procesar la solicitud.');
        }
    }

    public function confirmCompletion(Request $request, Bid $bid)
    {
        $user = Auth::user();
        $bid->load(['user', 'bideable.user']);

        // Verificar que el bid esté en estado 'pendiente_confirmacion'
        if ($bid->estado !== 'pendiente_confirmacion') {
            if ($request->wantsJson()) {
                return response()->json(['success' => false,'message' => 'No se puede confirmar la finalización de este trabajo en su estado actual.'], 400);
            }
            return redirect()->back()->with('error', 'No se puede confirmar la finalización de este trabajo en su estado actual.');
        }

        $isUserA = $user->id === $bid->user_id;
        $isUserB = $bid->bideable && $user->id === $bid->bideable->user_id;

        if (!$isUserA && !$isUserB) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false,'message' => 'No tienes permiso para realizar esta acción.'], 403);
            }
            return redirect()->back()->with('error', 'No tienes permiso para realizar esta acción.');
        }
        
        DB::beginTransaction();

        try {
            // Actualizar la confirmación del usuario actual
            if ($isUserA) {
                $bid->confirmacion_usuario_a = true;
                $otherUser = $bid->bideable->user;
            } else {
                $bid->confirmacion_usuario_b = true;
                $otherUser = $bid->user;
            }
            
            // Verificar si ambos usuarios han confirmado
            if ($bid->confirmacion_usuario_a && $bid->confirmacion_usuario_b) {
                $bid->estado = 'terminado';
                $messageContent = '¡Ambas partes han confirmado la finalización del trabajo!';
            } else {
                $messageContent = $user->name . ' ha confirmado la finalización del trabajo. Esperando confirmación de la otra parte.';
            }
            
            $bid->save();
            $bid->refresh();
            
            // Crear o obtener el chat
            $chat = $bid->chat ?? Chat::create([
                'bid_id' => $bid->id,
                'user_a_id' => $bid->user_id,
                'user_b_id' => $bid->bideable->user_id
            ]);
            
            // Crear mensaje en el chat
            $message = new Message([
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'content' => $messageContent,
                'is_system' => true,
                'read' => false
            ]);
            $message->save();
            $message->load('user');
            
            // Emitir evento de actualización de estado
            event(new BidStatusUpdated($bid, $bid->estado, $messageContent));
            
            // Emitir evento de nuevo mensaje de chat
            event(new NewChatMessage($message));
            
            // Notificar al otro usuario si es necesario
            if (isset($otherUser)) {
                $otherUser->notify(new GenericNotification(
                    $messageContent,
                    route('work.show', $bid),
                    'fas fa-check-circle'
                ));
            }
            
            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $messageContent,
                    'bid' => $bid->fresh()
                ]);
            }
            return redirect()
                ->route('work.show', $bid)
                ->with('success', $messageContent);
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al confirmar finalización del trabajo: ' . $e->getMessage(), [
                'exception' => $e,
                'bid_id' => $bid->id,
                'user_id' => $user->id
            ]);
            
            if ($request->wantsJson()) { return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar la confirmación: ' . $e->getMessage()
            ], 500);
            }
            return redirect()->back()->with('error', 'Ocurrió un error al procesar la confirmación.');
        }
    }

    public function rejectCompletion(Request $request, Bid $bid)
    {
        $user = Auth::user();
        $bid->load(['bideable.user']);

        // Verificar que el bid esté en estado 'pendiente_confirmacion'
        if ($bid->estado !== 'pendiente_confirmacion') {
            if ($request->wantsJson()) {
                return response()->json(['success' => false,'message' => 'No hay una solicitud de finalización pendiente.'], 400);
            }
            return redirect()->back()->with('error', 'No hay una solicitud de finalización pendiente.');
        }

        $isUserA = $user->id === $bid->user_id;
        $isUserB = $bid->bideable && $user->id === $bid->bideable->user_id;

        if (!$isUserA && !$isUserB) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false,'message' => 'No tienes permiso para realizar esta acción.'], 403);
            }
            return redirect()->back()->with('error', 'No tienes permiso para realizar esta acción.');
        }

        DB::beginTransaction();

        try {
            $otherUser = $isUserA ? $bid->bideable->user : $bid->user;
            
            // Revertir el estado a 'aceptado' y limpiar confirmaciones
            $bid->estado = 'aceptado';
            $bid->confirmacion_usuario_a = false;
            $bid->confirmacion_usuario_b = false;
            $bid->save();
            
            // Crear o obtener el chat
            $chat = $bid->chat ?? Chat::create([
                'bid_id' => $bid->id,
                'user_a_id' => $bid->user_id,
                'user_b_id' => $bid->bideable->user_id
            ]);

            // Mensaje detallado para el chat
            $messageContent = "{$user->name} ha rechazado la finalización del trabajo. El trabajo continúa en progreso.";
            
            // Crear mensaje en el chat
            $message = new Message([
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'content' => $messageContent,
                'is_system' => true,
                'read' => false
            ]);
            $message->save();
            $message->load('user');
            
            // Emitir evento de actualización de estado
            event(new BidStatusUpdated(
                $bid,
                'aceptado',
                $messageContent
            ));
            
            // Emitir evento de nuevo mensaje de chat
            event(new NewChatMessage($message));
            
            // Notificar al otro usuario
            $otherUser->notify(new GenericNotification(
                $messageContent,
                route('work.show', $bid),
                'fas fa-times-circle'
            ));
            
            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Has rechazado la finalización del trabajo.',
                    'bid' => $bid->fresh()
                ]);
            }
            return redirect()
                ->route('work.show', $bid)
                ->with('success', 'Has rechazado la finalización del trabajo.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al rechazar finalización: ' . $e->getMessage(), [
                'exception' => $e,
                'bid_id' => $bid->id,
                'user_id' => $user->id
            ]);

            if ($request->wantsJson()) { return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar el rechazo: ' . $e->getMessage()
            ], 500);
            } return redirect()->back()->with('error', 'Ocurrió un error al procesar el rechazo.');
        }
    }
}
