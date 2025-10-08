@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Switch Container -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="switch-container">
            <div class="switch">
                <input type="checkbox" id="ofertaSwitch" class="d-none">
                <label for="ofertaSwitch" class="switch-label">
                    <span class="switch-text left active">Ofertas de Carga</span>
                    <span class="switch-button"></span>
                    <span class="switch-text right">Ofertas de Ruta</span>
                </label>
            </div>
        </div>
        <div id="dynamicCTA">
            @auth
                <a href="{{ route('ofertas_carga.create') }}" class="btn btn-primary" id="cargaCTA">Nueva Publicación de Carga</a>
                <a href="{{ route('ofertas.create') }}" class="btn btn-primary d-none" id="rutaCTA">Nueva Pubicacion de Ruta</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">Publicar carga</a>
            @endauth
        </div>
    </div>

    <!-- Ofertas de Carga Table -->
    <div id="ofertasCargaContent">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tipo de Carga</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Fecha</th>
                        <th>Peso</th>
                        <th>Presupuesto</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ofertasCarga as $oferta)
                        <tr>
                            <td data-label="Tipo">{{ $oferta->cargoType?->name ?? 'N/A' }}</td>
                            <td data-label="Origen">{{ $oferta->origen }}</td>
                            <td data-label="Destino">{{ $oferta->destino }}</td>
                            <td data-label="Fecha">{{ $oferta->fecha_inicio }}</td>
                            <td data-label="Peso">{{ $oferta->peso }} kg</td>
                            <td data-label="Presupuesto">${{ number_format($oferta->presupuesto, 2) }}</td>
                            <td data-label="Acciones">
                                <div class="btn-group-responsive">
                                    <a href="{{ route('ofertas_carga.show', $oferta) }}" class="btn btn-info btn-sm">Ver</a>
                                    @auth
                                        @if(Auth::id() !== $oferta->user_id)
                                            @php
                                                $existingBid = $oferta->bids?->where('user_id', Auth::id())->first();
                                            @endphp
                                            
                                            @if($existingBid)
                                                <a href="{{ route('bids.edit', ['bid' => $existingBid->id]) }}" 
                                                   class="btn btn-warning btn-sm">Editar mi oferta</a>
                                            @else
                                                <a href="{{ route('bids.create', ['type' => 'carga', 'id' => $oferta->id]) }}" 
                                                   class="btn btn-success btn-sm">Bidear</a>
                                            @endif
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-success btn-sm">Bidear</a>
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Ofertas de Ruta Table -->
    <div id="ofertasRutaContent" style="display: none;">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tipo de Camión</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Fecha</th>
                        <th>Capacidad</th>
                        <th>Precio Ref.</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ofertasRuta as $oferta)
                        <tr>
                            <td>{{ $oferta->truckType?->name ?? 'N/A' }}</td>
                            <td>{{ $oferta->origen }}</td>
                            <td>{{ $oferta->destino }}</td>
                            <td>{{ $oferta->fecha_inicio }}</td>
                            <td>{{ $oferta->capacidad }} kg</td>
                            <td>${{ number_format($oferta->precio_referencial, 2) }}</td>
                            <td>
                                <a href="{{ route('ofertas.show', $oferta) }}" class="btn btn-info btn-sm">Ver</a>
                                @auth
                                    @if(Auth::id() !== $oferta->user_id)
                                        @php
                                            $existingBid = $oferta->bids?->where('user_id', Auth::id())->first();
                                        @endphp
                                        
                                        @if($existingBid)
                                            <a href="{{ route('bids.edit', ['bid' => $existingBid->id]) }}" 
                                               class="btn btn-warning btn-sm">Editar mi oferta</a>
                                        @else
                                            <a href="{{ route('bids.create', ['type' => 'ruta', 'id' => $oferta->id]) }}" 
                                               class="btn btn-success btn-sm">Bidear</a>
                                        @endif
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-success btn-sm">Ingresar oferta ruta</a>
                                @endauth
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Bienvenida para usuarios verificados -->
@if($showWelcomeModal)
<div class="modal fade" id="welcomeModal" tabindex="-1" aria-labelledby="welcomeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="welcomeModalLabel">¡BIENVENIDO!</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                <h4>Sus documentos han sido aprobados</h4>
                <p class="mb-0">Ahora puede acceder a todas las funcionalidades de la plataforma.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">Navegar</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const switchInput = document.getElementById('ofertaSwitch');
    const cargaContent = document.getElementById('ofertasCargaContent');
    const rutaContent = document.getElementById('ofertasRutaContent');
    const cargaCTA = document.getElementById('cargaCTA');
    const rutaCTA = document.getElementById('rutaCTA');

    switchInput.addEventListener('change', function() {
        if (this.checked) {
            cargaContent.style.display = 'none';
            rutaContent.style.display = 'block';
            cargaCTA.classList.add('d-none');
            rutaCTA.classList.remove('d-none');
            document.querySelector('.switch-text.left').classList.remove('active');
            document.querySelector('.switch-text.right').classList.add('active');
        } else {
            cargaContent.style.display = 'block';
            rutaContent.style.display = 'none';
            cargaCTA.classList.remove('d-none');
            rutaCTA.classList.add('d-none');
            document.querySelector('.switch-text.left').classList.add('active');
            document.querySelector('.switch-text.right').classList.remove('active');
        }
    });

    // Script para mostrar automáticamente el modal de bienvenida
    @if($showWelcomeModal)
    var welcomeModal = new bootstrap.Modal(document.getElementById('welcomeModal'));
    welcomeModal.show();
    @endif
});
</script>

<style>
.switch-container {
    display: flex;
    justify-content: center;
}

.switch {
    position: relative;
    display: inline-block;
    min-width: 300px;
}

.switch-label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 5px 10px;
    border-radius: 30px;
    background: #f0f0f0;
    cursor: pointer;
    position: relative;
}

.switch-button {
    position: absolute;
    width: 50%;
    height: 100%;
    background: #0d6efd;
    border-radius: 30px;
    top: 0;
    left: 0;
    transition: 0.3s ease;
}

#ofertaSwitch:checked + .switch-label .switch-button {
    left: 50%;
}

.switch-text {
    color: #666;
    font-weight: 500;
    z-index: 1;
    transition: 0.3s ease;
    padding: 5px 10px;
}

.switch-text.active {
    color: white;
}

.switch-text.left {
    margin-right: auto;
}

.switch-text.right {
    margin-left: auto;
}
</style>
@endpush
@endsection
