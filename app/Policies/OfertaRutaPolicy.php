<?php

namespace App\Policies;

use App\Models\OfertaRuta;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

// App/Policies/OfertaRutaPolicy.php
class OfertaRutaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) { return true; }
    public function view(?User $user, OfertaRuta $oferta) { return true; }
    public function create(User $user) { return true; }

    public function update(User $user, OfertaRuta $oferta)
    {
        if ($oferta->isLocked()) return false;
        return $user->id === $oferta->user_id;
    }

    public function delete(User $user, OfertaRuta $oferta)
    {
        if ($oferta->isLocked()) return false;
        return $user->id === $oferta->user_id;
    }

    /** Nueva: crear una oferta (bid) sobre esta publicación de ruta */
    public function createBid(User $user, OfertaRuta $oferta): bool
    {
        if ($oferta->isLocked()) return false;             // no nuevas ofertas si la publicación está lock
        if ($user->id === $oferta->user_id) return false;  // el dueño no se oferta

        // Bloquear si el usuario ya tiene oferta ACTIVA en ESTA publicación
        $yaTieneActiva = $oferta->bids()
            ->where('user_id', $user->id)
            ->whereIn('estado', ['pendiente','aceptado','pendiente_confirmacion','terminado'])
            ->exists();

        if ($yaTieneActiva) return false;

        return true;
    }

}
