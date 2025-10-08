<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CargoType extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_carga'
    ];

    public function ofertasCarga()
    {
        return $this->hasMany(OfertaCarga::class, 'tipo_carga');
    }
}
