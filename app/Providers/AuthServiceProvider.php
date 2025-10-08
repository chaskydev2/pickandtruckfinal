<?php

namespace App\Providers;

use App\Models\OfertaRuta;
use App\Models\OfertaCarga;
use App\Models\Bid;
use App\Policies\OfertaRutaPolicy;
use App\Policies\OfertaCargaPolicy;
use App\Policies\BidPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        OfertaRuta::class => OfertaRutaPolicy::class,
        OfertaCarga::class => OfertaCargaPolicy::class,
        Bid::class => BidPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
