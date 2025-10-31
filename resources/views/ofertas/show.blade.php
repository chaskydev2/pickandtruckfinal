@extends('layouts.app')

@section('content')
    @php
        // Estados clave
        $locked = $oferta->hasBlockingBid(); // bloqueada por oferta aceptada o flujo de finalización
        $acceptedBid = $oferta->bids->firstWhere('estado', 'aceptado');
        $hasAcceptedBid = (bool) $acceptedBid;
    @endphp
    <div class="container py-4">
        <!-- Breadcrumb con estilo mejorado -->
        <div class="card shadow-sm mb-4">
            <div class="card-body p-3">
                <nav aria-label="breadcrumb" class="bg-transparent m-0">
                    <ol class="breadcrumb p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('ofertas.index') }}">
                                Publicaciones de Ruta
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Detalles</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-truck me-2"></i>Oferta de Ruta #{{ $oferta->id }}
                </h5>
                <span class="badge bg-light text-dark">
                    Creada {{ $oferta->created_at->diffForHumans() }}
                </span>
            </div>

            <div class="card-body">
                <div class="row">
                    <!-- Columna con información principal -->
                    <div class="col-md-8">
                        <!-- Datos básicos -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">Detalles de la ruta</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <h6 class="fw-bold text-muted mb-2">Origen</h6>
                                        <p class="mb-0">{{ $oferta->origen }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <h6 class="fw-bold text-muted mb-2">Destino</h6>
                                        <p class="mb-0">{{ $oferta->destino }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <h6 class="fw-bold text-muted mb-2">Fecha Inicio</h6>
                                        <p class="mb-0">{{ $oferta->fecha_inicio->format('d/m/Y h:i A') }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <h6 class="fw-bold text-muted mb-2">Tipo de Camión</h6>
                                        <p class="mb-0">{{ $oferta->truckType?->name ?? 'No especificado' }}</p>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <h6 class="fw-bold text-muted mb-2">Capacidad</h6>
                                        <p class="mb-0">{{ number_format($oferta->capacidad, 0) }} kg</p>
                                    </div>

                                    @if ($oferta->tipo_despacho)
                                        <div class="col-md-6 mb-3">
                                            <h6 class="fw-bold text-muted mb-2">Tipo de Despacho</h6>
                                            <p class="mb-0">{{ $oferta->tipo_despacho_texto }}</p>
                                        </div>
                                    @endif

                                    <!-- ④ NUEVO: Unidades -->
                                    @if (!is_null($oferta->unidades))
                                        <div class="col-md-6 mb-3">
                                            <h6 class="fw-bold text-muted mb-2">Unidades</h6>
                                            <p class="mb-0">{{ number_format($oferta->unidades) }}</p>
                                        </div>
                                    @endif

                                    <div class="col-md-6 mb-3">
                                        <h6 class="fw-bold text-muted mb-2">Precio Referencial</h6>
                                        <p class="mb-0">${{ number_format($oferta->precio_referencial, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="d-flex gap-2 mb-4">
                            @if (Auth::check())
                                @if (Auth::id() === $oferta->user_id)
                                    <!-- Botones para el dueño de la oferta -->
                                    @can('update', $oferta)
                                        <a href="{{ route('ofertas.edit', $oferta) }}" class="btn"
                                            style="background-color:#22c55e;border-color:#22c55e;color:#ffffff;">
                                            <i class="fas fa-edit me-1"></i> Editar oferta
                                        </a>
                                    @else
                                        <button class="btn btn-warning" disabled>
                                            <i class="fas fa-lock me-1"></i> Edición no disponible
                                        </button>
                                    @endcan

                                    @can('delete', $oferta)
                                        <form action="{{ route('ofertas.destroy', $oferta) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger"
                                                onclick="return confirm('¿Está seguro que desea eliminar esta oferta?')">
                                                <i class="fas fa-trash me-1"></i> Eliminar
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-danger" disabled>
                                            <i class="fas fa-lock me-1"></i> Eliminación no disponible
                                        </button>
                                    @endcan
                                @else
                                    <!-- Botones para otros usuarios -->
                                    @php
                                        $existingBid = $oferta->bids
                                            ->where('user_id', Auth::id())
                                            ->sortByDesc('created_at')
                                            ->first();
                                    @endphp

                                    @if ($existingBid)
                                        <!-- Si ya hizo una oferta -->
                                        @if ($existingBid->estado === 'pendiente' && !$hasAcceptedBid)
                                            @can('update', $existingBid)
                                                <a href="{{ route('bids.edit', $existingBid) }}" class="btn btn-warning">
                                                    <i class="fas fa-edit me-1"></i> Editar mi oferta
                                                </a>
                                            @else
                                                <button class="btn btn-warning" disabled>
                                                    <i class="fas fa-lock me-1"></i> Edición no disponible
                                                </button>
                                            @endcan
                                            @can('delete', $existingBid)
                                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                                    data-bs-target="#cancelBidInlineModal{{ $existingBid->id }}">
                                                    <i class="fas fa-times me-1"></i> Cancelar
                                                </button>
                                                <!-- Modal simple inline -->
                                                <div class="modal fade" id="cancelBidInlineModal{{ $existingBid->id }}"
                                                    tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Cancelar oferta</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                    aria-label="Cerrar"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                ¿Deseas cancelar tu oferta de
                                                                ${{ number_format($existingBid->monto, 2) }}?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Cerrar</button>
                                                                <form action="{{ route('bids.destroy', $existingBid) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">Sí,
                                                                        cancelar</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endcan
                                        @elseif(in_array($existingBid->estado, ['aceptado', 'pendiente_confirmacion', 'terminado']))
                                            <a href="{{ route('work.show', $existingBid) }}"
                                                class="btn btn-{{ $existingBid->estado === 'terminado' ? 'info' : 'success' }}">
                                                <i
                                                    class="fas {{ $existingBid->estado === 'terminado' ? 'fa-check-circle' : ($existingBid->estado === 'pendiente_confirmacion' ? 'fa-hourglass-half' : 'fa-truck-loading') }} me-1"></i>
                                                {{ $existingBid->estado === 'terminado' ? 'Trabajo Terminado' : ($existingBid->estado === 'pendiente_confirmacion' ? 'Pendiente Confirmación' : 'Seguimiento') }}
                                            </a>
                                        @elseif($existingBid->estado === 'rechazado')
                                            <span class="badge bg-secondary align-self-center">Tu oferta fue
                                                rechazada</span>
                                            @can('createBid', $oferta)
                                                <a href="{{ route('bids.create', ['type' => 'ruta', 'id' => $oferta->id]) }}"
                                                    class="btn btn-success">
                                                    <i class="fas fa-hand-holding-usd me-1"></i> Hacer nueva oferta
                                                </a>
                                            @endcan
                                        @endif
                                    @else
                                        <!-- Si no ha hecho una oferta -->
                                        @can('createBid', $oferta)
                                            <a href="{{ route('bids.create', ['type' => 'ruta', 'id' => $oferta->id]) }}"
                                                class="btn btn-success">
                                                <i class="fas fa-hand-holding-usd me-1"></i> Hacer una oferta
                                            </a>
                                        @else
                                            <button class="btn btn-secondary" disabled>
                                                <i class="fas fa-lock me-1"></i> No disponible para nuevas ofertas
                                            </button>
                                        @endcan
                                    @endif
                                @endif
                            @else
                                @if (!$hasAcceptedBid)
                                    <a href="{{ route('login') }}" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt me-1"></i> Inicia sesión para hacer una oferta
                                    </a>
                                @else
                                    <button class="btn btn-secondary" disabled>
                                        <i class="fas fa-lock me-1"></i> Oferta ya asignada
                                    </button>
                                @endif
                            @endif

                            <a href="{{ route('ofertas.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Volver a la lista
                            </a>
                        </div>

                        <!-- Sección de ofertas del usuario actual -->
                        @if (Auth::check() && Auth::id() !== $oferta->user_id)
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Mis ofertas para esta ruta</h5>
                                </div>
                                <div class="card-body">
                                    @if (isset($misBids) && $misBids->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Monto</th>
                                                        <th>Fecha</th>
                                                        <th>Comentario</th>
                                                        <th>Estado</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($misBids as $bid)
                                                        <tr>
                                                            <td>#{{ $loop->iteration }}</td>
                                                            <td>${{ number_format($bid->monto, 2) }}</td>
                                                            <td>{{ optional($bid->fecha_hora)->format('d/m/Y') }}</td>
                                                            <td>{{ $bid->comentario ?? 'Sin comentario' }}</td>
                                                            <td>
                                                                <span
                                                                    class="badge bg-{{ $bid->estado === 'aceptado' ? 'success' : ($bid->estado === 'rechazado' ? 'danger' : ($bid->estado === 'terminado' ? 'secondary' : 'warning')) }}">
                                                                    {{ ucfirst(str_replace('_', ' ', $bid->estado)) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                {{-- 1) Seguimiento cuando aceptado / pendiente_confirmacion / terminado --}}
                                                                @if (in_array($bid->estado, ['aceptado', 'pendiente_confirmacion', 'terminado']))
                                                                    <a href="{{ route('work.show', $bid) }}"
                                                                        class="btn btn-sm {{ $bid->estado === 'terminado' ? 'btn-info' : 'btn-success' }}">
                                                                        <i
                                                                            class="fas {{ $bid->estado === 'terminado' ? 'fa-check-circle' : ($bid->estado === 'pendiente_confirmacion' ? 'fa-hourglass-half' : 'fa-truck-loading') }} me-1"></i>
                                                                        {{ $bid->estado === 'terminado' ? 'Trabajo Terminado' : ($bid->estado === 'pendiente_confirmacion' ? 'Pendiente Confirmación' : 'Seguimiento') }}
                                                                    </a>
                                                                @endif

                                                                {{-- 2) Modificar / Cancelar si pendiente y policy lo permite --}}
                                                                @if ($bid->estado === 'pendiente')
                                                                    @can('update', $bid)
                                                                        <a href="{{ route('bids.edit', $bid) }}"
                                                                            class="btn btn-sm btn-warning mt-1">
                                                                            <i class="fas fa-edit me-1"></i> Modificar
                                                                        </a>
                                                                    @else
                                                                        <button class="btn btn-sm btn-warning mt-1" disabled>
                                                                            <i class="fas fa-lock me-1"></i> Modificar
                                                                        </button>
                                                                    @endcan

                                                                    @can('delete', $bid)
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-outline-danger mt-1"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#cancelBidModal{{ $bid->id }}">
                                                                            <i class="fas fa-times me-1"></i> Cancelar
                                                                        </button>
                                                                    @else
                                                                        <button class="btn btn-sm btn-outline-danger mt-1"
                                                                            disabled>
                                                                            <i class="fas fa-lock me-1"></i> Cancelar
                                                                        </button>
                                                                    @endcan

                                                                    <!-- Modal de confirmación para cancelar oferta -->
                                                                    <div class="modal fade"
                                                                        id="cancelBidModal{{ $bid->id }}"
                                                                        tabindex="-1"
                                                                        aria-labelledby="cancelBidModalLabel{{ $bid->id }}"
                                                                        aria-hidden="true">
                                                                        <div class="modal-dialog">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title"
                                                                                        id="cancelBidModalLabel{{ $bid->id }}">
                                                                                        Confirmar cancelación</h5>
                                                                                    <button type="button"
                                                                                        class="btn-close"
                                                                                        data-bs-dismiss="modal"
                                                                                        aria-label="Cerrar"></button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <p>¿Estás seguro de que deseas cancelar
                                                                                        esta oferta?</p>
                                                                                    <p class="mb-0">
                                                                                        <strong>Monto:</strong>
                                                                                        ${{ number_format($bid->monto, 2) }}
                                                                                    </p>
                                                                                    @if ($bid->comentario)
                                                                                        <p class="mb-0">
                                                                                            <strong>Comentario:</strong>
                                                                                            {{ $bid->comentario }}
                                                                                        </p>
                                                                                    @endif
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button"
                                                                                        class="btn btn-secondary"
                                                                                        data-bs-dismiss="modal">Cerrar</button>
                                                                                    <form
                                                                                        action="{{ route('bids.destroy', $bid) }}"
                                                                                        method="POST" class="d-inline">
                                                                                        @csrf
                                                                                        @method('DELETE')
                                                                                        <button type="submit"
                                                                                            class="btn btn-danger">Sí,
                                                                                            cancelar oferta</button>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                {{-- 5) Estados informativos --}}
                                                                @if ($bid->estado === 'rechazado')
                                                                    <span
                                                                        class="badge bg-secondary mt-1 d-inline-block">Oferta
                                                                        rechazada</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Botón para hacer una nueva oferta -->
                                        @php
                                            $tieneOfertaAceptada = $oferta->bids->contains('estado', 'aceptado');
                                        @endphp

                                        @if (!$tieneOfertaAceptada)
                                            @can('createBid', $oferta)
                                                <div class="text-center mt-3">
                                                    <a href="{{ route('bids.create', ['type' => 'ruta', 'id' => $oferta->id]) }}"
                                                        class="btn btn-primary">
                                                        <i class="fas fa-plus-circle me-1"></i> Hacer nueva oferta
                                                    </a>
                                                </div>
                                            @else
                                                <div class="alert alert-info mt-3 mb-0">
                                                    <i class="fas fa-info-circle me-2"></i> Esta publicación no acepta ofertas
                                                    en este momento.
                                                </div>
                                            @endcan
                                        @else
                                            <div class="alert alert-warning mt-3 mb-0">
                                                <i class="fas fa-info-circle me-2"></i> Esta ruta ya tiene una oferta
                                                aceptada y no está disponible para nuevas ofertas.
                                            </div>
                                        @endif
                                    @else
                                        @php
                                            $tieneOfertaAceptada = $oferta->bids->contains('estado', 'aceptado');
                                        @endphp

                                        @if (!$tieneOfertaAceptada)
                                            <div class="text-center py-4">
                                                <div class="alert alert-info d-inline-block px-4 py-3 mb-0"
                                                    role="alert">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    No has realizado ninguna oferta para esta ruta.
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-warning mt-3 mb-0">
                                                <i class="fas fa-info-circle me-2"></i> Esta ruta ya tiene una oferta
                                                aceptada y no está disponible para nuevas ofertas.
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Lista de Bids (para el dueño de la publicación) -->
                        @if (Auth::check() && Auth::id() === $oferta->user_id)
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Ofertas Recibidas ({{ $oferta->bids->count() }})</h5>
                                </div>
                                <div class="card-body">
                                    @if ($oferta->bids->isEmpty())
                                        <div class="text-center py-4">
                                            <p class="text-muted">Aún no hay ofertas para esta ruta</p>
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Ofertante</th>
                                                        <th>Monto</th>
                                                        <th>Fecha</th>
                                                        <th>Comentario</th>
                                                        <th>Estado</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($oferta->bids as $bid)
                                                        <tr data-bid-id="{{ $bid->id }}">
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($bid->user->name) }}&background=random"
                                                                        alt="{{ $bid->user->name }}"
                                                                        class="rounded-circle me-2" width="30">
                                                                    {{ $bid->user->name }}
                                                                </div>
                                                            </td>
                                                            <td>${{ number_format($bid->monto, 2) }}</td>
                                                            <td>{{ optional($bid->fecha_hora)->format('d/m/Y') }}</td>
                                                            <td>{{ $bid->comentario ?? 'Sin comentario' }}</td>
                                                            <td>
                                                                <span
                                                                    class="badge bg-{{ $bid->estado === 'aceptado' ? 'success' : ($bid->estado === 'rechazado' ? 'danger' : ($bid->estado === 'terminado' ? 'secondary' : 'warning')) }}">
                                                                    {{ ucfirst(str_replace('_', ' ', $bid->estado)) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $isOwner = Auth::id() === $oferta->user_id;
                                                                    $isBidOwner = Auth::id() === $bid->user_id;
                                                                    $inTrackedState = in_array($bid->estado, [
                                                                        'aceptado',
                                                                        'pendiente_confirmacion',
                                                                        'terminado',
                                                                    ]);
                                                                    // ya tienes $hasAcceptedBid calculado arriba (bool)
                                                                @endphp

                                                                {{-- 1) Seguimiento cuando está en flujo bloqueante --}}
                                                                @if ($inTrackedState && ($isOwner || $isBidOwner))
                                                                    <a href="{{ route('work.show', $bid) }}"
                                                                        class="btn btn-sm {{ $bid->estado === 'terminado' ? 'btn-info' : 'btn-success' }}">
                                                                        <i
                                                                            class="fas {{ $bid->estado === 'terminado' ? 'fa-check-circle' : ($bid->estado === 'pendiente_confirmacion' ? 'fa-hourglass-half' : 'fa-truck-loading') }} me-1"></i>
                                                                        {{ $bid->estado === 'terminado' ? 'Trabajo Terminado' : ($bid->estado === 'pendiente_confirmacion' ? 'Pendiente Confirmación' : 'Seguimiento') }}
                                                                    </a>
                                                                @else
                                                                    {{-- 2) Dueño: Aceptar / Rechazar si está pendiente y aún no hay aceptada --}}
                                                                    @if ($isOwner && $bid->estado === 'pendiente' && !$hasAcceptedBid)
                                                                        <div class="btn-group btn-group-sm">
                                                                            <form
                                                                                action="{{ route('bids.accept', $bid) }}"
                                                                                method="POST" class="me-1">
                                                                                @csrf
                                                                                <button type="submit"
                                                                                    class="btn btn-sm btn-success">
                                                                                    <i class="fas fa-check me-1"></i>
                                                                                    Aceptar
                                                                                </button>
                                                                            </form>
                                                                            <form
                                                                                action="{{ route('bids.reject', $bid) }}"
                                                                                method="POST">
                                                                                @csrf
                                                                                <button type="submit"
                                                                                    class="btn btn-sm btn-danger">
                                                                                    <i class="fas fa-times me-1"></i>
                                                                                    Rechazar
                                                                                </button>
                                                                            </form>
                                                                        </div>
                                                                    @else
                                                                        {{-- 5) Info si nada aplica --}}
                                                                        <span
                                                                            class="badge bg-secondary mt-1 d-inline-block">No
                                                                            disponible</span>
                                                                    @endif
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Columna lateral -->
                    <div class="col-md-4">
                        <!-- Información del anunciante -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">Anunciante</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        @if ($oferta->user->empresa && $oferta->user->empresa->logo)
                                            <img src="{{ $oferta->user->empresa->logo }}"
                                                alt="{{ $oferta->user->empresa->nombre }}" class="rounded-circle"
                                                width="50" height="50" style="object-fit: cover;">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($oferta->user->name) }}&background=random"
                                                alt="{{ $oferta->user->name }}" class="rounded-circle" width="50">
                                        @endif
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="fw-bold mb-0">
                                            @if ($oferta->user->empresa)
                                                {{ $oferta->user->empresa->nombre }}
                                                @if ($oferta->user->verified)
                                                    <i class="fas fa-check-circle text-success ms-1"
                                                        data-bs-toggle="tooltip" title="Usuario verificado"></i>
                                                @endif
                                            @else
                                                {{ $oferta->user->name }}
                                            @endif
                                        </h6>
                                        <p class="text-muted small mb-0">Usuario desde
                                            {{ $oferta->user->created_at->format('M Y') }}</p>
                                    </div>
                                </div>
                                @if ($oferta->user->empresa)
                                    <hr>
                                    <div class="mb-2">
                                        <h6 class="fw-bold text-muted small mb-1">Información de contacto</h6>
                                        @if ($oferta->user->empresa->telefono)
                                            <p class="mb-1 small"><i class="fas fa-phone-alt me-2 text-muted"></i>
                                                {{ $oferta->user->empresa->telefono }}</p>
                                        @endif
                                        <p class="mb-1 small"><i class="fas fa-envelope me-2 text-muted"></i>
                                            {{ $oferta->user->email }}</p>
                                        @if ($oferta->user->empresa->direccion)
                                            <p class="mb-1 small"><i class="fas fa-map-marker-alt me-2 text-muted"></i>
                                                {{ $oferta->user->empresa->direccion }}</p>
                                        @endif
                                        @if ($oferta->user->empresa->sitio_web)
                                            <p class="mb-0 small">
                                                <i class="fas fa-globe me-2 text-muted"></i>
                                                <a href="{{ $oferta->user->empresa->sitio_web }}"
                                                    target="_blank">{{ $oferta->user->empresa->sitio_web }}</a>
                                            </p>
                                        @endif
                                    </div>
                                    @if ($oferta->user->empresa->descripcion)
                                        <div class="mt-3">
                                            <h6 class="fw-bold text-muted small mb-1">Acerca de la empresa</h6>
                                            <p class="small mb-0">
                                                {{ Str::limit($oferta->user->empresa->descripcion, 200) }}</p>
                                            @if (strlen($oferta->user->empresa->descripcion) > 200)
                                                <a href="{{ route('empresas.show') }}" class="small">Leer más</a>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        @if ($oferta->descripcion)
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Comentarios Adicionales</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted mb-0" style="white-space: pre-wrap;">{{ $oferta->descripcion }}
                                    </p>
                                </div>
                            </div>
                        @endif

                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('js/ofertas-realtime.js') }}"></script>
@endpush
