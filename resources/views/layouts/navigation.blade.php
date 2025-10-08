<nav x-data="{ open: false }" class="bg-nav border-b border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                @php
                    $isHome = request()->routeIs('home');
                @endphp
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="logo-link {{ $isHome ? 'logo-active' : '' }}"
                        aria-current="{{ $isHome ? 'page' : 'false' }}" title="Inicio">
                        <img src="{{ asset('images/pickntruck.png') }}" alt="PickNTruck Logo"
                            class="logo-img h-8 w-auto">
                        <span class="sr-only">Inicio</span>
                    </a>
                </div>

                <!-- Navigation Tabs (desktop) -->
                <div class="hidden sm:flex sm:items-center sm:ms-10">
                    @php
                        $isCarga = request()->routeIs('ofertas_carga.*');
                        $isRuta = request()->routeIs('ofertas.*');
                    @endphp

                    <a href="{{ route('ofertas_carga.index') }}" class="tab-link {{ $isCarga ? 'tab-active' : '' }}"
                        aria-current="{{ $isCarga ? 'page' : 'false' }}">
                        <i class="fa-solid fa-box-open me-2"></i>
                        Publicaciones de Carga
                    </a>

                    <a href="{{ route('ofertas.index') }}" class="tab-link {{ $isRuta ? 'tab-active' : '' }}"
                        aria-current="{{ $isRuta ? 'page' : 'false' }}">
                        <i class="fa-solid fa-truck-moving me-2"></i>
                        Publicaciones de Ruta
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            @auth
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <!-- Notifications -->
                    <div class="relative mr-6">
                        <x-dropdown align="right" width="400">
                            <x-slot name="trigger">
                                <button
                                    x-on:click="$nextTick(() => { if (window.markAllAsRead) { window.markAllAsRead().catch(console.error); } })"
                                    id="notifications-button"
                                    class="relative p-1 text-white bg-transparent border-0 rounded-md hover:bg-white/10 focus:bg-white/10 focus:outline-none focus:ring-0"
                                    aria-label="Notificaciones">
                                    {{-- Ícono campana visible en fondo oscuro --}}
                                    <svg class="nav-bell-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>

                                    {{-- Badge de nuevas notificaciones (ya respeta tu lógica de conteo) --}}
                                    <span id="notification-badge"
                                        class="notif-badge {{ auth()->user()->unreadNotifications->count() > 0 ? '' : 'hidden' }}">
                                        {{ auth()->user()->unreadNotifications->count() }}
                                    </span>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div id="notification-list" class="max-h-64 overflow-y-auto"
                                    style="min-width: 400px !important; width: 100% !important;">
                                    @forelse(auth()->user()->notifications->take(5) as $notification)
                                        <div class="notification-item block px-4 py-3 text-sm hover:bg-gray-100 {{ $notification->read_at ? 'text-gray-500' : 'font-semibold' }} text-dark border-bottom w-100"
                                            style="white-space: normal; word-break: break-word;">

                                            {{-- Enlace principal que marca como leída --}}
                                            <a href="{{ $notification->data['url'] ?? '#' }}"
                                                onclick="event.preventDefault(); if(window.markAsRead) { window.markAsRead('{{ $notification->id }}').then(() => window.location.href='{{ $notification->data['url'] ?? '#' }}'); }"
                                                class="text-decoration-none text-dark d-block notification-link">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2">
                                                        <i
                                                            class="{{ $notification->data['icon'] ?? 'fas fa-bell text-primary' }} fa-lg"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <p class="mb-1">
                                                            @if (isset($notification->data['title']))
                                                                <strong
                                                                    class="d-block">{{ $notification->data['title'] }}</strong>
                                                            @endif
                                                            {{ $notification->data['message'] ?? 'Nueva notificación' }}
                                                        </p>
                                                        <small
                                                            class="text-secondary">{{ $notification->created_at->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                            </a>

                                            @if (isset($notification->data['actions']) && is_array($notification->data['actions']))
                                                <div class="actions-container">
                                                    @foreach ($notification->data['actions'] as $action)
                                                        @if (isset($action['is_delete']) && $action['is_delete'])
                                                            <form action="{{ $action['url'] }}" method="POST"
                                                                class="d-inline"
                                                                onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta publicación?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-sm {{ $action['class'] ?? 'btn-secondary' }} action-btn">
                                                                    {{ $action['text'] }}
                                                                </button>
                                                            </form>
                                                        @else
                                                            <!-- Botón modificar o similar -->
                                                            <a href="{{ $action['url'] }}"
                                                                class="btn btn-sm {{ $action['class'] ?? 'btn-primary' }} action-btn">
                                                                {{ $action['text'] }}
                                                            </a>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="px-4 py-3 text-sm text-center text-secondary">
                                            No hay notificaciones
                                        </div>
                                    @endforelse
                                </div>
                                <div class="border-t border-gray-100">
                                    <a href="{{ route('notifications.index') }}"
                                        class="block px-4 py-3 text-sm font-medium text-center text-dark hover:bg-gray-100">
                                        Ver todas las notificaciones
                                    </a>
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    <!-- User dropdown -->
                    <div class="ml-2">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="flex items-center text-white bg-transparent border-0 rounded-md px-2 py-1 hover:bg-white/10 focus:bg-white/10 focus:outline-none focus:ring-0">
                                    @if (Auth::user()->empresa)
                                        <div class="flex items-center">
                                            @if (Auth::user()->empresa->logo)
                                                <img src="{{ Auth::user()->empresa->logo }}"
                                                    alt="{{ Auth::user()->empresa->nombre }}"
                                                    class="h-6 w-6 rounded-full object-cover mr-2">
                                            @else
                                                <div
                                                    class="h-6 w-6 rounded-full bg-gray-500 flex items-center justify-center mr-2 text-xs text-white">
                                                    {{ substr(Auth::user()->empresa->nombre, 0, 1) }}
                                                </div>
                                            @endif
                                            <div>{{ Auth::user()->empresa->nombre }}</div>
                                        </div>
                                    @else
                                        <div>{{ Auth::user()->name }}</div>
                                    @endif
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Perfil') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('profile.documents')">
                                    {{ __('Documentos') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('empresas.show')">
                                    {{ __('Mi Empresa') }}
                                </x-dropdown-link>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Cerrar Sesión') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            @else
                <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
                    <a href="{{ route('login') }}"
                        class="text-white hover:text-gray-300 rounded-md px-4 py-2 text-base font-medium">
                        Ingresar
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary rounded-md px-4 py-2 text-base font-medium">
                        Registrarse
                    </a>
                </div>
            @endauth

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 transition duration-150 ease-in-out"
                    aria-label="Toggle menu">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden bg-nav">
        @php
            $isCarga = request()->routeIs('ofertas_carga.*');
            $isRuta = request()->routeIs('ofertas.*');
        @endphp
        <div class="pt-2 pb-3 space-y-2 px-3">
            <a href="{{ route('ofertas_carga.index') }}"
                class="tab-link-mobile {{ $isCarga ? 'tab-mobile-active' : '' }}">
                <i class="fa-solid fa-box-open me-2"></i> Ofertas de Carga
            </a>
            <a href="{{ route('ofertas.index') }}" class="tab-link-mobile {{ $isRuta ? 'tab-mobile-active' : '' }}">
                <i class="fa-solid fa-truck-moving me-2"></i> Ofertas de Ruta
            </a>
        </div>

        @auth
            <div class="pt-4 pb-1 border-t border-gray-700">
                <div class="px-4">
                    @if (Auth::user()->empresa)
                        <div class="flex items-center mb-2">
                            @if (Auth::user()->empresa->logo)
                                <img src="{{ Auth::user()->empresa->logo }}" alt="{{ Auth::user()->empresa->nombre }}"
                                    class="h-8 w-8 rounded-full object-cover mr-2">
                            @else
                                <div
                                    class="h-8 w-8 rounded-full bg-gray-500 flex items-center justify-center mr-2 text-sm text-white">
                                    {{ substr(Auth::user()->empresa->nombre, 0, 1) }}
                                </div>
                            @endif
                            <div class="font-medium text-base text-white">{{ Auth::user()->empresa->nombre }}</div>
                        </div>
                    @else
                        <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                    @endif
                    <div class="font-medium text-sm text-gray-300">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.show')" class="text-white">
                        {{ __('Mi Perfil') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('profile.documents')" class="text-white">
                        {{ __('Documentos') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('empresas.show')" class="text-white">
                        {{ __('Mi Empresa') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')" class="text-white"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Cerrar Sesión') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 pb-1 border-t border-gray-700">
                <div class="px-4 space-y-2">
                    <a href="{{ route('login') }}"
                        class="block w-full px-4 py-2 text-base font-medium text-white hover:bg-gray-700">
                        {{ __('Ingresar') }}
                    </a>
                    <a href="{{ route('register') }}"
                        class="btn btn-primary block w-full text-center rounded-md px-4 py-2 text-base font-medium">
                        {{ __('Registrarse') }}
                    </a>
                </div>
            </div>
        @endauth
    </div>
</nav>

<style>
    /* ====== Notificaciones ====== */

    /* Solo el ENLACE PRINCIPAL ocupa todo el ancho */
    #notification-list .notification-item>a.notification-link {
        white-space: normal;
        overflow: visible;
        text-overflow: clip;
        display: block;
        width: 100%;
    }

    /* Contenedor de acciones alineado a la derecha en una fila */
    .notification-item .actions-container {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 0.5rem;
        border-top: 1px solid #eee;
        padding-top: 0.5rem;
        margin-top: 0.5rem;
        flex-wrap: wrap;
        /* evita salto a nueva línea */
    }

    /* Fuerza a los botones de acción a NO ser de ancho completo */
    #notification-list .notification-item .actions-container a,
    #notification-list .notification-item .actions-container .btn,
    #notification-list .notification-item .actions-container button {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        width: auto !important;
        white-space: nowrap;
    }

    /* El form de eliminar en línea */
    #notification-list .notification-item .actions-container form {
        display: inline !important;
        margin: 0;
        padding: 0;
    }

    /* Botones compactos y consistentes */
    .notification-item .action-btn {
        min-width: 110px;
        margin: 0;
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        box-sizing: border-box;
    }

    /* ===== Dropdown ancho mínimo ===== */
    .dropdown-menu {
        min-width: 320px !important;
    }

    .dropdown-content {
        min-width: 320px !important;
        width: auto !important;
    }

    /* ===== Tabs desktop ===== */
    .tab-link {
        position: relative;
        display: inline-flex;
        align-items: center;
        margin-right: .5rem;
        padding: .5rem .9rem;
        border-radius: .65rem;
        font-weight: 600;
        font-size: .95rem;
        color: #cfe0ff;
        transition: all .18s ease-in-out;
        text-decoration: none !important;
        border: 1px solid rgba(255, 255, 255, .08);
        background: rgba(255, 255, 255, .02);
    }

    .tab-link:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, .12);
        border-color: rgba(255, 255, 255, .24);
        transform: translateY(-1px);
    }

    .tab-link::after {
        content: '';
        position: absolute;
        left: 12px;
        right: 12px;
        bottom: -10px;
        height: 3px;
        border-radius: 3px;
        background: transparent;
        transform: scaleX(0);
        transform-origin: center;
        transition: transform .18s ease, background .18s ease;
    }

    .tab-active {
        color: #fff !important;
        background: rgba(255, 255, 255, .14) !important;
        border-color: rgba(255, 255, 255, .26) !important;
    }

    .tab-active::after {
        background: linear-gradient(90deg, #4DD0E1, #2E7D32);
        transform: scaleX(1);
    }

    .logo-link {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: .75rem;
        padding: .35rem .55rem;
        border-radius: .65rem;
        background: rgba(255, 255, 255, .03);
        border: 1px solid rgba(255, 255, 255, .08);
        transition: background .18s ease, border-color .18s ease, transform .18s ease, filter .18s ease;
    }

    .logo-link:hover {
        background: rgba(255, 255, 255, .12);
        border-color: rgba(255, 255, 255, .24);
        transform: translateY(-1px);
        filter: brightness(1.05);
    }

    .logo-link::after {
        content: '';
        position: absolute;
        left: 10px;
        right: 10px;
        bottom: -10px;
        height: 3px;
        border-radius: 3px;
        background: transparent;
        transform: scaleX(0);
        transform-origin: center;
        transition: transform .18s ease, background .18s ease;
    }

    .logo-active {
        background: rgba(255, 255, 255, .16) !important;
        border-color: rgba(255, 255, 255, .30) !important;
    }

    .logo-active::after {
        background: linear-gradient(90deg, #4DD0E1, #2E7D32);
        transform: scaleX(1);
    }

    .logo-link:focus-visible {
        outline: 2px solid rgba(77, 208, 225, .8);
        outline-offset: 2px;
        box-shadow: 0 0 0 3px rgba(77, 208, 225, .25);
    }

    .logo-img {
        transition: transform .18s ease, filter .18s ease;
    }

    .logo-link:hover .logo-img {
        transform: scale(1.04);
        filter: drop-shadow(0 0 6px rgba(255, 255, 255, .15));
    }

    /* Ícono campana legible en nav oscuro */
    .nav-bell-icon {
        width: 24px;
        height: 24px;
        color: #e8eeff;
        /* blanco-azulado para contraste */
        opacity: .95;
        transition: opacity .15s ease, transform .15s ease;
    }

    #notifications-button:hover .nav-bell-icon,
    #notifications-button:focus .nav-bell-icon {
        opacity: 1;
        transform: translateY(-1px);
    }
</style>
