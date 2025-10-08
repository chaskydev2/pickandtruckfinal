<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bid;
use App\Events\RealtimeEvent;

class RealtimeController extends Controller
{
    /**
     * Suscribirse a actualizaciones de un trabajo específico
     */
    public function subscribeToWorkUpdates(Request $request, $bidId)
    {
        $bid = Bid::findOrFail($bidId);
        
        // Verificar que el usuario tiene permiso para ver este trabajo
        if (Auth::id() !== $bid->user_id && Auth::id() !== $bid->bideable->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado para ver este trabajo.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'status' => $bid->estado,
            'confirmacion_usuario_a' => $bid->confirmacion_usuario_a,
            'confirmacion_usuario_b' => $bid->confirmacion_usuario_b,
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    /**
     * Obtener el estado actual de un trabajo
     */
    public function getWorkStatus($bidId)
    {
        $bid = Bid::findOrFail($bidId);
        
        return response()->json([
            'success' => true,
            'status' => $bid->estado,
            'confirmacion_usuario_a' => $bid->confirmacion_usuario_a,
            'confirmacion_usuario_b' => $bid->confirmacion_usuario_b,
            'last_updated' => $bid->updated_at->toDateTimeString()
        ]);
    }
}
