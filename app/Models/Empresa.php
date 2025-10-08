<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Empresa extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'nombre',
        'logo',
        'descripcion',
        'telefono',
        'direccion',
        'sitio_web',
        'verificada'
    ];

    /**
     * Relación con el usuario propietario de la empresa
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
