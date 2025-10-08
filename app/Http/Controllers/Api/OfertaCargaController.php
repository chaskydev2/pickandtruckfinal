<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OfertaCarga;
use App\Models\CargoType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OfertaCargaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = OfertaCarga::with(['cargoType', 'user' => fn($q) => $q->select('id', 'name')]);

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

        if ($request->filled('tipo_carga')) { $query->where('tipo_carga', $request->tipo_carga); }
        if ($request->filled('origen')) { $query->where('origen', 'LIKE', '%' . $request->origen . '%'); }
        if ($request->filled('destino')) { $query->where('destino', 'LIKE', '%' . $request->destino . '%'); }
        
        switch ($request->get('sort')) {
            case 'price_high': $query->orderBy('presupuesto', 'desc'); break;
            case 'price_low': $query->orderBy('presupuesto', 'asc'); break;
            case 'closest': $query->orderBy('fecha_inicio', 'asc'); break;
            default: $query->latest();
        }

        $query->withCount([
            'bids as bids_aceptadas_count' => function ($q) {
                $q->where('estado', 'aceptado'); // ← SOLO aceptado
            }
        ]);

        $pag = $query->paginate(10);
        $pag->getCollection()->transform(function ($oferta) {
            $oferta->setAttribute('asignada', ($oferta->bids_aceptadas_count ?? 0) > 0);
            return $oferta;
        });

        return response()->json($pag);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tipo_carga'    => 'required|exists:cargo_types,id',
            'origen'        => 'required|string|max:255',
            'destino'       => 'required|string|max:255',
            'fecha_inicio'  => 'required|date_format:Y-m-d H:i:s',
            'peso'          => 'required|numeric|min:0',
            'presupuesto'   => 'required|numeric|min:0',
            'descripcion'   => 'nullable|string',
            'unidades'      => 'nullable|integer|min:1',
            'es_contenedor' => 'nullable|boolean',
        ]);
        
        $payload = $validated;
        $payload['es_contenedor'] = $request->has('es_contenedor') ? $request->boolean('es_contenedor') : null;
        $payload['unidades'] = $request->filled('unidades') ? (int)$request->input('unidades') : null;
        
        $oferta = Auth::user()->ofertasCarga()->create($payload);
        return response()->json(['message' => 'Oferta de carga creada exitosamente', 'data' => $oferta->load('cargoType')], 201);
    }

    public function show(OfertaCarga $oferta): JsonResponse
    {
        return response()->json($oferta->load(['cargoType', 'user', 'bids.user']));
    }

    public function update(Request $request, OfertaCarga $oferta): JsonResponse
    {
        $this->authorize('update', $oferta);

        $validated = $request->validate([
            'tipo_carga'    => 'sometimes|required|exists:cargo_types,id',
            'origen'        => 'sometimes|required|string|max:255',
            'destino'       => 'sometimes|required|string|max:255',
            'fecha_inicio'  => 'sometimes|required|date_format:Y-m-d H:i:s',
            'peso'          => 'sometimes|required|numeric|min:0',
            'presupuesto'   => 'sometimes|required|numeric|min:0',
            'descripcion'   => 'nullable|string',
            'unidades'      => 'nullable|integer|min:1',
            'es_contenedor' => 'nullable|boolean',
        ]);

        $data = $validated;
        
        if ($request->has('fecha_inicio')) {
            $newFechaInicio = \Carbon\Carbon::parse($request->fecha_inicio);
            if ($newFechaInicio->toDateTimeString() !== $oferta->fecha_inicio->toDateTimeString()) {
                $data['expiry_notification_sent_at'] = null;
                $data['expired_notification_sent_at'] = null;
            }
        }
        
        // Mantenemos tu lógica original para manejar los campos opcionales
        if ($request->has('es_contenedor')) {
            $data['es_contenedor'] = $request->boolean('es_contenedor');
        }
        if ($request->has('unidades')) {
            $data['unidades'] = $request->input('unidades');
        }

        $oferta->update($data);

        return response()->json([
            'message' => 'Oferta de carga actualizada exitosamente',
            'data'    => $oferta->load('cargoType')
        ]);
    }

    public function destroy(OfertaCarga $oferta): JsonResponse
    {
        $this->authorize('delete', $oferta);
        $oferta->delete();
        return response()->json(['message' => 'Oferta de carga eliminada exitosamente'], 200);
    }

    public function getCargoTypes(): JsonResponse
    {
        return response()->json(CargoType::orderBy('name')->get(['id', 'name', 'description']));
    }
}