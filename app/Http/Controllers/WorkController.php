<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bid;
use Illuminate\Support\Facades\Auth;

class WorkController extends Controller
{
    /**
     * Muestra una lista de trabajos asociados al usuario autenticado.
     */
    public function index()
    {
        $user = Auth::user();

        $bids = Bid::with(['bideable', 'user', 'bideable.user'])
            ->where('estado', '!=', 'pendiente')
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhereHas('bideable', function($q) use ($user) {
                          $q->where('user_id', $user->id);
                      });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('work.index', compact('bids'));
    }

    /**
     * Muestra los detalles de un trabajo específico.
     */
    public function show($id)
    {
        $bid = Bid::with(['user', 'bideable.user', 'chat'])
            ->findOrFail($id);

        $userId = Auth::id();

        if ($userId !== $bid->user_id && $userId !== $bid->bideable->user_id) {
            abort(403, 'No tienes permiso para ver este trabajo.');
        }

        return view('work.show', compact('bid'));
    }
}
