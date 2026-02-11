<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Build Path
    |--------------------------------------------------------------------------
    |
    | Este valor determina la ruta "public" donde Vite compilará sus assets
    | durante el build. Esta ruta es relativa al directorio public de tu
    | aplicación. El valor por defecto es "build".
    |
    */

    'build_path' => env('VITE_BUILD_PATH', 'build'),

    /*
    |--------------------------------------------------------------------------
    | Hot File Path
    |--------------------------------------------------------------------------
    |
    | Este valor determina la ruta donde Vite guardará información sobre
    | el servidor de desarrollo. Esta ruta es relativa al directorio public.
    |
    */

    'hot_file' => env('VITE_HOT_FILE', storage_path('app/vite.hot')),

    /*
    |--------------------------------------------------------------------------
    | Manifest Path  
    |--------------------------------------------------------------------------
    |
    | Ruta al archivo manifest.json de Vite. Cuando index.php está en raíz,
    | la ruta debe ajustarse acorde.
    |
    */

    'manifest_path' => public_path('build/.vite/manifest.json'),

];
