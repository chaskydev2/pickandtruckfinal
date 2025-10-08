<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixRoutesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Arregla problemas de rutas limpiando las cachés del sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Arreglando problemas de rutas...');
        
        // Ejecutar todos los comandos de limpieza
        $this->call('route:clear');
        $this->call('config:clear');
        $this->call('cache:clear');
        $this->call('view:clear');
        $this->call('optimize:clear');
        
        // Volver a generar manualmente la caché de rutas
        $this->info('Regenerando caché de rutas...');
        $this->call('route:cache');
        
        $this->info('Todas las cachés han sido limpiadas y regeneradas.');
        $this->info('Por favor reinicie el servidor para aplicar los cambios.');
        
        return Command::SUCCESS;
    }
}
