@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body p-3">
            <nav aria-label="breadcrumb" class="bg-transparent m-0">
                <ol class="breadcrumb p-0 m-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                    <li class="breadcrumb-item">
                        <a href="{{ $type === 'carga' ? route('ofertas_carga.index') : route('ofertas.index') }}">
                            {{ $type === 'carga' ? 'Publicaciones de Carga' : 'Publicaciones de Ruta' }}
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ $type === 'carga' ? route('ofertas_carga.show', $oferta) : route('ofertas.show', $oferta) }}">
                            Detalles
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Hacer Oferta</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Mostrar errores si existen -->
    @if ($errors->any())
    <div class="alert alert-danger mb-4">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Mostrar mensajes de error del controlador -->
    @if(session('error'))
    <div class="alert alert-danger mb-4">
        {{ session('error') }}
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h2 class="h4 mb-0">Hacer una Oferta</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i> Información de la Oferta
                        </h5>
                        <p class="mb-0">
                            <strong>Tipo:</strong> {{ $type == 'carga' ? 'Oferta de Carga' : 'Oferta de Ruta' }}<br>
                            <strong>Origen:</strong> {{ $oferta->origen }}<br>
                            <strong>Destino:</strong> {{ $oferta->destino }}<br>
                            <strong>Fecha propuesta:</strong> {{ $oferta->fecha_inicio->format('d/m/Y') }}<br>
                            @if($type == 'carga')
                                <strong>Peso:</strong> {{ number_format($oferta->peso, 2) }} kg<br>
                                <strong>Presupuesto Referencial:</strong> ${{ number_format($oferta->presupuesto, 2) }}
                            @else
                                <strong>Capacidad:</strong> {{ number_format($oferta->capacidad, 2) }} kg<br>
                                <strong>Precio Referencial:</strong> ${{ number_format($oferta->precio_referencial, 2) }}
                            @endif
                        </p>
                    </div>

                    <form action="{{ route('bids.store') }}" method="POST" id="bidForm">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">
                        <input type="hidden" name="id" value="{{ $oferta->id }}">

                        <div class="mb-3">
                            <label for="monto" class="form-label">Monto de su Oferta</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control @error('monto') is-invalid @enderror" 
                                    id="monto" name="monto" required
                                    value="{{ old('monto', $type == 'carga' ? $oferta->presupuesto : $oferta->precio_referencial) }}">
                                @error('monto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">
                                Precio referencial: ${{ $type == 'carga' ? number_format($oferta->presupuesto, 2) : number_format($oferta->precio_referencial, 2) }}
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_servicio" class="form-label">Fecha Propuesta</label>
                            <input type="date" class="form-control @error('fecha_servicio') is-invalid @enderror" 
                                   id="fecha_servicio" name="fecha_servicio" required
                                   value="{{ old('fecha_servicio', $oferta->fecha_inicio->format('Y-m-d')) }}">
                            @error('fecha_servicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Fecha propuesta originalmente: {{ $oferta->fecha_inicio->format('d/m/Y') }}
                            </small>
                        </div>

                        <div class="mb-4">
                            <label for="mensaje" class="form-label">Comentario (opcional)</label>
                            <textarea class="form-control @error('mensaje') is-invalid @enderror" id="mensaje" name="mensaje" rows="3">{{ old('mensaje') }}</textarea>
                            @error('mensaje')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Puede incluir detalles adicionales sobre su oferta.</small>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ $type == 'carga' ? route('ofertas_carga.show', $oferta) : route('ofertas.show', $oferta) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </a>
                            <button type="submit" id="submitBtn" class="btn btn-success">
                                <i class="fas fa-paper-plane me-1"></i> Enviar Oferta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prevenir doble envío del formulario
    const form = document.getElementById('bidForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (form) {
        form.addEventListener('submit', function() {
            // Deshabilitar el botón al enviar para evitar múltiples clics
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Enviando...';
            }
        });
    }
});
</script>
@endpush
@endsection
