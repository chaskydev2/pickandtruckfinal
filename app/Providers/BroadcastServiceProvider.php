<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar rutas de autenticación de broadcasting sin middlewares problemáticos
        Broadcast::routes(['middleware' => ['web', 'auth:sanctum']]);
        
        // Cargar rutas de canales
        require base_path('routes/channels.php');
    }
}