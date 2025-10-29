@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-5 fw-bold text-primary">Dashboard</h1>
        <div>
            <a href="{{ route('ofertas_carga.create') }}" class="btn btn-success me-2">
                <i class="fas fa-box me-1"></i> Carga
            </a>
            <a href="{{ route('ofertas.create') }}" class="btn btn-secondary">
                <i class="fas fa-truck me-1"></i> Ruta
            </a>
        </div>
    </div>

    <!-- Se han eliminado las tres tarjetas de estadísticas de la parte superior -->
    
    <!-- Tabs de Contenido -->
    <div class="card">
        <div class="card-header p-0">
            <ul class="nav nav-tabs" id="dashboardTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="mis-ofertas-tab" data-bs-toggle="tab" data-bs-target="#mis-ofertas" type="button" role="tab" aria-controls="mis-ofertas" aria-selected="true">
                        <i class="fas fa-clipboard-list me-1"></i> Mis Publicaciones
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link nav-tab-enviadas" id="bids-enviados-tab" data-bs-toggle="tab" data-bs-target="#bids-enviados" type="button" role="tab" aria-controls="bids-enviados" aria-selected="false">
                        <i class="fas fa-paper-plane me-1"></i> Ofertas Enviadas
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link nav-tab-carga" id="bids-recibidos-tab" data-bs-toggle="tab" data-bs-target="#bids-recibidos" type="button" role="tab" aria-controls="bids-recibidos" aria-selected="false">
                        <i class="fas fa-inbox me-1"></i> Ofertas Recibidos
                    </button>
                </li>

            </ul>
        </div>
        <div class="card-body">
                <div class="tab-content" id="dashboardTabContent">
                <!-- Tab: Mis Ofertas -->
                <div class="tab-pane fade show active" id="mis-ofertas" role="tabpanel" aria-labelledby="mis-ofertas-tab">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center bg-dark text-white">
                                    <h5 class="mb-0 text-white">
                                        <i class="fas fa-box me-1"></i> Mis Publicaciones de Carga
                                    </h5>
                                    <a href="{{ route('ofertas_carga.index') }}?view=mine" class="btn btn-sm btn-success text-white">
                                        Ver todas
                                    </a>
                                </div>
                                <div class="card-body">
                                    @if($misOfertasCarga->isEmpty())
                                        <div class="text-center py-4">
                                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">
                                                Aún no has publicado ninguna carga.<br>
                                                Cuando publiques una carga, aparecerá aquí para que puedas gestionarla.
                                            </p>
                                        </div>
                                    @else
                                        <div class="list-group">
                                            @foreach($misOfertasCarga as $oferta)
                                                <a href="{{ route('ofertas_carga.show', $oferta) }}" class="list-group-item list-group-item-action">
                                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                                        <h6 class="mb-1">{{ $oferta->origen }} → {{ $oferta->destino }}</h6>
                                                        <span class="badge bg-primary rounded-pill">{{ $oferta->bids_count }} bids</span>
                                                    </div>
                                                    <p class="mb-1">{{ $oferta->cargoType->name }} - ${{ number_format($oferta->presupuesto, 2) }}</p>
                                                    <small class="text-muted">{{ $oferta->created_at->diffForHumans() }}</small>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center bg-dark text-white">
                                    <h5 class="mb-0 text-white">
                                        <i class="fas fa-truck me-1"></i> Missss publicaciones de Ruta
                                    </h5>
                                    <a href="{{ route('ofertas.index') }}?view=mine" class="btn btn-sm btn-success text-white">
                                        Ver todas
                                    </a>
                                </div>
                                <div class="card-body">
                                    @if($misOfertasRuta->isEmpty())
                                        <div class="text-center py-4">
                                            <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">
                                                Aún no has publicado ninguna ruta.<br>
                                                Cuando publiques una ruta, aparecerá aquí para que puedas gestionarla.
                                            </p>
                                        </div>
                                    @else
                                        <div class="list-group">
                                            @foreach($misOfertasRuta as $oferta)
                                                <a href="{{ route('ofertas.show', $oferta) }}" class="list-group-item list-group-item-action">
                                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                                        <h6 class="mb-1">{{ $oferta->origen }} → {{ $oferta->destino }}</h6>
                                                        <span class="badge bg-success rounded-pill">{{ $oferta->bids_count }} bids</span>
                                                    </div>
                                                    <p class="mb-1">{{ $oferta->truckType->name }} - ${{ number_format($oferta->precio_referencial, 2) }}</p>
                                                    <small class="text-muted">{{ $oferta->created_at->diffForHumans() }}</small>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tab: Bids Enviados -->
                <div class="tab-pane fade" id="bids-enviados" role="tabpanel" aria-labelledby="bids-enviados-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Ofertas que he realizado</h5>
                        <a href="{{ route('bids.index') }}" class="btn btn-sm btn-outline-secondary">Ver todas</a>
                    </div>
                    
                    @if($misBids->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-paper-plane fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">
                                Aún no has realizado ninguna oferta.<br>
                                Cuando envíes una oferta, aparecerá aquí para que puedas consultarla y gestionarla.
                            </p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#ID</th>
                                        <th>Tipo</th>
                                        <th>Ruta</th>
                                        <th>Mi Oferta</th>
                                        <th>Publicación de</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($misBids as $bid)
                                        @if($bid->bideable)
                                            <tr data-bid-id="{{ $bid->id }}">
                                                <td>#{{ $bid->id }}</td>
                                                <td>
                                                    <span class="badge rounded-pill bg-{{ $bid->bideable_type == 'App\Models\OfertaCarga' ? 'primary' : 'success' }}">
                                                        <i class="fas fa-{{ $bid->bideable_type == 'App\Models\OfertaCarga' ? 'box' : 'truck' }}"></i>
                                                        {{ $bid->bideable_type == 'App\Models\OfertaCarga' ? 'Carga' : 'Ruta' }}
                                                    </span>
                                                </td>
                                                <td>{{ $bid->bideable->origen }} → {{ $bid->bideable->destino }}</td>
                                                <td class="fw-bold">${{ number_format($bid->monto, 2) }}</td>
                                                <td>
                                                    @if($bid->bideable->user->empresa)
                                                        {{ $bid->bideable->user->empresa->nombre_empresa }}
                                                    @else
                                                        {{ $bid->bideable->user->name }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $bid->estado === 'aceptado' ? 'success' : ($bid->estado === 'rechazado' ? 'danger' : 'warning') }}">
                                                        {{ ucfirst($bid->estado) }}
                                                    </span>
                                                </td>
                                                <td>{{ $bid->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    @if($bid->estado === 'aceptado')
                                                        <a href="{{ route('work.show', $bid) }}" class="btn btn-sm btn-success">
                                                            <i class="fas fa-truck-loading me-1"></i> Seguimiento
                                                        </a>
                                                    @else
                                                        <a href="{{ route($bid->bideable_type == 'App\Models\OfertaCarga' ? 'ofertas_carga.show' : 'ofertas.show', $bid->bideable) }}" 
                                                        class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-eye me-1"></i> Ver
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td>#{{ $bid->id }}</td>
                                                <td colspan="7" class="text-center text-muted">
                                                    <em>Oferta ya no disponible</em>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    @endif
                </div>
                
                <!-- Tab: Bids Recibidos -->
                <div class="tab-pane fade" id="bids-recibidos" role="tabpanel" aria-labelledby="bids-recibidos-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Ofertas recibidas en mis publicaciones</h5>
                        <a href="{{ route('bids.received') }}" class="btn btn-sm btn-outline-secondary">Ver todas</a>
                    </div>
                    
                    @if($bidsRecibidos->isEmpty() ?? true)
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">
                                No has recibido ofertas en tus publicaciones.<br>
                                Cuando recibas ofertas, aparecerán aquí para que puedas gestionarlas.
                            </p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Mi Publicación</th>
                                        <th>Oferta</th>
                                        <th>Ofertante</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bidsRecibidos as $bid)
                                        <tr data-bid-id="{{ $bid->id }}">
                                            <td>
                                                <span class="badge rounded-pill bg-{{ $bid->bideable_type == 'App\Models\OfertaCarga' ? 'primary' : 'success' }}">
                                                    <i class="fas fa-{{ $bid->bideable_type == 'App\Models\OfertaCarga' ? 'box' : 'truck' }}"></i>
                                                    {{ $bid->bideable_type == 'App\Models\OfertaCarga' ? 'Carga' : 'Ruta' }}
                                                </span>
                                            </td>
                                            <td>{{ $bid->bideable->origen }} → {{ $bid->bideable->destino }}</td>
                                            <td class="fw-bold">${{ number_format($bid->monto, 2) }}</td>
                                            <td>
                                                <span class="d-flex align-items-center">
                                                    @if($bid->user->empresa && $bid->user->empresa->logo)
                                                        <img src="{{ $bid->user->empresa->logo }}" 
                                                             alt="{{ $bid->user->empresa->nombre }}" class="rounded-circle me-2" width="30">
                                                    @else
                                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($bid->user->empresa ? $bid->user->empresa->nombre : $bid->user->name) }}&background=random" 
                                                             alt="{{ $bid->user->empresa ? $bid->user->empresa->nombre : $bid->user->name }}" class="rounded-circle me-2" width="30">
                                                    @endif
                                                    {{ $bid->user->empresa ? $bid->user->empresa->nombre : $bid->user->name }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $bid->estado === 'aceptado' ? 'success' : ($bid->estado === 'rechazado' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($bid->estado) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($bid->estado === 'pendiente')
                                                    <div class="btn-group btn-group-sm">
                                                        <form action="{{ route('bids.accept', $bid) }}" method="POST" class="me-1">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">
                                                                <i class="fas fa-check me-1"></i> Aceptar
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('bids.reject', $bid) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="fas fa-times me-1"></i> Rechazar
                                                            </button>
                                                        </form>
                                                    </div>
                                                @elseif($bid->estado === 'aceptado')
                                                    <a href="{{ route('work.show', $bid) }}" class="btn btn-sm btn-success">
                                                        <i class="fas fa-truck-loading me-1"></i> Seguimiento
                                                    </a>
                                                @else
                                                    <a href="{{ route($bid->bideable_type == 'App\Models\OfertaCarga' ? 'ofertas_carga.show' : 'ofertas.show', $bid->bideable) }}" 
                                                    class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-eye me-1"></i> Ver
                                                    </a>
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
        </div>
    </div>
</div>


@endsection

@push('styles')
<style>
    .nav-tabs .nav-link {
        border-top: none;
        border-left: none;
        border-right: none;
        border-bottom: 3px solid transparent;
        color: var(--color-nav-background);
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        border-color: var(--color-primary);
        color: #ffffff !important;
        background-color: var(--color-nav-background) !important;
    }
    
    /* Sobrescribir colores específicos para clases especializadas */
    .nav-tab-enviadas, 
    .nav-tab-carga, 
    .nav-tab-estadisticas,
    #mis-ofertas-tab {
        color: var(--color-nav-background) !important;
    }
    
    /* Establecer color específico cuando está activa para consistencia */
    .nav-tabs .nav-link.active#mis-ofertas-tab,
    .nav-tabs .nav-link.active.nav-tab-enviadas,
    .nav-tabs .nav-link.active.nav-tab-carga,
    .nav-tabs .nav-link.active.nav-tab-estadisticas {
        color: #ffffff !important;
        background-color: var(--color-nav-background) !important;
    }
    
    /* Mantener el hover con el mismo color para consistencia */
    .nav-tabs .nav-link:hover {
        color: var(--color-nav-background);
    }
    
    .list-group-item {
        transition: all 0.2s;
    }
    
    .list-group-item:hover {
        z-index: 1;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .card {
        transition: all 0.3s;
    }
    
    .card:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    tr[data-bid-id] {
        transition: background-color 0.3s ease, opacity 0.3s ease, transform 0.3s ease;
    }
    tr.row-updated {
        background-color: rgba(13,110,253,0.06);
        transform: translateY(-4px);
    }
</style>
@endpush

@push('scripts')
<!-- Reemplazo del script FontAwesome con problemas CORS por una versión del CDN confiable -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mantener la pestaña activa después de recargar
        let activeTab = localStorage.getItem('dashboardActiveTab');
        
        if (activeTab) {
            const tab = document.querySelector(activeTab);
            if (tab) {
                const bsTab = new bootstrap.Tab(tab);
                bsTab.show();
            }
        }
        
        // Guardar la pestaña activa cuando cambie
        document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(e) {
                localStorage.setItem('dashboardActiveTab', '#' + e.target.id);
            });
        });
    });
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Función genérica para fetch y reemplazo de tbody. Intenta obtener JSON { html } o la página completa y extraer tbody.
    async function fetchTbody(url, selector) {
        try {
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const contentType = res.headers.get('content-type') || '';
            if (contentType.includes('application/json')) {
                const data = await res.json();
                if (data.html) {
                    document.querySelector(selector).innerHTML = data.html;
                    return;
                }
            }

            const text = await res.text();
            // Extraer el primer <tbody> del HTML retornado
            const tmp = document.createElement('div');
            tmp.innerHTML = text;
            const tbody = tmp.querySelector('tbody');
            if (tbody) {
                document.querySelector(selector).innerHTML = tbody.innerHTML;
            }
        } catch (e) {
            // Silenciar errores para no romper la UI; se puede loguear en consola
            console.error('Error actualizando tabla desde', url, e);
        }
    }

    // Actualizar ofertas enviadas y recibidas cada segundo
    setInterval(function() {
            // Enviadas: intenta '/bids' y actualiza filas en #bids-enviados
            updateRows('/bids', '#bids-enviados table tbody');
            // Recibidas: intenta '/bids/received' y actualiza filas en #bids-recibidos
            updateRows('/bids/received', '#bids-recibidos table tbody');
    }, 1000);
});
</script>
@endpush

    @push('scripts')
    <script>
    // Función genérica updateRows para dashboard (compartida si se necesita desde otras vistas)
    async function updateRows(url, selector) {
        try {
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const text = await res.text();
            const tmp = document.createElement('div');
            tmp.innerHTML = text;
            const newTbody = tmp.querySelector('tbody');
            if (!newTbody) return;

            const container = document.querySelector(selector);
            if (!container) return;

            const existingRows = {};
            container.querySelectorAll('tr[data-bid-id]').forEach(r => existingRows[r.dataset.bidId] = r);

            newTbody.querySelectorAll('tr[data-bid-id]').forEach(newRow => {
                const id = newRow.dataset.bidId;
                const existing = existingRows[id];
                if (existing) {
                    if (existing.innerHTML !== newRow.innerHTML) {
                        existing.innerHTML = newRow.innerHTML;
                        existing.classList.add('row-updated');
                        setTimeout(() => existing.classList.remove('row-updated'), 700);
                    }
                    delete existingRows[id];
                } else {
                    container.appendChild(newRow);
                    newRow.classList.add('row-updated');
                    setTimeout(() => newRow.classList.remove('row-updated'), 700);
                }
            });

            Object.values(existingRows).forEach(r => r.remove());
        } catch (e) { console.error('dashboard updateRows error', e); }
    }
    </script>
    @endpush
