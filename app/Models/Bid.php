<?php
// App/Models/Bid.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bid extends Model
{
    use HasFactory;

    public const ST_ACEPTADO = 'aceptado';
    public const ST_PEND_CONF = 'pendiente_confirmacion';
    public const ST_TERMINADO = 'terminado';

    public const BLOCKING_STATES = [
        self::ST_ACEPTADO,
        self::ST_PEND_CONF,
        self::ST_TERMINADO,
    ];

    protected $fillable = [
        'user_id',
        'bideable_id',
        'bideable_type',
        'monto',
        'fecha_hora',
        'mensaje',
        'comentario',
        'estado',
        'confirmacion_usuario_a',
        'confirmacion_usuario_b',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'monto' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bideable()
    {
        return $this->morphTo();
    }

    public function chat()
    {
        return $this->hasOne(Chat::class, 'bid_id');
    }

    public function setEstadoAttribute($value)
    {
        $this->attributes['estado'] = strtolower(trim($value));
    }

    /** Bloquea edición/eliminación si está en estados duros */
    public function isLocked(): bool
    {
        return in_array($this->estado, self::BLOCKING_STATES, true);
    }

    /** Scope: bids bloqueantes */
    public function scopeBlocking($query)
    {
        return $query->whereIn('estado', self::BLOCKING_STATES);
    }

    /** Scope: por bideable concreto */
    public function scopeForBideable($query, Model $bideable)
    {
        return $query
            ->where('bideable_id', $bideable->getKey())
            ->where('bideable_type', get_class($bideable));
    }
}
