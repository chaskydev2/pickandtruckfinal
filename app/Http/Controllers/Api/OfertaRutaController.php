<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OfertaRuta;
use App\Models\TruckType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OfertaRutaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = OfertaRuta::with(['truckType', 'user' => fn($q) => $q->select('id', 'name')]);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        } elseif ($request->filled('exclude_user_id')) {
            $query->where('user_id', '!=', $request->exclude_user_id);
        } elseif ($request->get('view') === 'mine') {
            $query->where('user_id', Auth::id());
        } elseif (Auth::check()) {
            $query->where('user_id', '!=', Auth::id());
        }

        // ---- Filtro de fecha para NO-mis publicaciones (solo hoy o futuro) ----
        $viendoLasMias = false;

        // Caso 1: view=mine (con usuario autenticado)
        if ($request->get('view') === 'mine' && Auth::check()) {
            $viendoLasMias = true;
        }

        // Caso 2: user_id explícito igual al usuario autenticado
        if ($request->filled('user_id') && Auth::check() && (int)$request->user_id === Auth::id()) {
            $viendoLasMias = true;
        }

        // Si NO estoy viendo mis publicaciones, ocultar pasadas
        if (!$viendoLasMias) {
            $query->whereDate('fecha_inicio', '>=', now()->startOfDay());
        }

        if ($request->filled('truck_type')) { $query->where('tipo_camion', $request->truck_type); }
        if ($request->filled('origen')) { $query->where('origen', 'LIKE', '%' . $request->origen . '%'); }
        if ($request->filled('destino')) { $query->where('destino', 'LIKE', '%' . $request->destino . '%'); }

        switch ($request->get('sort')) {
            case 'price_high': $query->orderBy('precio_referencial', 'desc'); break;
            case 'price_low': $query->orderBy('precio_referencial', 'asc'); break;
            case 'closest': $query->orderBy('fecha_inicio', 'asc'); break;
            default: $query->latest();
        }

        $query->withCount([
            'bids as bids_aceptadas_count' => function ($q) {
                $q->where('estado', 'aceptado'); // ← SOLO aceptado
            }
        ]);

        $ofertas = $query->paginate(10);
        $ofertas->getCollection()->transform(function ($oferta) {
            $oferta->append('tipo_despacho_texto');
            $oferta->setAttribute('asignada', ($oferta->bids_aceptadas_count ?? 0) > 0);
            return $oferta;
        });

        return response()->json($ofertas);

    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tipo_camion'       => 'required|exists:truck_types,id',
            'origen'            => 'required|string|max:255',
            'destino'           => 'required|string|max:255',
            'fecha_inicio'      => 'required|date_format:Y-m-d H:i:s',
            'capacidad'         => 'required|integer|min:1',
            'precio_referencial'=> 'required|numeric|min:0',
            'descripcion'       => 'nullable|string',
            'unidades'          => 'nullable|integer|min:1',
            'tipo_despacho'     => ['nullable', Rule::in(['despacho_anticipado', 'despacho_general', 'no_sabe_no_responde'])],
        ]);
        
        $payload = $validated;
        $payload['unidades'] = $request->filled('unidades') ? (int)$request->input('unidades') : null;

        $oferta = Auth::user()->ofertasRuta()->create($payload);
        return response()->json(['message' => 'Oferta de ruta creada exitosamente', 'data' => $oferta->load('truckType')], 201);
    }

    public function show(OfertaRuta $oferta): JsonResponse
    {
        $oferta->append('tipo_despacho_texto');
        return response()->json($oferta->load(['truckType', 'user', 'bids.user']));
    }

    public function update(Request $request, OfertaRuta $oferta): JsonResponse
    {
        $this->authorize('update', $oferta);

        $validated = $request->validate([
            'tipo_camion'       => 'sometimes|required|exists:truck_types,id',
            'origen'            => 'sometimes|required|string|max:255',
            'destino'           => 'sometimes|required|string|max:255',
            'fecha_inicio'      => 'sometimes|required|date_format:Y-m-d H:i:s',
            'capacidad'         => 'sometimes|required|integer|min:1',
            'precio_referencial'=> 'sometimes|required|numeric|min:0',
            'descripcion'       => 'nullable|string',
            'unidades'          => 'nullable|integer|min:1',
            'tipo_despacho'     => ['nullable', Rule::in(['despacho_anticipado', 'despacho_general', 'no_sabe_no_responde'])],
        ]);

        $data = $validated;

        if ($request->has('fecha_inicio')) {
            $newFechaInicio = \Carbon\Carbon::parse($request->fecha_inicio);
            if ($newFechaInicio->toDateTimeString() !== $oferta->fecha_inicio->toDateTimeString()) {
                $data['expiry_notification_sent_at'] = null;
                $data['expired_notification_sent_at'] = null;
            }
        }

        if ($request->has('unidades')) {
            $data['unidades'] = $request->input('unidades');
        }
        
        $oferta->update($data);

        return response()->json([
            'message' => 'Oferta de ruta actualizada exitosamente',
            'data'    => $oferta->load('truckType')
        ]);
    }
    
    public function destroy(OfertaRuta $oferta): JsonResponse
    {
        $this->authorize('delete', $oferta);
        $oferta->delete();
        return response()->json(['message' => 'Oferta de ruta eliminada exitosamente'], 200);
    }

    public function getTruckTypes(): JsonResponse
    {
        return response()->json(TruckType::orderBy('name')->get(['id', 'name', 'description']));
    }
}