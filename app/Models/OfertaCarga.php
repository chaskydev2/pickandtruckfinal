<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// App/Models/OfertaCarga.php
class OfertaCarga extends Model
{
    use HasFactory;

    protected $table = 'ofertas_carga';

    protected $fillable = [
        'user_id',
        'tipo_carga',
        'origen',
        'destino',
        'fecha_inicio',
        'peso',
        'presupuesto',
        'descripcion',
        'unidades',
        'es_contenedor',
        'expiry_notification_sent_at',
        'expired_notification_sent_at',
    ];

    protected $casts = [
        'fecha_inicio'   => 'datetime',
        'peso'           => 'decimal:2',
        'presupuesto'    => 'decimal:2',
        'unidades'       => 'integer',
        'es_contenedor'  => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cargoType()
    {
        return $this->belongsTo(CargoType::class, 'tipo_carga');
    }

    public function bids()
    {
        return $this->morphMany(Bid::class, 'bideable');
    }

    /** ¿Tiene algún bid en estado bloqueante? */
    public function hasBlockingBid(): bool
    {
        return $this->bids()->blocking()->exists();
    }

    /** Conveniencia para usar en policies/vistas */
    public function isLocked(): bool
    {
        return $this->hasBlockingBid();
    }
}
