<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OfertaCarga;
use App\Models\OfertaRuta;
use App\Models\Bid;
use Illuminate\Support\Facades\Auth;

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
            
            // Estadísticas de bids enviados por estado
            'bids_aceptados' => Bid::where('user_id', $user->id)->where('estado', 'aceptado')->count(),
            'bids_rechazados' => Bid::where('user_id', $user->id)->where('estado', 'rechazado')->count(),
            'bids_pendientes' => Bid::where('user_id', $user->id)->where('estado', 'pendiente')->count(),
            
            // Total de bids recibidos en mis publicaciones y sus estados
            'bids_recibidos' => Bid::whereHasMorph(
                'bideable', 
                [OfertaCarga::class, OfertaRuta::class], 
                function($query) use ($user) {
                    $query->where('user_id', $user->id);
                }
            )->count(),
            
            'bids_recibidos_pendientes' => Bid::whereHasMorph(
                'bideable', 
                [OfertaCarga::class, OfertaRuta::class], 
                function($query) use ($user) {
                    $query->where('user_id', $user->id);
                }
            )->where('estado', 'pendiente')->count(),
            
            'bids_por_aprobar' => Bid::whereHasMorph(
                'bideable', 
                [OfertaCarga::class, OfertaRuta::class], 
                function($query) use ($user) {
                    $query->where('user_id', $user->id);
                }
            )->whereNull('estado')->orWhere('estado', '')->count(),
            
            // Estadísticas generales
            'total_ofertas_carga' => OfertaCarga::count(),
            'total_ofertas_ruta' => OfertaRuta::count(),
            'total_bids' => Bid::count()
        ];

        // Mis últimas ofertas de carga - Corregido para cargar bids correctamente
        $misOfertasCarga = OfertaCarga::where('user_id', $user->id)
            ->with(['cargoType', 'bids'])
            ->withCount('bids')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Obtener todas las ofertas de ruta sin límite
        $misOfertasRuta = OfertaRuta::where('user_id', $user->id)
            ->with(['truckType', 'bids'])
            ->withCount('bids')
            ->orderBy('created_at', 'desc')
            ->get();

        // Obtener TODOS los bids con relaciones bideable válidas
        $misBids = Bid::where('user_id', $user->id)
            ->whereHas('bideable')  // Solo incluir bids con relación bideable válida
            ->with(['bideable' => function($query) {
                $query->withTrashed();  // Incluir ofertas incluso si están eliminadas
            }, 'bideable.user'])
            ->orderBy('created_at', 'desc')
            ->get();  // Obtener todos los registros sin paginación

        // Bids recibidos en mis publicaciones
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

        return view('dashboard', compact(
            'stats',
            'misOfertasCarga',
            'misOfertasRuta',
            'misBids',
            'bidsRecibidos'
        ));
    }
}
