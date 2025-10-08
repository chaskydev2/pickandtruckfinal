<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// App/Models/OfertaRuta.php
class OfertaRuta extends Model
{
    use HasFactory;

    protected $table = 'ofertas_ruta';

    protected $fillable = [
        'user_id',
        'tipo_camion',
        'origen',
        'destino',
        'fecha_inicio',
        'capacidad',
        'precio_referencial',
        'descripcion',
        'unidades',
        'tipo_despacho',
        'expiry_notification_sent_at',
        'expired_notification_sent_at',
    ];

    protected $casts = [
        'fecha_inicio'       => 'datetime',
        'capacidad'          => 'integer',
        'precio_referencial' => 'decimal:2',
        'unidades'           => 'integer',
    ];

    public function getTipoDespachoTextoAttribute()
    {
        switch ($this->tipo_despacho) {
            case 'despacho_anticipado': return 'Despacho Anticipado';
            case 'despacho_general':    return 'Despacho General';
            case 'no_sabe_no_responde': return 'No sabe/No responde';
            default:                    return null;
        }
    }

    public function truckType()
    {
        return $this->belongsTo(TruckType::class, 'tipo_camion');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bids()
    {
        return $this->morphMany(Bid::class, 'bideable');
    }

    public function hasBlockingBid(): bool
    {
        return $this->bids()->blocking()->exists();
    }

    public function isLocked(): bool
    {
        return $this->hasBlockingBid();
    }
}
