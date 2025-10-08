<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" type="image/png">

    <!-- Montserrat Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 auth-background">
        <div class="mt-6 mb-6">
            <a href="/">
                <img src="{{ asset('images/pickntruck.png') }}" alt="PickNTruck Logo" class="w-auto h-12">
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>

        <div class="mt-8 text-center text-sm text-gray-600">
            &copy; {{ date('Y') }} PickNTruck - Todos los derechos reservados
        </div>
    </div>
</body>

</html>

<style>
    .auth-background {
        background-color: #1a2b4c;
        /* Azul oscuro */
        background-image: radial-gradient(circle at 50% 50%, #2a3b5c, #1a2b4c 70%);
    }

    /* Estilos para textos en el fondo oscuro */
    .text-gray-600 {
        color: #e0e0e0 !important;
    }

    /* Estilos para el logo */
    .h-12 {
        filter: brightness(1.2);
    }

    /* Estilos para botones de login/register */
    button[type="submit"],
    .inline-flex,
    .bg-gray-800,
    .bg-indigo-600 {
        background-color: #1a2b4c !important;
        color: #ffffff !important;
        border-color: #1a2b4c !important;
    }

    button[type="submit"]:hover,
    .inline-flex:hover,
    .bg-gray-800:hover,
    .bg-indigo-600:hover {
        background-color: #2a3b5c !important;
    }

    /* Asegurar que textos en formularios sean negros, excepto enlaces */
    .bg-white label span.text-gray-600 {
        color: #000000 !important;
    }

    /* Solución específica para el checkbox "Recuérdame" */
    .inline-flex.items-center span.text-gray-600 {
        color: #000000 !important;
    }

    /* Pero mantener enlaces con su color */
    a.text-gray-600,
    a .text-gray-600 {
        color: #4299e1 !important;
    }
</style>
