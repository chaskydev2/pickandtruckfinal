<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Estadísticas personales
        $stats = [
            'mis_ofertas_carga' => OfertaCarga::where('user_id', $user->id)->count(),
            'mis_ofertas_ruta' => OfertaRuta::where('user_id', $user->id)->count(),
            'mis_bids' => Bid::where('user_id', $user->id)->count(),
            'bids_aceptados' => Bid::where('user_id', $user->id)->where('estado', 'aceptado')->count(),
            'bids_rechazados' => Bid::where('user_id', $user->id)->where('estado', 'rechazado')->count(),
        ];
        // Últimas ofertas y bids
        $misOfertasCarga = OfertaCarga::where('user_id', $user->id)->latest()->take(10)->get();
        $misOfertasRuta = OfertaRuta::where('user_id', $user->id)->latest()->take(10)->get();
        $misBids = Bid::where('user_id', $user->id)->latest()->take(10)->get();
        $bidsRecibidos = Bid::whereHasMorph(
            'bideable',
            [OfertaCarga::class, OfertaRuta::class],
            function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }
        )
            ->with(['bideable', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        return response()->json([
            'stats' => $stats,
            'misOfertasCarga' => $misOfertasCarga,
            'misOfertasRuta' => $misOfertasRuta,
            'misBids' => $misBids,
            'bidsRecibidos' => $bidsRecibidos
        ]);
    }
}
