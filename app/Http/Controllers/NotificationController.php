<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Muestra todas las notificaciones del usuario
     */
    public function index()
    {
        $user = Auth::user();
        
        // Obtener todas las notificaciones
        $notifications = $user->notifications()->paginate(15);
        
        return view('notifications.index', compact('notifications'));
    }
    
    /**
     * Verifica si hay notificaciones nuevas (para AJAX)
     */

    public function check(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['count' => 0, 'notifications' => []]);
        }

        try {
            // Forzamos a Laravel a recargar el usuario y sus notificaciones desde la BD.
            $user->refresh();
            
            // Ahora sí, estas consultas traerán los datos más recientes.
            $unreadCount = $user->unreadNotifications()->count();
            
            $recentNotifications = $user->notifications()
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($notification) {
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
            \Log::error('Error en notifications.check: ' . $e->getMessage());
            return response()->json(['count' => 0, 'notifications' => []], 500);
        }
    }
    
    /**
     * Marca una notificación como leída y redirige al URL correspondiente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead(Request $request, $id): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => false, 'message' => 'Usuario no autenticado'], 401);
            }
            return redirect()->route('login');
        }
        
        $notification = $user->notifications()->find($id);
        
        if ($notification) {
            // Marcar esta notificación como leída
            $notification->markAsRead();
            
            // Si es una notificación de chat, marcar como leídas todas las del mismo chat
            if (isset($notification->data['type']) && $notification->data['type'] === 'chat_message') {
                $chatId = $notification->data['chat_id'] ?? null;
                if ($chatId) {
                    $user->unreadNotifications()
                        ->where('type', 'App\\Notifications\\NewChatMessage')
                        ->where('data->chat_id', $chatId)
                        ->update(['read_at' => now()]);
                }
            }
            
            if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true, 
                    'count' => $user->unreadNotifications()->count()
                ]);
            }
            
            // Redirigir al URL almacenado en la notificación
            if (isset($notification->data['url'])) {
                return redirect($notification->data['url']);
            }
        }
        
        if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => false,
                'message' => 'Notificación no encontrada'
            ], 404);
        }
        
        return redirect()->back()->with('error', 'Notificación no encontrada');
    }
    
    /**
     * Marca todas las notificaciones como leídas
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        // Marcar todas las notificaciones no leídas como leídas
        $user->unreadNotifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
            
        // Limpiar la caché de notificaciones
        $user->refresh();
        
        // Si es petición AJAX, devolver JSON
        if (request()->wantsJson() || request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true, 
                'count' => 0,
                'message' => 'Todas las notificaciones han sido marcadas como leídas'
            ]);
        }
        
        return redirect()->back()->with('success', 'Todas las notificaciones han sido marcadas como leídas');
    }

    /**
     * Método para realizar pruebas de notificaciones.
     */
    public function testSend(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Usuario no autenticado'], 401);
        }
        
        try {
            // Crear una notificación de prueba
            $success = $user->testNotification();
            
            if ($success) {
                return response()->json(['success' => true, 'message' => 'Notificación de prueba enviada']);
            } else {
                return response()->json(['success' => false, 'message' => 'Error al enviar notificación de prueba']);
            }
        } catch (\Exception $e) {
            Log::error("Error en testSend: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
