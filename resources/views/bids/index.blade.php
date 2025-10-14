@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body p-3">
            <nav aria-label="breadcrumb" class="bg-transparent m-0">
                <ol class="breadcrumb p-0 m-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Mis Ofertas Enviadas</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-paper-plane me-2"></i> Mis Ofertas Enviadas
                    </h5>
                    <div class="d-flex align-items-center">
                        <a href="{{ route('ofertas_carga.index') }}" class="btn btn-primary btn-sm me-2 d-flex align-items-center justify-content-center" style="min-width: 150px; height: 32px;">
                            <i class="fas fa-box me-1"></i> Ver Ofertas de Carga
                        </a>
                        <a href="{{ route('ofertas.index') }}" class="btn btn-success btn-sm d-flex align-items-center justify-content-center" style="min-width: 150px; height: 32px;">
                            <i class="fas fa-truck me-1"></i> Ver Ofertas de Ruta
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
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
                                        <th>ID</th>
                                        <th>Tipo</th>
                                        <th>Ruta</th>
                                        <th>Monto</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($misBids as $bid)
                                        @if($bid->bideable)
                                            <tr data-bid-id="{{ $bid->id }}">
                                                <td class="text-muted">
                                                    #{{ $bid->id }}
                                                </td>
                                                <td>
                                                    <span class="badge rounded-pill bg-{{ $bid->bideable_type == 'App\Models\OfertaCarga' ? 'primary' : 'success' }}">
                                                        <i class="fas fa-{{ $bid->bideable_type == 'App\Models\OfertaCarga' ? 'box' : 'truck' }}"></i>
                                                        {{ $bid->bideable_type == 'App\Models\OfertaCarga' ? 'Carga' : 'Ruta' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $bid->bideable->origen }} → {{ $bid->bideable->destino }}
                                                </td>
                                                <td class="fw-bold">${{ number_format($bid->monto, 2) }}</td>
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
                                                    @elseif($bid->estado === 'pendiente')
                                                        <a href="{{ route('bids.edit', $bid) }}" class="btn btn-sm btn-warning">
                                                            <i class="fas fa-edit me-1"></i> Editar
                                                        </a>
                                                    @endif
                                                    
                                                    <a href="{{ route($bid->bideable_type == 'App\Models\OfertaCarga' ? 'ofertas_carga.show' : 'ofertas.show', $bid->bideable) }}" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-eye me-1"></i> Ver Publicación
                                                    </a>
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">
                                                    <em>Oferta ya no disponible</em>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $misBids->links() }}
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
    .table th, .table td {
        vertical-align: middle;
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }
        /* transiciones para cambios suaves */
        tr[data-bid-id] {
            transition: background-color 0.3s ease, opacity 0.3s ease, transform 0.3s ease;
        }
        tr.row-updated {
            background-color: rgba(13,110,253,0.08);
            transform: translateY(-4px);
        }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Actualiza filas individualmente comparando por data-bid-id
    async function updateRows(url, containerSelector) {
        try {
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const text = await res.text();
            const tmp = document.createElement('div');
            tmp.innerHTML = text;
            const newTbody = tmp.querySelector('tbody');
            if (!newTbody) return;

            const container = document.querySelector(containerSelector);
            if (!container) return;

            // Map existing rows
            const existingRows = {};
            container.querySelectorAll('tr[data-bid-id]').forEach(r => existingRows[r.dataset.bidId] = r);

            // For each new row, update or insert
            newTbody.querySelectorAll('tr[data-bid-id]').forEach(newRow => {
                const id = newRow.dataset.bidId;
                const existing = existingRows[id];
                if (existing) {
                    // If HTML changed, replace innerHTML and animate
                    if (existing.innerHTML !== newRow.innerHTML) {
                        existing.innerHTML = newRow.innerHTML;
                        existing.classList.add('row-updated');
                        setTimeout(() => existing.classList.remove('row-updated'), 700);
                    }
                    delete existingRows[id];
                } else {
                    // Insert new row at the end
                    container.appendChild(newRow);
                    newRow.classList.add('row-updated');
                    setTimeout(() => newRow.classList.remove('row-updated'), 700);
                }
            });

            // Remove rows that no longer exist
            Object.values(existingRows).forEach(r => r.remove());

        } catch (e) {
            console.error('updateRows error', e);
        }
    }

    setInterval(() => updateRows('/bids', '.card .table-responsive tbody'), 1000);
});
</script>
@endpush
