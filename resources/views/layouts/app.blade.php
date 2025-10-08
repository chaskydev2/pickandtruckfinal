<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- Tailwind / app --}}

    <link href="{{ asset('css/theme.css') }}" rel="stylesheet"> {{-- Tu paleta/variables --}}
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet"> {{-- Ajustes finos al final --}}

    <!-- 4) Font Awesome (puede ir aquí o arriba; no afecta la cascada de botones) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Cargar chat.js después de app.js -->
    <script src="{{ asset('js/chat.js') }}" defer></script>

    @stack('styles')

    <style>
        /* Correcciones globales para problemas de diseño */
        .card-body {
            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        /* Evitar que contenido largo desborde contenedores */
        p,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        a,
        span,
        div {
            max-width: 100%;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        /* Asegurar que los containers sean responsive */
        .container,
        .container-fluid {
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
        }

        /* Estilos específicos para secciones de información de contacto */
        .card-info-section {
            margin-bottom: 1rem;
        }

        .card-info-section p {
            margin-bottom: 0.5rem;
        }

        /* Ajustes adicionales */
        @media (max-width: 767px) {
            .col-md-4 {
                margin-top: 1.5rem;
            }
        }

        /* Corrección CRÍTICA para el desplegable de notificaciones */
        .dropdown-content {
            min-width: 400px !important;
            width: 400px !important;
            max-width: 90vw !important;
        }

        #notification-list {
            min-width: 100% !important;
            width: 100% !important;
        }

        .notification-item {
            white-space: normal !important;
            overflow: visible !important;
            text-overflow: clip !important;
            word-break: break-word !important;
            word-wrap: break-word !important;
            display: block !important;
            width: 100% !important;
        }

        /* Selector más fuerte para sobrescribir estilos que puedan interferir */
        [x-ref="panel"],
        div[x-ref="panel"] {
            min-width: 400px !important;
            width: 400px !important;
            max-width: 90vw !important;
        }

        /* Asegurar textos negros y fondos blancos en inputs */
        .container .card {
            color: #000000 !important;
        }

        .container .form-control,
        .container .form-select,
        .container input[type="text"],
        .container input[type="email"],
        .container input[type="password"],
        .container input[type="number"],
        .container input[type="date"],
        .container textarea,
        .container select {
            background-color: #ffffff !important;
            color: #000000 !important;
            border: 1px solid #ced4da !important;
        }

        /* Asegurar que texto en cards sea negro */
        .card-title,
        .card-header h5,
        .card-text,
        .card-body p,
        .card-body label,
        .card-body h1,
        .card-body h2,
        .card-body h3,
        .card-body h4,
        .card-body h5,
        .card-body h6 {
            color: #000000 !important;
        }

        /* Excepciones para elementos con color definido */
        .badge,
        .btn,
        .alert,
        .text-muted,
        .text-success,
        .text-danger,
        .text-warning,
        .text-info,
        .text-primary,
        .navbar,
        .text-white {
            color: inherit !important;
        }
    </style>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="font-sans antialiased" @stack('body_attrs')>
    <div class="min-h-screen bg-light">
        @include('layouts.navigation')

        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main>
            @yield('content')
            {{ $slot ?? '' }}
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('js/notifications.js') }}"></script>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Esperar un momento para asegurar que todo esté cargado
            setTimeout(function() {
                try {
                    // Verificar si RealTimeNotifications está disponible
                    if (typeof RealTimeNotifications !== 'undefined') {
                        // Inicializar el gestor de notificaciones en tiempo real
                        window.notificationsManager = new RealTimeNotifications({
                            updateInterval: 5000,
                            playSound: false,
                            notificationBadgeSelector: '#notification-badge',
                            notificationListSelector: '#notification-list',
                            markAsReadUrl: '{{ route('notifications.read', ':id') }}',
                            checkNotificationsUrl: '{{ route('notifications.check') }}'
                        });
                        console.log('Sistema de notificaciones inicializado correctamente');
                    } else {
                        console.warn(
                            'RealTimeNotifications no está disponible. Asegúrate de que notifications.js se cargue correctamente.'
                            );
                    }
                } catch (error) {
                    console.error('Error al inicializar sistema de notificaciones:', error);
                }
            }, 500); // Pequeño retraso para asegurar que todo esté listo
        });
    </script>

    @if (session('bid_success'))
        <div class="modal fade" id="bidSuccessModal" tabindex="-1" aria-labelledby="bidSuccessModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="bidSuccessModalLabel">¡Oferta enviada exitosamente!</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center py-4 text-dark">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success fa-4x"></i>
                        </div>
                        <h4 class="mb-3 text-dark">Tu oferta fue enviada correctamente</h4>
                        <p class="text-dark">Tu oferta le llegará a <strong>{{ session('recipient_name') }}</strong>
                            inmediatamente.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <a href="{{ route('home') }}" class="btn btn-success px-4">
                            <i class="fas fa-search me-2"></i> Seguir explorando
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if (session('bid_success'))
                    var bidSuccessModal = new bootstrap.Modal(document.getElementById('bidSuccessModal'));
                    bidSuccessModal.show();
                @endif
            });
        </script>
    @endif
    @if (session('publication_success'))
        <div class="modal fade" id="publicationSuccessModal" tabindex="-1"
            aria-labelledby="publicationSuccessModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="publicationSuccessModalLabel">¡Publicación creada exitosamente!</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center py-4 text-dark">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success fa-4x"></i>
                        </div>
                        <h4 class="mb-3 text-dark">Tu publicación fue creada correctamente</h4>
                        <p class="text-dark">Tu publicación ya está disponible para que otros usuarios puedan
                            encontrarla.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <a href="{{ session('publication_url') }}" class="btn btn-success px-4">
                            <i class="fas fa-eye me-2"></i> Ver publicación
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if (session('publication_success'))
                    var publicationSuccessModal = new bootstrap.Modal(document.getElementById(
                        'publicationSuccessModal'));
                    publicationSuccessModal.show();
                @endif
            });
        </script>
    @endif

    <!-- Script para verificación periódica del estado del usuario -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Verificar el estado cada 10 segundos
            const checkUserStatus = () => {
                fetch('{{ route('user.checkStatus') }}', {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.is_blocked && data.redirect_url) {
                            window.location.href = data.redirect_url;
                        }
                    })
                    .catch(error => console.error('Error al verificar el estado:', error));
            };

            // Verificar inmediatamente al cargar la página
            checkUserStatus();

            // Configurar el intervalo para verificar cada 10 segundos
            setInterval(checkUserStatus, 10000);
        });
    </script>
    <x-profile-alert />

</body>

</html>
