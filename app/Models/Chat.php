<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    protected $fillable = [
        'bid_id',
        'created_at',
        'updated_at'
    ];

    protected $with = ['messages'];

    public function bid(): BelongsTo
    {
        return $this->belongsTo(Bid::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function participants()
    {
        // Obtenemos los participantes a través del bid
        if ($this->bid) {
            return [
                'user' => $this->bid->user,
                'bideable_user' => $this->bid->bideable->user ?? null
            ];
        }
        return [];
    }

    public function unreadCount($userId)
    {
        return $this->messages()
            ->where('user_id', '!=', $userId)
            ->where('read', false)
            ->count();
    }
}
