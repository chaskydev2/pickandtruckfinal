<?php

namespace App\Http\Controllers;

use App\Models\OfertaRuta;
use App\Models\TruckType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Validation\Rule; // <-- NUEVO: Importar para la validación
use App\Events\NewPublication;

class OfertaRutaController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware(['auth', 'verified'])->except(['index', 'show']);
    }

    // ... (index y create no necesitan cambios)
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = OfertaRuta::with(['truckType', 'bids', 'user']);

        if ($request->get('view') === 'mine' && $user) {
            $query->where('user_id', $user->id);
        } else {
            if ($user) {
                $query->where('user_id', '!=', $user->id);
            }
            $query->whereDate('fecha_inicio', '>=', now()->startOfDay());
        }

        if ($request->filled('truck_type')) {
            $query->where('tipo_camion', $request->truck_type);
        }
        if ($request->filled('origen')) {
            $query->where('origen', 'LIKE', '%' . $request->origen . '%');
        }
        if ($request->filled('destino')) {
            $query->where('destino', 'LIKE', '%' . $request->destino . '%');
        }

        $ofertas = $query->latest()->paginate(12);
        $truckTypes = TruckType::all();
        $misOfertasCount = $user ? OfertaRuta::where('user_id', $user->id)->count() : 0; // <-- null-safe

        return view('ofertas.index', compact('ofertas', 'truckTypes', 'misOfertasCount'));
    }

    public function create()
    {
        $truckTypes = TruckType::all();
        return view('ofertas.create', compact('truckTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_camion'        => 'required|exists:truck_types,id',
            'origen'             => 'required|string|max:255',
            'destino'            => 'required|string|max:255',
            'fecha_inicio'       => 'required|date',
            'capacidad'          => 'required|integer|min:1',
            'precio_referencial' => 'required|numeric|min:0',
            'descripcion'        => 'nullable|string',
            'unidades'           => 'nullable|integer|min:1',
            // NUEVO: Regla de validación para el campo enum
            'tipo_despacho' => ['nullable', Rule::in(['despacho_anticipado', 'despacho_general', 'no_sabe_no_responde'])],
        ]);

        $data = $validated;
        $data['user_id'] = Auth::id();
        $data['fecha_inicio'] = \Carbon\Carbon::parse($request->fecha_inicio);
        $data['unidades'] = $request->filled('unidades') ? (int)$request->input('unidades') : null;

        $oferta = OfertaRuta::create($data);

        event(new NewPublication('ruta', [
            'id'         => $oferta->id,
            'titulo'     => $oferta->truckType?->name ? ('Ruta ' . $oferta->truckType->name) : "Ruta #{$oferta->id}",
            'origen'     => $oferta->origen,
            'destino'    => $oferta->destino,
            'monto'      => $oferta->precio_referencial,
            'created_at' => $oferta->created_at?->toISOString(),
            'url'        => route('ofertas.show', $oferta),
            'tipo'       => 'ruta',
        ]));

        return redirect()->route('ofertas.index')->with([
            'publication_success' => true,
            'publication_url' => route('ofertas.show', $oferta->id)
        ]);
    }

    public function show(OfertaRuta $oferta)
    {
        $oferta->load(['truckType', 'user']);

        $misBids = $oferta->bids()
            ->where('user_id', auth()->id())
            ->with('user')
            ->orderBy('created_at','desc')
            ->get();

        if (auth()->check()) {
            $oferta->load(['bids' => function ($q) { $q->with('user')->orderBy('created_at','desc'); }]);
        } else {
            $oferta->load(['bids' => function ($q) {
                $q->with('user')->where('estado', 'aceptado')->orderBy('created_at','desc');
            }]);
        }

        return view('ofertas.show', [
            'oferta'  => $oferta,
            'misBids' => $misBids,
            'tieneOfertaAceptada' => $oferta->hasBlockingBid(),
        ]);
    }

    public function edit(OfertaRuta $oferta)
    {
        $this->authorize('update', $oferta);
        $truckTypes = TruckType::all();
        return view('ofertas.edit', compact('oferta', 'truckTypes'));
    }

    public function update(Request $request, OfertaRuta $oferta)
    {
        $this->authorize('update', $oferta);

        $validated = $request->validate([
            'tipo_camion'       => 'required|exists:truck_types,id',
            'origen'            => 'required|string|max:255',
            'destino'           => 'required|string|max:255',
            'fecha_inicio'      => 'required|date',
            'capacidad'         => 'required|integer|min:1',
            'precio_referencial'=> 'required|numeric|min:0',
            'descripcion'       => 'nullable|string',
            'unidades'          => 'nullable|integer|min:1',
            'tipo_despacho'     => ['nullable', Rule::in(['despacho_anticipado', 'despacho_general', 'no_sabe_no_responde'])],
        ]);

        $data = $validated;
        $newFechaInicio = \Carbon\Carbon::parse($request->fecha_inicio);

        // <-- INICIO DE LA CORRECCIÓN
        // Comparamos si la nueva fecha es diferente a la que ya está guardada.
        if ($newFechaInicio->toDateTimeString() !== $oferta->fecha_inicio->toDateTimeString()) {
            $data['expiry_notification_sent_at'] = null;
            $data['expired_notification_sent_at'] = null;
        }
        // FIN DE LA CORRECCIÓN -->
        
        $data['fecha_inicio'] = $newFechaInicio;
        $data['unidades'] = $request->filled('unidades') ? (int)$request->input('unidades') : null;

        $oferta->update($data);

        return redirect()->route('ofertas.index')->with('success', 'Oferta actualizada exitosamente.');
    }

    // ... (destroy no necesita cambios)
    public function destroy(OfertaRuta $oferta)
    {
        $this->authorize('delete', $oferta);
        $oferta->delete();
        return redirect()->route('ofertas.index')->with('success', 'Oferta eliminada exitosamente.');
    }
}
