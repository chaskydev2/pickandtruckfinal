<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemoRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'company_name',
        'company_type',
        'additional_info',
        'status',
        'requested_at',
        'contacted_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'contacted_at' => 'datetime',
    ];

    // Constantes
    const STATUS_PENDING = 'pending';
    const STATUS_CONTACTED = 'contacted';
    const STATUS_COMPLETED = 'completed';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeContacted($query)
    {
        return $query->where('status', self::STATUS_CONTACTED);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function markAsContacted(): void
    {
        $this->update([
            'status' => self::STATUS_CONTACTED,
            'contacted_at' => now(),
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
        ]);
    }
}
