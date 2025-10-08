@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body p-3">
            <nav aria-label="breadcrumb" class="bg-transparent m-0">
                <ol class="breadcrumb p-0 m-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Ofertas Recibidas</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-inbox me-2"></i> Ofertas Recibidas en Mis Publicaciones
                    </h5>
                    <div>
                        <a href="{{ route('ofertas_carga.create') }}" class="btn btn-primary btn-sm me-2">
                            <i class="fas fa-plus me-1"></i> Nueva Oferta de Carga
                        </a>
                        <a href="{{ route('ofertas.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus me-1"></i> Nueva Oferta de Ruta
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if($bidsRecibidos->isEmpty())
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
                                        <th>Monto</th>
                                        <th>Ofertante</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bidsRecibidos as $bid)
                                        <tr>
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
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $bidsRecibidos->links() }}
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
</style>
@endpush
