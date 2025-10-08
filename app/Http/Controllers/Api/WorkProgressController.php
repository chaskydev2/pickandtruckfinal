<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bid;
use App\Models\Chat;
use App\Models\Message;
use App\Notifications\GenericNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Importa los modelos de oferta y los modelos de tipo de carga/camión
use App\Models\OfertaCarga; 
use App\Models\OfertaRuta; 

class WorkProgressController extends Controller
{
    /**
     * Muestra los detalles de un trabajo
     */
    public function show(Bid $bid)
    {
        if (Auth::id() !== $bid->user_id && Auth::id() !== $bid->bideable->user_id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Carga las relaciones necesarias, incluyendo las polimórficas anidadas
        $bid->load([
            'user', 
            'bideable', 
            'bideable.user',
            'bideable' => function ($morphTo) {
                $morphTo->morphWith([
                    OfertaCarga::class => ['cargoType'], 
                    OfertaRuta::class => ['truckType'],
                ]);
            }
        ]);
        
        $chat = Chat::firstOrCreate(['bid_id' => $bid->id]);

        return response()->json(compact('bid', 'chat'));
    }

    /**
     * Solicita la finalización de un trabajo
     */
    public function requestCompletion(Bid $bid)
    {
        $user = Auth::user();
        
        if ($user->id !== $bid->user_id && $user->id !== $bid->bideable->user_id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        if ($bid->estado === 'terminado') {
            return response()->json(['error' => 'El trabajo ya está terminado'], 400);
        }

        if ($bid->estado === 'pendiente_confirmacion') {
            return response()->json(['error' => 'Ya hay una solicitud pendiente'], 400);
        }

        DB::beginTransaction();
        
        try {
            $isUserA = $user->id === $bid->user_id;
            $otherUser = $isUserA ? $bid->bideable->user : $bid->user;
            
            $chat = Chat::firstOrCreate(['bid_id' => $bid->id]);
            
            $bid->estado = 'pendiente_confirmacion';
            $bid->confirmacion_usuario_a = $isUserA;
            $bid->confirmacion_usuario_b = !$isUserA;
            $bid->save();

            $message = new Message([
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'content' => "{$user->name} ha solicitado finalizar el trabajo.",
                'is_system' => true
            ]);
            $message->save();

            $otherUser->notify(new GenericNotification(
                'Solicitud de finalización',
                route('api.work.show', $bid),
                'fas fa-handshake',
                'info',
                "El usuario {$user->name} ha solicitado finalizar el trabajo."
            ));

            DB::commit();
            return response()->json(['message' => 'Solicitud enviada']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en requestCompletion: ' . $e->getMessage());
            return response()->json(['error' => 'Error al procesar la solicitud'], 500);
        }
    }

    /**
     * Confirma la finalización de un trabajo
     */
    public function confirmCompletion(Bid $bid)
    {
        return $this->updateCompletionStatus($bid, true);
    }

    /**
     * Rechaza la finalización de un trabajo
     */
    public function rejectCompletion(Bid $bid)
    {
        return $this->updateCompletionStatus($bid, false);
    }

    /**
     * Actualiza el estado de finalización de un trabajo
     */
    protected function updateCompletionStatus(Bid $bid, $confirm)
    {
        $user = Auth::user();
        
        if ($user->id !== $bid->user_id && $user->id !== $bid->bideable->user_id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        if ($bid->estado !== 'pendiente_confirmacion') {
            return response()->json(['error' => 'No hay solicitud pendiente'], 400);
        }

        DB::beginTransaction();
        
        try {
            $otherUser = $user->id === $bid->user_id ? $bid->bideable->user : $bid->user;
            $chat = Chat::firstOrCreate(['bid_id' => $bid->id]);

            if ($confirm) {
                $bid->estado = 'terminado';
                $bid->fecha_finalizacion = now();
                $message = "{$user->name} ha confirmado la finalización del trabajo.";
                $title = 'Trabajo finalizado';
                $type = 'success';
            } else {
                $bid->estado = 'aceptado';
                $bid->confirmacion_usuario_a = false;
                $bid->confirmacion_usuario_b = false;
                $message = "{$user->name} ha rechazado la finalización del trabajo.";
                $title = 'Finalización rechazada';
                $type = 'warning';
            }
            
            $bid->save();

            $msg = new Message([
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'content' => $message,
                'is_system' => true
            ]);
            $msg->save();

            $otherUser->notify(new GenericNotification(
                $title,
                route('api.work.show', $bid),
                $confirm ? 'fas fa-check-circle' : 'fas fa-times-circle',
                $type,
                $message
            ));

            DB::commit();
            return response()->json(['message' => 'Estado actualizado']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en updateCompletionStatus: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar el estado'], 500);
        }
    }

    /**
     * Verifica el estado de un trabajo
     */
    public function checkStatus(Bid $bid, Request $request)
    {
        if (Auth::id() !== $bid->user_id && Auth::id() !== $bid->bideable->user_id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return response()->json([
            'status' => $bid->estado,
            'updated_at' => $bid->updated_at->toIso8601String(),
            'confirmacion_usuario_a' => $bid->confirmacion_usuario_a,
            'confirmacion_usuario_b' => $bid->confirmacion_usuario_b
        ]);
    }
}