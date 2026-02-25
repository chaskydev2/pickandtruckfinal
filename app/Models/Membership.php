<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tier',
        'billing_cycle',
        'price_paid',
        'status',
        'start_date',
        'end_date',
        'payment_method',
        'payment_proof_path',
    ];

    protected $casts = [
        'price_paid' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Constantes
    const TIER_PIONEROS = 'pioneros';
    const TIER_VISIONARIOS = 'visionarios';
    const TIER_CONSERVADORES = 'conservadores';
    const BILLING_MONTHLY = 'monthly';
    const BILLING_ANNUALLY = 'annually';
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELLED = 'cancelled';
    const PAYMENT_QR = 'qr';
    const PAYMENT_CRYPTO = 'crypto';
    const PAYMENT_OTHER = 'other';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
