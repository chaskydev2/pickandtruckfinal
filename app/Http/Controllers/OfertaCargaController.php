<?php

namespace App\Http\Controllers;

use App\Models\OfertaCarga;
use App\Models\CargoType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Events\NewPublication;

class OfertaCargaController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware(['auth', 'verified'])->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = OfertaCarga::with(['cargoType', 'bids', 'user']);

        // Filtrar por tipo de vista (mis ofertas o todas las demás)
        if ($request->get('view') === 'mine' && $user) {
            $query->where('user_id', $user->id);
        } else {
            // Si hay usuario autenticado, excluye sus propias publicaciones
            if ($user) {
                $query->where('user_id', '!=', $user->id);
            }
            // Mostrar solo publicaciones con fecha_inicio hoy o futura
            $query->whereDate('fecha_inicio', '>=', now()->startOfDay());
        }

        // Filtrar por tipo de carga
        if ($request->filled('tipo_carga')) {
            $query->where('tipo_carga', $request->tipo_carga);
        }

        // Filtrar por origen y destino
        if ($request->filled('origen')) {
            $query->where('origen', 'LIKE', '%' . $request->origen . '%');
        }

        if ($request->filled('destino')) {
            $query->where('destino', 'LIKE', '%' . $request->destino . '%');
        }

        // Ordenar resultados
        switch ($request->get('sort')) {
            case 'price_high':
                $query->orderBy('presupuesto', 'desc');
                break;
            case 'price_low':
                $query->orderBy('presupuesto', 'asc');
                break;
            case 'closest':
                $query->orderBy('fecha_inicio', 'asc');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $ofertas = $query->paginate(10);
        $cargoTypes = CargoType::all();
        $misOfertasCount = $user ? OfertaCarga::where('user_id', $user->id)->count() : 0;

        return view('ofertas_carga.index', compact('ofertas', 'cargoTypes', 'misOfertasCount'));
    }

    public function create()
    {
        $this->authorize('create', OfertaCarga::class);
        $cargoTypes = CargoType::all();
        return view('ofertas_carga.create', compact('cargoTypes'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', OfertaCarga::class);

        $validated = $request->validate([
            'tipo_carga'     => 'required|exists:cargo_types,id',
            'origen'         => 'required|string|max:255',
            'destino'        => 'required|string|max:255',
            'fecha_inicio'   => 'required|date',
            'peso'           => 'required|numeric|min:0',
            'presupuesto'    => 'required|numeric|min:0',
            'descripcion'    => 'nullable|string',
            // nuevos:
            'unidades'       => 'nullable|integer|min:1',
            'es_contenedor'  => 'nullable|boolean',
        ]);

        $data = $validated;
        $data['user_id'] = Auth::id();
        $data['fecha_inicio'] = \Carbon\Carbon::parse($request->fecha_inicio);

        // manejar checkbox nullable: si no viene, queda null
        $data['es_contenedor'] = $request->has('es_contenedor')
            ? (bool)$request->boolean('es_contenedor')
            : null;

        // si no envían unidades, queda null
        $data['unidades'] = $request->filled('unidades')
            ? (int)$request->input('unidades')
            : null;

        $oferta = OfertaCarga::create($data);

        // Emitimos publicación nueva
        event(new NewPublication('carga', [
            'id'         => $oferta->id,
            'titulo'     => $oferta->cargoType?->name ? ('Carga ' . $oferta->cargoType->name) : "Carga #{$oferta->id}",
            'origen'     => $oferta->origen,
            'destino'    => $oferta->destino,
            'monto'      => $oferta->presupuesto,
            'created_at' => $oferta->created_at?->toISOString(),
            'url'        => route('ofertas_carga.show', $oferta),
            'tipo'       => 'carga',
        ]));

        return redirect()->route('ofertas_carga.index')->with([
            'publication_success' => true,
            'publication_url' => route('ofertas_carga.show', $oferta->id)
        ]);
    }

    public function show(OfertaCarga $oferta)
    {
        $oferta->load(['cargoType', 'user']);

        $tieneOfertaBloqueante = $oferta->hasBlockingBid(); // <- reemplaza a “tieneOfertaAceptada”

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

        return view('ofertas_carga.show', [
            'oferta' => $oferta,
            'misBids' => $misBids,
            'tieneOfertaAceptada' => $tieneOfertaBloqueante, // si tu blade usa este nombre, pásalo así
        ]);
    }

    public function edit(OfertaCarga $oferta)
    {
        $this->authorize('update', $oferta);
        $cargoTypes = CargoType::all();
        return view('ofertas_carga.edit', compact('oferta','cargoTypes'));
    }

    public function update(Request $request, OfertaCarga $oferta)
    {
        $this->authorize('update', $oferta);

        $validated = $request->validate([
            'tipo_carga'    => 'required|exists:cargo_types,id',
            'origen'        => 'required|string|max:255',
            'destino'       => 'required|string|max:255',
            'fecha_inicio'  => 'required|date',
            'peso'          => 'required|numeric|min:0',
            'presupuesto'   => 'required|numeric|min:0',
            'descripcion'   => 'nullable|string',
            'unidades'      => 'nullable|integer|min:1',
            'es_contenedor' => 'nullable|boolean',
        ]);

        $data = $validated;
        $newFecha = \Carbon\Carbon::parse($request->fecha_inicio);

        if ($newFecha->toDateTimeString() !== $oferta->fecha_inicio->toDateTimeString()) {
            $data['expiry_notification_sent_at']  = null;
            $data['expired_notification_sent_at'] = null;
        }

        $data['fecha_inicio'] = $newFecha;
        $data['es_contenedor'] = $request->has('es_contenedor') ? (bool)$request->boolean('es_contenedor') : null;
        $data['unidades']      = $request->filled('unidades') ? (int)$request->input('unidades') : null;

        $oferta->update($data);

        return redirect()->route('ofertas_carga.index')->with('success', 'Oferta de carga actualizada exitosamente.');
    }

    public function destroy(OfertaCarga $oferta)
    {
        $this->authorize('delete', $oferta); // <- bloqueará si isLocked()
        $oferta->delete();
        return redirect()->route('ofertas_carga.index')->with('success', 'Oferta de carga eliminada exitosamente.');
    }

}
