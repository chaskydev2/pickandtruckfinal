<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use Illuminate\Support\Facades\DB;
use App\Models\OfertaCarga;
use App\Models\OfertaRuta;
use App\Models\Chat;
use App\Models\Message;
use App\Notifications\BidReceived;
use App\Notifications\BidStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BidController extends Controller
{
    /**
     * Muestra una lista de las ofertas (bids) enviadas por el usuario.
     */
    public function index()
    {
        $misBids = Bid::where('user_id', Auth::id())
            ->with(['bideable', 'bideable.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('bids.index', compact('misBids'));
    }

    /**
     * Muestra una lista de las ofertas recibidas en las publicaciones del usuario.
     */
    public function received()
    {
        $bidsRecibidos = Bid::whereHasMorph(
                'bideable',
                [OfertaCarga::class, OfertaRuta::class],
                function ($query) {
                    $query->where('user_id', Auth::id());
                }
            )
            ->with(['bideable', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('bids.received', compact('bidsRecibidos'));
    }

    /**
     * Elimina una oferta (bid) específica.
     */
    public function destroy(Bid $bid)
    {
        $this->authorize('delete', $bid); // <- usa BidPolicy (bloquea si isLocked)

        // (opcional: si quieres mantener el mensaje específico cuando esté bloqueado)
        if ($bid->isLocked()) {
            return back()->with('error', 'No se puede eliminar una oferta en estado aceptado/pendiente_confirmacion/terminado.');
        }

        $bid->delete();
        return back()->with('success', 'La oferta ha sido eliminada correctamente.');
    }

    /**
     * Muestra el formulario para crear un bid.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $type = $request->type;
        $id   = $request->id;

        if (!in_array($type, ['ruta', 'carga'])) {
            return back()->with('error', 'Tipo inválido');
        }

        $oferta = $type === 'ruta'
            ? OfertaRuta::findOrFail($id)
            : OfertaCarga::findOrFail($id);

        // ⬇️ esto aplica: no dejar que el dueño se oferte a sí mismo y no permitir cuando está “locked”
        $this->authorize('createBid', $oferta);

        // (opcional) Mensaje amigable cuando está lockeada
        if ($oferta->isLocked()) {
            return back()->with('error', 'Esta publicación ya no acepta nuevas ofertas.');
        }

        // ⛔️ No permitir crear si ya tengo una oferta activa (pendiente/aceptado/pendiente_confirmacion/terminado) sobre esta publicación
        $yaTengoActiva = $oferta->bids()
            ->where('user_id', Auth::id())
            ->whereIn('estado', ['pendiente','aceptado','pendiente_confirmacion','terminado'])
            ->exists();

        if ($yaTengoActiva) {
            return back()->with('error', 'Ya tienes una oferta activa para esta publicación.');
        }

        return view('bids.create', compact('oferta', 'type'));
    }

    /**
     * Guarda un nuevo bid en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info("Recibida solicitud de creación de bid", $request->only('type','id','monto','fecha_servicio'));

        try {
            $validated = $request->validate([
                'type'            => 'required|in:carga,ruta',
                'id'              => 'required|numeric',
                'monto'           => 'required|numeric|min:1',
                'fecha_servicio'  => 'required|date',
                'mensaje'         => 'nullable|string|max:1000',
            ]);

            $model = $validated['type'] === 'carga'
                ? OfertaCarga::findOrFail($validated['id'])
                : OfertaRuta::findOrFail($validated['id']);

            // Gate/Policy: dueños no ofertan y bloquea si hay bid aceptado/pend_conf/terminado
            $this->authorize('createBid', $model);

            // No permitir múltiples ofertas activas del mismo usuario sobre la misma publicación.
            // Sólo permitir si todas las previas están RECHAZADAS.
            $tieneActiva = $model->bids()
                ->where('user_id', auth()->id())
                ->whereIn('estado', ['pendiente','aceptado','pendiente_confirmacion','terminado'])
                ->exists();

            if ($tieneActiva) {
                return back()->with('error', 'Ya tienes una oferta activa para esta publicación.');
            }

            $bid = Bid::create([
                'user_id'       => auth()->id(),
                'bideable_id'   => $model->id,
                'bideable_type' => get_class($model),
                'monto'         => $validated['monto'],
                'fecha_hora'    => $validated['fecha_servicio'],
                'comentario'    => $validated['mensaje'] ?? null,
                'estado'        => 'pendiente',
            ]);

            // Notificaciones (tu bloque tal cual; lo dejo igual)
            try {
                $bid->refresh()->load(['bideable','user']);
                $model->user->notify(new BidReceived($bid));
            } catch (\Exception $e) {
                Log::error("Error al enviar notificación BidReceived: ".$e->getMessage());
                try {
                    $model->user->notify(new \App\Notifications\GenericNotification(
                        "Has recibido una nueva oferta de $".number_format($validated['monto'],2),
                        route('bids.received'),
                        'fas fa-hand-holding-usd'
                    ));
                } catch (\Exception $e2) {
                    Log::error("Error al enviar notificación alternativa: ".$e2->getMessage());
                }
            }

            return redirect()->route('bids.index')->with([
                'bid_success'    => true,
                'recipient_name' => $model->user->name ?? 'el publicante'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Error de validación al crear oferta: ".json_encode($e->errors()));
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error("Error al crear oferta: ".$e->getMessage());
            return back()->with('error','Ocurrió un error al procesar tu oferta. Intenta de nuevo.')->withInput();
        }
    }

    public function edit(Bid $bid)
    {
        $this->authorize('update', $bid); // <- bloquea si isLocked

        $model = $bid->bideable;
        $type  = $model instanceof OfertaCarga ? 'carga' : 'ruta';
        return view('bids.edit', compact('bid','model','type'));
    }

    public function update(Request $request, Bid $bid)
    {
        $this->authorize('update', $bid); // <- bloquea si isLocked

        $request->validate([
            'monto'      => 'required|numeric|min:0',
            'fecha_hora' => 'required|date',
            'comentario' => 'nullable|string',
        ]);

        $raw       = $request->input('fecha_hora');
        $target    = \Carbon\Carbon::parse($raw)->format('Y-m-d');
        $prevTime  = $bid->fecha_hora ? $bid->fecha_hora->format('H:i:s') : '00:00:00';
        $normalized= \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', "{$target} {$prevTime}");

        $bid->update([
            'monto'      => $request->monto,
            'fecha_hora' => $normalized,
            'comentario' => $request->comentario
        ]);

        $redirectRoute = $bid->bideable instanceof \App\Models\OfertaCarga ? 'ofertas_carga.show' : 'ofertas.show';
        return redirect()->route($redirectRoute, $bid->bideable)->with('success', 'Puja actualizada exitosamente.');
    }

    public function updateStatus(Request $request, Bid $bid)
    {
        $validated = $request->validate([
            'estado' => 'required|in:aceptado,rechazado',
        ]);

        // Verificar que el usuario sea el dueño de la publicación original
        $bideable = $bid->bideable;
        if (!$bideable || $bideable->user_id != Auth::id()) {
            return back()->with('error', 'No tienes permiso para realizar esta acción.');
        }

        $bid->estado = $validated['estado'];
        $bid->save();

        return back()->with('success', 'Estado de la oferta actualizado correctamente.');
    }

    public function accept(Bid $bid)
    {
        $oferta = $bid->bideable;
        
        if (Auth::id() !== $oferta->user_id) {
            return redirect()->back()->with('error', 'No tienes permiso para realizar esta acción');
        }

        if ($oferta->bids->contains('estado', 'aceptado')) {
            return redirect()->back()->with('error', 'Esta oferta ya tiene un bid aceptado');
        }

        // Iniciar una transacción para asegurar la integridad de los datos
        DB::beginTransaction();
        
        try {
            // Aceptar la oferta seleccionada
            $bid->estado = 'aceptado';
            $bid->save();
            
            // Crear el chat para esta oferta aceptada
            $chat = Chat::firstOrCreate(
                ['bid_id' => $bid->id],
                [
                    'user_a_id' => $bid->bideable->user_id, // Dueño de la publicación
                    'user_b_id' => $bid->user_id,           // Usuario que hizo la oferta
                ]
            );
            
            // Crear un mensaje de sistema en el chat
            if ($chat->wasRecentlyCreated) {
                Message::create([
                    'chat_id' => $chat->id,
                    'user_id' => Auth::id(),
                    'content' => '¡Oferta aceptada! Ahora pueden coordinar los detalles del trabajo.',
                    'is_system' => true
                ]);
            }
            
            // Rechazar automáticamente todas las demás ofertas pendientes de esta publicación
            $oferta->bids()
                ->where('id', '!=', $bid->id)
                ->whereIn('estado', ['pendiente'])
                ->update(['estado' => 'rechazado']);
            
            // Notificar al usuario cuya oferta fue aceptada
            $bid->user->notify(new BidStatusChanged($bid, 'aceptado'));
            
            // Notificar a los usuarios cuyas ofertas fueron rechazadas
            $rejectedBids = $oferta->bids()->where('estado', 'rechazado')
                ->where('id', '!=', $bid->id)
                ->with('user')
                ->get();
                
            foreach ($rejectedBids as $rejectedBid) {
                $rejectedBid->user->notify(new BidStatusChanged($rejectedBid, 'rechazado'));
            }
            
            DB::commit();
            
            $redirectRoute = $oferta instanceof OfertaCarga ? 'ofertas_carga.show' : 'ofertas.show';
            
            return redirect()->route($redirectRoute, $oferta)
                ->with('success', 'Oferta aceptada correctamente. Las demás ofertas han sido rechazadas.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al aceptar la oferta: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Ocurrió un error al procesar la solicitud. Por favor, inténtalo de nuevo.');
        }
    }

    public function reject(Bid $bid)
    {
        $oferta = $bid->bideable;
        
        if (Auth::id() !== $oferta->user_id) {
            return redirect()->back()->with('error', 'No tienes permiso para realizar esta acción');
        }

        $bid->update(['estado' => 'rechazado']);

        $bid->user->notify(new BidStatusChanged($bid, 'rechazado'));

        $redirectRoute = $oferta instanceof OfertaCarga ? 'ofertas_carga.show' : 'ofertas.show';

        return redirect()->route($redirectRoute, $oferta)
            ->with('success', 'Oferta rechazada exitosamente.');
    }
}
