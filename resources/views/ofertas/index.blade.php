@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <!-- Navigation tabs -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link {{ !request()->has('view') ? 'active' : '' }}" href="{{ route('ofertas.index') }}">
                    <i class="fas fa-list-ul me-1"></i> Publicaciones disponibles
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->get('view') === 'mine' ? 'active' : '' }}"
                    href="{{ route('ofertas.index', ['view' => 'mine']) }}">
                    <i class="fas fa-user-check me-1"></i> Mis Publicaciones
                    @if($misOfertasCount > 0)
                        <span class="badge offer-count">{{ $misOfertasCount }}</span>
                    @else
                        <span class="badge bg-secondary">{{ $misOfertasCount }}</span>
                    @endif
                </a>
            </li>
        </ul>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">
                    {{ request()->get('view') === 'mine' ? 'Mis publicaciones' : 'Publicaciones disponibles' }}
                </h1>
                <p class="text-muted">
                    {{ request()->get('view') === 'mine'
                        ? 'Administra tus ofertas de ruta publicadas'
                        : 'Explora las últimas rutas disponibles en el mercado' }}
                </p>
            </div>

            <div>
                <a href="{{ route('ofertas.create') }}" class="btn-pickn">
                    <i class="fas fa-plus"></i> Publicar ruta
                </a>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('ofertas.index') }}" method="GET" class="row g-3 align-items-end">
                    @if (request()->has('view'))
                        <input type="hidden" name="view" value="{{ request('view') }}">
                    @endif

                    <div class="col-md-3">
                        <label for="truck_type" class="form-label">Tipo de Camión</label>
                        <select class="form-select" id="truck_type" name="truck_type">
                            <option value="">Todos los tipos</option>
                            @foreach ($truckTypes as $type)
                                <option value="{{ $type->id }}"
                                    {{ request('truck_type') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="origen" class="form-label">Origen</label>
                        <input type="text" class="form-control" id="origen" name="origen"
                            value="{{ request('origen') }}" placeholder="Ciudad o estado">
                    </div>

                    <div class="col-md-3">
                        <label for="destino" class="form-label">Destino</label>
                        <input type="text" class="form-control" id="destino" name="destino"
                            value="{{ request('destino') }}" placeholder="Ciudad o estado">
                    </div>

                    <div class="col-md-3">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if (count($ofertas) > 0)
            <!-- Feed de Ofertas -->
            <div id="publications-list" class="timeline-feed">
                @foreach ($ofertas as $oferta)
                    @include('partials.oferta_ruta_card', ['oferta' => $oferta])
                @endforeach
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="display-1 text-muted mb-3">
                        <i class="fas {{ request()->get('view') === 'mine' ? 'fa-truck' : 'fa-search' }}"></i>
                    </div>
                    <h3 class="text-muted mb-3">
                        {{ request()->get('view') === 'mine'
                            ? 'No has publicado ninguna oferta de ruta aún'
                            : 'No hay ofertas de ruta disponibles' }}
                    </h3>
                    <p class="text-muted mb-0">
                        {{ request()->get('view') === 'mine'
                            ? 'Publica tu primera oferta de ruta y conecta con empresas de carga.'
                            : 'Prueba con otros filtros o publica tu propia oferta de ruta.' }}
                    </p>
                </div>
            </div>
        @endif

        <div class="mt-4 d-flex justify-content-center">
            {{ $ofertas->appends(request()->query())->links() }}
        </div>
    </div>

    @push('styles')
        <style>
            /* Estilos para las pestañas */
            .nav-tabs .nav-link {
                color: var(--color-text-dark);
                border: none;
                border-bottom: 3px solid transparent;
                padding: 0.75rem 1.25rem;
                font-weight: 500;
                transition: all 0.3s ease;
            }

            .nav-tabs .nav-link:hover {
                color: #38a169;
                border-bottom-color: rgba(56, 161, 105, 0.5);
            }

            .nav-tabs .nav-link.active {
                color: #38a169;
                border-bottom-color: #38a169;
                background-color: transparent;
            }

            /* Estilos adicionales si ya existen */
            .hover-shadow {
                transition: all 0.3s ease-in-out;
            }

            .hover-shadow:hover {
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
                transform: translateY(-2px);
            }

            .timeline-feed .feed-card::before {
                content: '';
                position: absolute;
                left: -10px;
                top: 20px;
                width: 20px;
                height: 20px;
                background-color: #fff;
                border: 3px solid #38a169;
                border-radius: 50%;
            }

            .timeline-feed .feed-card {
                position: relative;
                border-left: 3px solid #dee2e6;
                margin-left: 20px;
                border-radius: 0.5rem;
            }

            /* Estilos específicos para el botón de ordenar */
            .btn-outline-secondary {
                color: #6c757d !important;
                border-color: #6c757d !important;
                background-color: transparent !important;
            }

            .btn-outline-secondary:hover,
            .btn-outline-secondary:focus {
                color: #fff !important;
                background-color: #6c757d !important;
                border-color: #6c757d !important;
            }

            @media (max-width: 768px) {
                .timeline-feed .feed-card::before {
                    display: none;
                }

                .timeline-feed .feed-card {
                    margin-left: 0;
                    border-left: none;
                }
            }

            /* Ribbon style for assigned offers */
            .ribbon {
                width: 150px;
                height: 150px;
                overflow: hidden;
                position: absolute;
                z-index: 1;
            }

            .ribbon::before,
            .ribbon::after {
                position: absolute;
                z-index: -1;
                content: '';
                display: block;
                border: 5px solid #28a745;
            }

            .ribbon span {
                position: absolute;
                display: block;
                width: 225px;
                padding: 8px 0;
                background-color: #28a745;
                box-shadow: 0 5px 10px rgba(0, 0, 0, .1);
                color: #fff;
                font-size: 14px;
                font-weight: 600;
                text-shadow: 0 1px 1px rgba(0, 0, 0, .2);
                text-transform: uppercase;
                text-align: center;
            }

            .ribbon-top-right {
                top: -10px;
                right: -10px;
            }

            .ribbon-top-right::before,
            .ribbon-top-right::after {
                border-top-color: transparent;
                border-right-color: transparent;
            }

            .ribbon-top-right::before {
                top: 0;
                left: 0;
            }

            .ribbon-top-right::after {
                bottom: 0;
                right: 0;
            }

            .ribbon-top-right span {
                left: -25px;
                top: 30px;
                transform: rotate(45deg);
            }

            /* Make border more visible for assigned offers */
            .border-success {
                border: 2px solid #28a745 !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('echo:ready', () => {
                const list = document.getElementById('publications-list');
                if (!list || !window.Echo) return;

                window.Echo.channel('publications')
                    .listen('.publication.created', async (e) => {
                        if (e.type !== 'ruta') return; // esta vista es de RUTA
                        console.log('[RT ruta] recibido .publication.created', e);

                        try {
                            const url = "{{ route('partials.ofertas_ruta.card', ':id') }}".replace(':id', e
                                .publication.id);
                            const html = await fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            }).then(r => r.text());
                            const wrapper = document.createElement('div');
                            wrapper.innerHTML = html.trim();
                            const el = wrapper.firstElementChild;
                            if (!el) return;

                            list.prepend(el);
                            // animación opcional para la nueva tarjeta
                            el.classList.add('animate__animated', 'animate__fadeIn');
                        } catch (err) {
                            console.error('Error trayendo parcial (ruta):', err);
                        }
                    });
            });

            // Por si Echo ya estaba conectado antes de que cargue este script
            if (window.Echo && window.Echo.connector?.pusher?.connection?.state === 'connected') {
                document.dispatchEvent(new Event('echo:ready'));
            }
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Animación para las tarjetas
                const feedCards = document.querySelectorAll('.feed-card');
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('animate__animated', 'animate__fadeIn');
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1
                });

                feedCards.forEach(card => {
                    observer.observe(card);
                });
            });
        </script>
    @endpush
@endsection
