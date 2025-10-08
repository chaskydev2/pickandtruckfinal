<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Bid;
use App\Models\OfertaCarga;
use App\Models\OfertaRuta;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB as FacadesDB;
use App\Notifications\BidStatusChanged;
use Carbon\Carbon;

// Alias para DB para asegurar la compatibilidad
if (!class_exists('DB')) {
    class_alias(FacadesDB::class, 'DB');
}

class BidController extends Controller
{
    /**
     * Muestra una lista de las ofertas (bids) enviadas por el usuario.
     */
    public function index()
    {
        $bids = Bid::where('user_id', Auth::id())
            ->with(['bideable' => function($query) {
                // Cargar relaciones específicas según el tipo de oferta
                $query->with('user');
            }])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($bid) {
                $offer = $bid->bideable;
                $response = [
                    'id' => $bid->id,
                    'monto' => $bid->monto,
                    'fecha_hora' => $bid->fecha_hora,
                    'comentario' => $bid->comentario,
                    'estado' => $bid->estado,
                    'created_at' => $bid->created_at,
                    'updated_at' => $bid->updated_at,

                    'bideable_type' => $bid->bideable_type,    // ⬅️ NUEVO
                    'bideable_id'   => $bid->bideable_id,      // ⬅️ NUEVO

                    'tipo' => class_basename($bid->bideable_type) === 'OfertaRuta' ? 'ruta' : 'carga',
                    'estado_oferta' => $offer->estado ?? null,
                    'usuario' => [
                        'id' => $offer->user->id ?? null,
                        'name' => $offer->user->name ?? null,
                        'email' => $offer->user->email ?? null,
                    ]
                ];

                // Agregar campos específicos según el tipo de oferta
                if (class_basename($bid->bideable_type) === 'OfertaRuta') {
                    $response = array_merge($response, [
                        'origen' => $offer->origen ?? null,
                        'destino' => $offer->destino ?? null,
                        'fecha_salida' => $offer->fecha_salida ?? null,
                        'tipo_camion' => $offer->tipo_camion ?? null,
                    ]);
                } else { // OfertaCarga
                    $response = array_merge($response, [
                        'origen' => $offer->origen ?? null,
                        'destino' => $offer->destino ?? null,
                        'fecha_entrega' => $offer->fecha_entrega ?? null,
                        'tipo_carga' => $offer->tipo_carga ?? null,
                        'peso' => $offer->peso ?? null,
                        'volumen' => $offer->volumen ?? null,
                    ]);
                }

                return $response;
            });
            
        return response()->json([
            'success' => true,
            'data' => $bids
        ]);
    }

    /**
     * Muestra una lista de las ofertas recibidas en las publicaciones del usuario.
     */
    public function received()
    {
        // Consulta idéntica a la versión web
        $bids = Bid::whereHasMorph(
            'bideable',
            [OfertaCarga::class, OfertaRuta::class],
            function ($query) {
                $query->where('user_id', Auth::id());
            }
        )
        ->with(['bideable', 'user', 'chat'])  // Incluimos la relación 'chat'
        ->orderBy('created_at', 'desc')
        ->paginate(15);
        
        // Transformar la respuesta
        $transformedBids = $bids->getCollection()->map(function($bid) {
            $bideable = $bid->bideable;
            
            return [
                'id' => $bid->id,
                'monto' => $bid->monto,
                'fecha_hora' => $bid->fecha_hora,
                'comentario' => $bid->comentario,
                'estado' => $bid->estado,
                'created_at' => $bid->created_at,
                'updated_at' => $bid->updated_at,

                'bideable_type' => $bid->bideable_type,
                'bideable_id'   => $bid->bideable_id,

                'tipo' => class_basename($bid->bideable_type) === 'OfertaRuta' ? 'ruta' : 'carga',
                'user' => [
                    'id' => $bid->user->id,
                    'name' => $bid->user->name,
                    'email' => $bid->user->email,
                ],
                'bideable' => [
                    'id' => $bideable->id,
                    'origen' => $bideable->origen ?? null,
                    'destino' => $bideable->destino ?? null,
                    'fecha_salida' => $bideable->fecha_salida ?? $bideable->fecha_inicio ?? null,
                    'fecha_entrega' => $bideable->fecha_entrega ?? null,
                    'tipo_camion' => $bideable->tipo_camion ?? null,
                    'tipo_carga' => $bideable->tipo_carga ?? null,
                    'peso' => $bideable->peso ?? null,
                    'volumen' => $bideable->volumen ?? null,
                    'estado' => $bideable->estado ?? null,
                    'user' => [
                        'id' => $bideable->user->id ?? null,
                        'name' => $bideable->user->name ?? null,
                        'email' => $bideable->user->email ?? null,
                    ]
                ],
                'chat_id' => $bid->chat ? $bid->chat->id : null
            ];
        });
        
        $bids->setCollection($transformedBids);
        
        return response()->json($bids);
    }

    /**
     * Muestra el formulario para crear un bid. (No disponible en API)
     */
    public function create(Request $request)
    {
        return response()->json(['error' => 'No disponible en API.'], 405);
    }

    /**
     * Guarda un nuevo bid en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'bideable_type' => 'required|string|in:App\\Models\\OfertaCarga,App\\Models\\OfertaRuta',
                'bideable_id'   => 'required|numeric',
                'monto'         => 'required|numeric|min:0',
                'fecha_hora'    => 'required|date',
                'comentario'    => 'nullable|string',
            ]);

            $user  = Auth::user();
            $model = $validated['bideable_type'] === 'App\\Models\\OfertaRuta'
                ? OfertaRuta::findOrFail($validated['bideable_id'])
                : OfertaCarga::findOrFail($validated['bideable_id']);

            // 1) BLOQUEO si la publicación ya tiene un ganador (aceptado)
            if ($model->bids()->where('estado', 'aceptado')->exists()) {
                return response()->json(['error' => 'Esta oferta ya ha sido asignada a otro usuario.'], 409);
            }

            // 2) BLOQUEO de "segunda oferta" del MISMO usuario en cualquier estado MENOS rechazado
            $yaOfertoNoRechazado = $model->bids()
                ->where('user_id', $user->id)
                ->where('estado', '!=', 'rechazado')
                ->exists();

            if ($yaOfertoNoRechazado) {
                return response()->json(['error' => 'Ya ofertaste en esta publicación.'], 409);
            }

            // OK: crear en pendiente
            $bid = Bid::create([
                'user_id'       => $user->id,
                'bideable_id'   => $model->id,
                'bideable_type' => get_class($model),
                'monto'         => $validated['monto'],
                'fecha_hora'    => $validated['fecha_hora'],
                'comentario'    => $validated['comentario'] ?? null,
                'estado'        => 'pendiente'
            ]);

            return response()->json(['bid' => $bid], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear bid', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Editar un bid (No disponible en API)
     */
    public function edit(Bid $bid)
    {
        return response()->json(['error' => 'No disponible en API.'], 405);
    }

    /**
     * Actualiza un bid existente.
     */
    public function update(Request $request, Bid $bid)
    {
        if (Auth::id() !== $bid->user_id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'monto'      => 'required|numeric|min:0',
            'fecha_hora' => 'required|date',
            'comentario' => 'nullable|string',
        ]);

        $raw = $request->input('fecha_hora');
        $targetDate = Carbon::parse($raw)->format('Y-m-d');
        $prevTime   = $bid->fecha_hora ? $bid->fecha_hora->format('H:i:s') : '00:00:00';
        $normalized = Carbon::createFromFormat('Y-m-d H:i:s', "{$targetDate} {$prevTime}");

        $bid->update([
            'monto'      => $request->monto,
            'fecha_hora' => $normalized,
            'comentario' => $request->comentario
        ]);
        return response()->json(['message' => 'Puja actualizada exitosamente.', 'bid' => $bid]);
    }

    /**
     * Cambia el estado de un bid. Ahora evita aceptar si ya existe otro aceptado,
     * y si se acepta, replica la lógica de `accept()` (transacción + rechazar otros + chat + notificación).
     */
    public function updateStatus(Request $request, Bid $bid)
    {
        $validated = $request->validate([
            'estado' => 'required|in:aceptado,rechazado',
        ]);

        $bideable = $bid->bideable;

        if (!$bideable || $bideable->user_id != Auth::id()) {
            return response()->json(['error' => 'No tienes permiso para realizar esta acción.'], 403);
        }

        // Si intenta "rechazar" un bid ya aceptado, lo bloqueamos (mismo criterio que reject()).
        if ($validated['estado'] === 'rechazado' && $bid->estado === 'aceptado') {
            return response()->json(['error' => 'No puedes rechazar una oferta ya aceptada'], 400);
        }

        // Si intenta "aceptar", validamos unicidad y replicamos la lógica de accept()
        if ($validated['estado'] === 'aceptado') {
            // ¿Ya hay otro aceptado?
            $yaAceptadoOtro = $bideable->bids()
                ->where('estado', 'aceptado')
                ->where('id', '!=', $bid->id)
                ->exists();

            if ($yaAceptadoOtro) {
                return response()->json(['error' => 'Esta oferta ya tiene un bid aceptado'], 409);
            }

            // Transacción para mantener integridad
            \Illuminate\Support\Facades\DB::beginTransaction();

            try {
                // Aceptar este bid
                $bid->estado = 'aceptado';
                $bid->save();

                // Rechazar los demás
                $bideable->bids()
                    ->where('id', '!=', $bid->id)
                    ->update(['estado' => 'rechazado']);

                // Crear chat si no existe
                $chat = \App\Models\Chat::firstOrCreate(
                    ['bid_id' => $bid->id],
                    [
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );

                // Mensaje de sistema
                \App\Models\Message::create([
                    'chat_id' => $chat->id,
                    'user_id' => Auth::id(),
                    'content' => '¡Oferta aceptada! Ahora pueden coordinar los detalles del trabajo.',
                    'read' => false
                ]);

                // Notificar al ofertante
                $bid->user->notify(new \App\Notifications\BidStatusChanged($bid, 'aceptado'));

                \Illuminate\Support\Facades\DB::commit();

                $bid->refresh()->load(['user', 'bideable']);

                return response()->json([
                    'message' => 'Estado actualizado: aceptado',
                    'bid'     => $bid,
                    'chat'    => $chat
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\DB::rollBack();
                \Log::error('updateStatus(aceptado) error: '.$e->getMessage());
                return response()->json([
                    'error' => 'Ocurrió un error al procesar la solicitud',
                    'message' => $e->getMessage()
                ], 500);
            }
        }

        // Caso "rechazado" (cuando no estaba previamente aceptado)
        try {
            $bid->estado = 'rechazado';
            $bid->save();

            // Notificar al proponente del bid
            $bid->user->notify(new \App\Notifications\BidStatusChanged($bid, 'rechazado'));

            return response()->json([
                'message' => 'Estado actualizado: rechazado',
                'bid'     => $bid
            ]);
        } catch (\Throwable $e) {
            \Log::error('updateStatus(rechazado) error: '.$e->getMessage());
            return response()->json([
                'error' => 'Ocurrió un error al procesar la solicitud',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Acepta un bid.
     */
    public function accept(Bid $bid)
    {
        $oferta = $bid->bideable;
        
        if (Auth::id() !== $oferta->user_id) {
            return response()->json(['error' => 'No tienes permiso para realizar esta acción'], 403);
        }

        if ($oferta->bids->contains('estado', 'aceptado')) {
            return response()->json(['error' => 'Esta oferta ya tiene un bid aceptado'], 409);
        }

        // Iniciar una transacción para asegurar la integridad de los datos
        FacadesDB::beginTransaction();
        
        try {
            // Actualizar el estado del bid a aceptado
            $bid->estado = 'aceptado';
            $bid->save();

            // Rechazar automáticamente las demás ofertas
            $oferta->bids()
                ->where('id', '!=', $bid->id)
                ->update(['estado' => 'rechazado']);

            // Crear un chat entre las partes si no existe
            $chat = Chat::firstOrCreate(
                ['bid_id' => $bid->id],
                [
                    // No incluimos user_a_id ni user_b_id ya que no existen en la tabla
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            // Crear un mensaje de sistema
            Message::create([
                'chat_id' => $chat->id,
                'user_id' => Auth::id(),
                'content' => '¡Oferta aceptada! Ahora pueden coordinar los detalles del trabajo.',
                'read' => false
            ]);

            // Notificar al usuario que hizo la oferta
            $bid->user->notify(new BidStatusChanged($bid, 'aceptado'));

            FacadesDB::commit();

            // Recargar el modelo para asegurar que tenemos los datos más recientes
            $bid->refresh();
            
            // Cargar las relaciones necesarias
            $bid->load(['user', 'bideable']);

            return response()->json([
                'message' => 'Oferta aceptada correctamente', 
                'bid' => $bid,
                'chat' => $chat
            ]);
            
        } catch (\Exception $e) {
            FacadesDB::rollBack();
            \Log::error('Error al aceptar la oferta: ' . $e->getMessage());
            return response()->json([
                'error' => 'Ocurrió un error al procesar la solicitud',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rechaza un bid.
     */
    public function reject(Bid $bid)
    {
        $oferta = $bid->bideable;
        
        if (Auth::id() !== $oferta->user_id) {
            return response()->json(['error' => 'No tienes permiso para realizar esta acción'], 403);
        }

        if ($bid->estado === 'aceptado') {
            return response()->json(['error' => 'No puedes rechazar una oferta ya aceptada'], 400);
        }

        try {
            $bid->estado = 'rechazado';
            $bid->save();

            // Notificar al usuario que hizo la oferta
            $bid->user->notify(new BidStatusChanged($bid, 'rechazado'));

            return response()->json([
                'message' => 'Oferta rechazada correctamente', 
                'bid' => $bid
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al rechazar la oferta: ' . $e->getMessage());
            return response()->json([
                'error' => 'Ocurrió un error al procesar la solicitud',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina un bid (solo el dueño del bid, y no si ya fue aceptado).
     */
    public function destroy(Bid $bid)
    {
        if (Auth::id() !== $bid->user_id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        if ($bid->estado === 'aceptado') {
            return response()->json(['error' => 'No puedes eliminar un bid aceptado'], 400);
        }

        // (Opcional) Si gestionas relaciones/archivos, aquí podrías limpiar asociados
        $bid->delete();

        // 204: No Content (sin body)
        return response()->json(null, 204);
    }
}

