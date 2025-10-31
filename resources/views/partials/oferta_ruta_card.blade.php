@php
    // Estado general de la publicación
    $locked = $oferta->hasBlockingBid(); // (aceptado / pendiente_confirmacion / terminado)

    // Bid bloqueante (cualquiera) para esta oferta
    $blockingBid = $oferta->bids->first(function ($b) {
        return in_array($b->estado, ['aceptado', 'pendiente_confirmacion', 'terminado']);
    });

    // Bid bloqueante mío (si soy transportista y estoy involucrado)
    $myBlockingBid = Auth::check()
        ? $oferta->bids->first(function ($b) {
            return $b->user_id === Auth::id() &&
                in_array($b->estado, ['aceptado', 'pendiente_confirmacion', 'terminado']);
        })
        : null;

    // ¿Soy dueño de la publicación?
    $iAmOwner = Auth::check() && Auth::id() === $oferta->user_id;

    // ¿Estoy involucrado en el bid bloqueante?
    // - Transportista: si $myBlockingBid
    // - Dueño de la publicación: si existe $blockingBid
    $involved = $myBlockingBid !== null || ($iAmOwner && $blockingBid);

    // Texto del ribbon según los casos pedidos (no mostrar al dueño)
    $ribbonText = null;
    if (!$iAmOwner) {
        if ($myBlockingBid) {
            $ribbonText = $myBlockingBid->estado === 'terminado' ? 'Trabajo terminado' : 'Asignada';
        } elseif ($locked) {
            $ribbonText = 'No disponible';
        }
    }

    // Badge de estado “superior” (no mostrar al dueño)
    $statusBadge = null;
    if (!$iAmOwner) {
        if ($myBlockingBid) {
            $statusBadge =
                $myBlockingBid->estado === 'terminado'
                    ? ['text' => 'Trabajo terminado', 'class' => 'secondary']
                    : ['text' => 'Asignada', 'class' => 'success'];
        } elseif ($locked) {
            $statusBadge = ['text' => 'No disponible', 'class' => 'secondary'];
        }
    }

    // Bid que usaremos para el link de seguimiento (si soy el ofertante: el mío; si soy el dueño: el bloqueante)
    $bidForTracking = $myBlockingBid ?: $blockingBid;

    // Mi última oferta cualquiera (para alertas informativas)
    $myAnyBid = Auth::check() ? $oferta->bids->sortByDesc('created_at')->firstWhere('user_id', Auth::id()) : null;
@endphp

<div class="card feed-card mb-4 hover-shadow {{ $locked ? 'border-success' : '' }}" data-oferta-id="{{ $oferta->id }}">
    @if ($ribbonText)
        <div class="ribbon ribbon-top-right">
            <span class="ribbon-trabajo-terminado" @if($ribbonText==="Trabajo terminado") style="color:#fff !important;" @endif>{{ $ribbonText }}</span>
        </div>
    @endif

    <div class="card-body">
        <div class="row">
            <!-- Columna izquierda: Info principal -->
            <div class="col-md-8 border-end">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <span class="badge-darkish" style="color:#ffffff !important; font-weight:500 !important;">{{ $oferta->truckType?->name ?? 'Ruta' }}</span>
                        @if ($oferta->tipo_despacho)
                            <span class="badge-darkish ms-2" style="color:#ffffff !important; font-weight:500 !important;">{{ $oferta->tipo_despacho_texto }}</span>
                        @endif
                        @if (!is_null($oferta->unidades))
                            <span class="badge-darkish ms-2" style="color:#ffffff !important; font-weight:500 !important;">{{ number_format($oferta->unidades) }} uds</span>
                        @endif
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if ($statusBadge)
                            @if($statusBadge['text']==='Trabajo terminado')
                                <span class="badge badge-trabajo-terminado-bg">{{ $statusBadge['text'] }}</span>
                            @else
                                <span class="badge bg-{{ $statusBadge['class'] }}">{{ $statusBadge['text'] }}</span>
                            @endif
                        @endif
                        <span class="text-muted">{{ $oferta->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <h4 class="mb-3 fw-bold">
                    <i class="fas fa-map-marker-alt text-danger"></i>
                    {{ $oferta->origen }}
                    <i class="fas fa-long-arrow-alt-right mx-2"></i>
                    <i class="fas fa-map-marker-alt text-success"></i>
                    {{ $oferta->destino }}
                </h4>

                <p class="text-muted mb-1">
                    <i class="fas fa-calendar me-2"></i>Fecha de salida:
                    <strong>{{ $oferta->fecha_inicio->format('d/m/Y h:i A') }}</strong>
                </p>
                <p class="text-muted mb-1">
                    <i class="fas fa-weight me-2"></i>Capacidad:
                    <strong>{{ number_format($oferta->capacidad, 0) }} kg</strong>
                </p>

                @if (!is_null($oferta->unidades))
                    <p class="text-muted mb-1">
                        <i class="fas fa-boxes me-2"></i>Unidades:
                        <strong>{{ number_format($oferta->unidades) }}</strong>
                    </p>
                @endif

                <p class="text-success mb-3 fw-bold">
                    <i class="fas fa-dollar-sign me-2"></i>Precio referencial:
                    ${{ number_format($oferta->precio_referencial, 2) }}
                </p>

                @if ($oferta->descripcion)
                    <p class="text-muted fst-italic">
                        <i class="fas fa-comment-dots me-2"></i>
                        {{ Str::limit($oferta->descripcion, 80) }}
                    </p>
                @endif

                @if ($myAnyBid)
                    @if ($myAnyBid->estado === 'terminado')
                        {{-- Suave: alerta verde de baja saturación para Trabajo terminado --}}
                        <div class="alert alert-success-soft p-2 mb-3">
                            <i class="fas fa-info-circle me-1"></i> Ya ofertaste en esta ruta (Trabajo terminado)
                        </div>
                    @elseif ($myAnyBid->estado === 'pendiente_confirmacion')
                        <div class="alert alert-primary p-2 mb-3">
                            <i class="fas fa-hourglass-half me-1"></i> Tienes una finalización pendiente de confirmar.
                        </div>
                    @else
                        <div class="alert alert-success-soft p-2 mb-3">
                            <i class="fas fa-info-circle me-1"></i> Ya ofertaste en esta ruta
                        </div>
                    @endif
                @endif

                <div class="mt-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            @if ($oferta->user->empresa && $oferta->user->empresa->logo)
                                <img src="{{ $oferta->user->empresa->logo }}"
                                    alt="{{ $oferta->user->empresa->nombre }}" class="rounded-circle" width="40">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($oferta->user->empresa ? $oferta->user->empresa->nombre : $oferta->user->name) }}&background=random"
                                    alt="{{ $oferta->user->empresa ? $oferta->user->empresa->nombre : $oferta->user->name }}"
                                    class="rounded-circle" width="40">
                            @endif
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">
                                {{ $oferta->user->empresa ? $oferta->user->empresa->nombre : $oferta->user->name }}
                            </h6>
                            <small class="text-muted">
                                @if ($oferta->user->verified)
                                    <i class="fas fa-check-circle text-success"></i> Empresa verificada
                                @else
                                    <i class="fas fa-hourglass-half text-warning"></i> Verificación pendiente
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: Acciones y Stats -->
            <div class="col-md-4 d-flex flex-column">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Ofertas recibidas:</span>
                        @if($oferta->bids->count() > 0)
                            <span class="badge offer-count">
                                {{ $oferta->bids->count() }}
                            </span>
                            @else
                            @php $bidsCount = $oferta->bids->count(); @endphp
                            @if($bidsCount > 0)
                                <span class="badge offer-count">{{ $bidsCount }}</span>
                            @else
                                <span class="badge offer-count">0</span>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="mt-auto">
                    <div class="d-grid gap-2">
                        {{-- Ver detalles SIEMPRE --}}
                        <a href="{{ route('ofertas.show', $oferta) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-info-circle"></i> Ver detalles
                        </a>

                        @if ($involved && $bidForTracking)
                            {{-- Involucrado en bid bloqueante (aceptado/pendiente_confirmacion/terminado) --}}
                            <a href="{{ route('work.show', $bidForTracking) }}"
                                class="btn {{ $myBlockingBid && $myBlockingBid->estado === 'terminado' ? 'btn-historico' : 'btn-success' }}">
                                <i class="fas fa-truck-loading"></i>
                                {{ $myBlockingBid && $myBlockingBid->estado === 'terminado' ? 'Ver seguimiento (histórico)' : 'Seguimiento' }}
                            </a>
                        @else
                            {{-- No involucrado --}}
                            @if ($locked)
                                {{-- Bloqueado por otro → No disponible (sin Hacer oferta) --}}
                                <button class="btn btn-unavailable" disabled>
                                    <i class="fas fa-ban"></i> No disponible
                                </button>
                            @else
                                {{-- No bloqueado y no involucrado: puedo ofertar (si policy) --}}
                                @can('createBid', $oferta)
                                    <a href="{{ route('bids.create', ['type' => 'ruta', 'id' => $oferta->id]) }}"
                                        class="btn btn-success">
                                        <i class="fas fa-hand-holding-usd"></i> Hacer Oferta
                                    </a>
                                @else
                                    <button class="btn btn-unavailable" disabled>
                                        <i class="fas fa-lock"></i> No disponible
                                    </button>
                                @endcan
                            @endif
                        @endif

                        @if ($iAmOwner)
                            {{-- Herramientas del dueño (no cambia la lógica de Seguimiento) --}}
                            @can('update', $oferta)
                                <a href="{{ route('ofertas.edit', $oferta) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Editar oferta
                                </a>
                            @else
                                <button class="btn btn-warning" disabled>
                                    <i class="fas fa-lock"></i> Edición no disponible
                                </button>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
