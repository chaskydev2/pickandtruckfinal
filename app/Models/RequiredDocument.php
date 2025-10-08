<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RequiredDocument extends Model
{
    protected $fillable = [
        'name',
        'description',
        'notes',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function userDocuments(): HasMany
    {
        return $this->hasMany(UserDocument::class);
    }
}
