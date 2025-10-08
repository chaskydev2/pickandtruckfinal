<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Devuelve las notificaciones más recientes y el contador de no leídas.
     */
    public function check(): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['count' => 0, 'notifications' => []]);
        }

        try {
            // ¡Crucial! Recarga el usuario y sus notificaciones desde la BD.
            $user->refresh();

            $unreadCount = $user->unreadNotifications()->count();

            // Lógica inteligente: prioriza notificaciones no leídas.
            $notifications = $user->unreadNotifications()->latest()->take(5)->get();
            if ($notifications->count() < 5) {
                $needed = 5 - $notifications->count();
                $readNotifications = $user->readNotifications()->latest()->take($needed)->get();
                $notifications = $notifications->merge($readNotifications);
            }

            $recentNotifications = $notifications->map(function ($notification) {
                return [
                    'id'      => $notification->id,
                    'data'    => $notification->data,
                    'read_at' => $notification->read_at,
                    'timeAgo' => $notification->created_at->diffForHumans(),
                ];
            });

            return response()->json([
                'count'         => $unreadCount,
                'notifications' => $recentNotifications,
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error en API notifications.check: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudieron cargar las notificaciones'], 500);
        }
    }

    /**
     * Marca una notificación específica como leída.
     */
    public function markAsRead(string $id): JsonResponse
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            return response()->json([
                'success' => true,
                'count' => $user->unreadNotifications()->count()
            ]);
        }

        return response()->json(['error' => 'Notificación no encontrada'], 404);
    }

    /**
     * Marca todas las notificaciones del usuario como leídas.
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();
        $user->unreadNotifications()->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'count' => 0
        ]);
    }
}