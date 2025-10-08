<?php

namespace App\Http\Controllers;

use App\Models\OfertaCarga;
use App\Models\OfertaRuta;
use App\Models\Bid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class HomeController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        $showWelcomeModal = false;

        // Redirigir a login si no está autenticado
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Si el usuario está autenticado pero no está verificado, redirigir a la página de documentos
        if (Auth::check() && !Auth::user()->verified) {
            return redirect()->route('profile.document-submission')
                ->with('warning', 'Debe completar la verificación de documentos para acceder a la plataforma.');
        }

        $user = Auth::user();
        
        // Verificar si el usuario acaba de ser verificado (todos sus documentos aprobados)
        if ($user->verified && !session('welcomed_verified_user')) {
            $showWelcomeModal = true;
            // Marcar al usuario como que ya ha visto el mensaje de bienvenida
            session(['welcomed_verified_user' => true]);
        }

        // Obtener las últimas ofertas del usuario con recuento correcto de bids
        $misOfertasCarga = $user->ofertasCarga()
            ->with(['cargoType'])
            ->withCount('bids') // Esto asegura que se cargue correctamente el contador
            ->latest()
            ->take(5)
            ->get();
            
        $misOfertasRuta = $user->ofertasRuta()
            ->with(['truckType'])
            ->withCount('bids') // Esto asegura que se cargue correctamente el contador
            ->latest()
            ->take(5)
            ->get();
        
        // Obtener todos los bids del usuario que aún tengan ofertas válidas
        $misBids = $user->bids()
            ->with(['bideable', 'bideable.user'])
            ->whereHasMorph('bideable', ['App\Models\OfertaCarga', 'App\Models\OfertaRuta'])
            ->latest()
            ->get();
            
        // Obtener los bids recibidos en ofertas del usuario
        $bidsRecibidos = Bid::with(['bideable', 'user'])
            ->where(function($query) use ($user) {
                // Para ofertas de carga
                $query->whereHasMorph(
                    'bideable',
                    'App\Models\OfertaCarga',
                    function($query) use ($user) {
                        $query->where('user_id', $user->id);
                    }
                );
                
                // Para ofertas de ruta
                $query->orWhereHasMorph(
                    'bideable',
                    'App\Models\OfertaRuta',
                    function($query) use ($user) {
                        $query->where('user_id', $user->id);
                    }
                );
            })
            ->where('user_id', '!=', $user->id)
            ->latest()
            ->take(5)
            ->get();
        
        // Estadísticas
        $stats = [
            'mis_ofertas_carga' => $user->ofertasCarga()->count(),
            'mis_ofertas_ruta' => $user->ofertasRuta()->count(),
            'mis_bids' => $user->bids()->count(),
            'total_ofertas_carga' => OfertaCarga::count(),
            'total_ofertas_ruta' => OfertaRuta::count(),
            'total_bids' => Bid::count(),
            'bids_aceptados' => $user->bids()->where('estado', 'aceptado')->count(),
            'bids_rechazados' => $user->bids()->where('estado', 'rechazado')->count(),
            'bids_pendientes' => $user->bids()->where('estado', 'pendiente')->count(),
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
            )->whereNull('estado')->orWhere('estado', '')->count()
        ];

        return view('dashboard', compact(
            'misOfertasCarga',
            'misOfertasRuta',
            'misBids',
            'bidsRecibidos',
            'stats',
            'showWelcomeModal'
        ));
    }
}
