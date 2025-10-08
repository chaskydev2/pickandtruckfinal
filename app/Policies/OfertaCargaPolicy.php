<?php

namespace App\Policies;

use App\Models\OfertaCarga;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

// App/Policies/OfertaCargaPolicy.php
class OfertaCargaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) { return true; }
    public function view(?User $user, OfertaCarga $oferta) { return true; }
    public function create(User $user) { return true; }

    public function update(User $user, OfertaCarga $oferta)
    {
        if ($oferta->isLocked()) return false;
        return $user->id === $oferta->user_id;
    }

    public function delete(User $user, OfertaCarga $oferta)
    {
        if ($oferta->isLocked()) return false;
        return $user->id === $oferta->user_id;
    }

    /** Nueva: crear una oferta (bid) sobre esta publicación de carga */
    public function createBid(User $user, OfertaCarga $oferta): bool
    {
        if ($oferta->isLocked()) return false;             // no nuevas ofertas si la publicación está lock
        if ($user->id === $oferta->user_id) return false;  // el dueño no se oferta

        // Si el usuario ya tiene una oferta ACTIVA (pendiente/aceptado/pendiente_confirmacion/terminado) en ESTA publicación, NO permitir
        $yaTieneActiva = $oferta->bids()
            ->where('user_id', $user->id)
            ->whereIn('estado', ['pendiente','aceptado','pendiente_confirmacion','terminado'])
            ->exists();

        if ($yaTieneActiva) return false;

        // Si llegó aquí, o no tiene ofertas previas, o todas las previas fueron rechazadas
        return true;
    }

}
