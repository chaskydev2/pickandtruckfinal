@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="h5 mb-0">Perfil</h2>
                </div>
                <div class="card-body">
                    <!-- Información del usuario -->
                    <div class="text-center mb-4">
                        <div class="avatar avatar-xl mb-3">
                            <!-- Avatar del usuario -->
                            <span class="avatar-initials rounded-circle bg-primary text-white fs-2">
                                {{ substr($user->name, 0, 1) }}
                            </span>
                        </div>
                        <h3 class="h5 mb-1">{{ $user->name }}</h3>
                        <p class="text-muted mb-0">{{ $user->email }}</p>
                        
                        @if($user->empresa)
                            <hr class="my-3">
                            <h4 class="h6 mb-2">Empresa</h4>
                            @if($user->empresa->logo)
                                <div class="mb-2">
                                    <img src="{{ $user->empresa->logo }}" alt="{{ $user->empresa->nombre }}" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                            <p class="mb-1 fw-bold">{{ $user->empresa->nombre }}</p>
                            @if($user->empresa->descripcion)
                                <p class="small text-muted">{{ $user->empresa->descripcion }}</p>
                            @endif
                        @endif
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="d-grid gap-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                            <i class="fas fa-user-edit me-1"></i> Editar Perfil
                        </a>
                        <a href="{{ route('empresas.edit') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-building me-1"></i> Gestionar Empresa
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Información del Usuario -->
            <div class="card">
                <div class="card-header">
                    <h3>Perfil de Usuario</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <h4 class="mb-0">{{ $user->name }}</h4>
                        @if($user->verified)
                            <span class="badge bg-success ms-2">Verificado</span>
                        @else
                            <span class="badge bg-warning ms-2">No Verificado</span>
                        @endif
                    </div>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Miembro desde:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">Editar Perfil</a>
                </div>
            </div>
        </div>
        
        <!-- Pestañas para Ofertas y Bids -->
        <div class="col-md-8">
            <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="ofertas-tab" data-bs-toggle="tab" href="#ofertas" role="tab">
                        Mis Ofertas Publicadas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="bids-tab" data-bs-toggle="tab" href="#bids" role="tab">
                        Mis Pujas
                    </a>
                </li>
            </ul>

            <div class="tab-content" id="profileTabsContent">
                <!-- Mis Ofertas -->
                <div class="tab-pane fade show active" id="ofertas" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <h5>Ofertas de Carga</h5>
                            @if($ofertasCarga->count() > 0)
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Origen</th>
                                                <th>Destino</th>
                                                <th>Fecha</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($ofertasCarga as $oferta)
                                                <tr>
                                                    <td>{{ $oferta->cargoType->name }}</td>
                                                    <td>{{ $oferta->origen }}</td>
                                                    <td>{{ $oferta->destino }}</td>
                                                    <td>{{ $oferta->fecha_inicio }}</td>
                                                    <td>{{ $oferta->bids->count() }} pujas</td>
                                                    <td>
                                                        <a href="{{ route('ofertas_carga.show', $oferta) }}" class="btn btn-sm btn-info">Ver</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No has publicado ofertas de carga.</p>
                            @endif

                            <h5 class="mt-4">Ofertas de Ruta</h5>
                            @if($ofertasRuta->count() > 0)
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Origen</th>
                                                <th>Destino</th>
                                                <th>Fecha</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($ofertasRuta as $oferta)
                                                <tr>
                                                    <td>{{ $oferta->truckType->name }}</td>
                                                    <td>{{ $oferta->origen }}</td>
                                                    <td>{{ $oferta->destino }}</td>
                                                    <td>{{ $oferta->fecha_inicio }}</td>
                                                    <td>{{ $oferta->bids->count() }} pujas</td>
                                                    <td>
                                                        <a href="{{ route('ofertas.show', $oferta) }}" class="btn btn-sm btn-info">Ver</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No has publicado ofertas de ruta.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Mis Pujas -->
                <div class="tab-pane fade" id="bids" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            @if($bids->count() > 0)
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Monto</th>
                                                <th>Fecha</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($bids as $bid)
                                                <tr>
                                                    <td>{{ get_class($bid->bideable) === 'App\Models\OfertaCarga' ? 'Carga' : 'Ruta' }}</td>
                                                    <td>${{ number_format($bid->monto, 2) }}</td>
                                                    <td>{{ $bid->fecha_hora->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $bid->estado === 'pendiente' ? 'warning' : ($bid->estado === 'aceptada' ? 'success' : 'danger') }}">
                                                            {{ ucfirst($bid->estado) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('bids.edit', $bid) }}" class="btn btn-sm btn-warning">Editar</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No has realizado ninguna puja.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var triggerTabList = [].slice.call(document.querySelectorAll('#profileTabs a'));
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl);
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            tabTrigger.show();
        });
    });
});
</script>
@endpush
