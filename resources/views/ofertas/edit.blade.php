@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/location-selector.css') }}">
@endpush

@section('content')
<div class="container mt-5">
    <h1 class="mb-4 fw-bold">Editar Oferta</h1>
    <form action="{{ route('ofertas.update', $oferta->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group mb-3">
            <label for="tipo_camion">Tipo de Camión</label>
            <select id="tipo_camion" name="tipo_camion" class="form-control" required>
                <option value="">Seleccione un tipo de camión</option>
                @foreach($truckTypes as $truckType)
                    <option value="{{ $truckType->id }}" {{ $oferta->tipo_camion == $truckType->id ? 'selected' : '' }}>{{ $truckType->name }}</option>
                @endforeach
            </select>
        </div>
        <!-- Selector de Origen -->
        <div class="form-group mb-3">
            <label>Origen <i class="fas fa-question-circle text-muted fst-italic" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-html="true" title="<em>Seleccione el país, departamento/región y ciudad de origen</em>"></i></label>
            <input type="hidden" id="origen" name="origen" value="{{ $oferta->origen }}" required>
            
            <div class="row g-2">
                <div class="col-md-4">
                    <select id="origen_pais" class="form-control">
                        <option value="">País</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select id="origen_departamento" class="form-control" disabled>
                        <option value="">Departamento/Región</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select id="origen_ciudad" class="form-control" disabled>
                        <option value="">Ciudad</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Selector de Destino -->
        <div class="form-group mb-3">
            <label>Destino <i class="fas fa-question-circle text-muted fst-italic" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-html="true" title="<em>Seleccione el país, departamento/región y ciudad de destino</em>"></i></label>
            <input type="hidden" id="destino" name="destino" value="{{ $oferta->destino }}" required>
            
            <div class="row g-2">
                <div class="col-md-4">
                    <select id="destino_pais" class="form-control">
                        <option value="">País</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select id="destino_departamento" class="form-control" disabled>
                        <option value="">Departamento/Región</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select id="destino_ciudad" class="form-control" disabled>
                        <option value="">Ciudad</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="fecha_inicio">Fecha de Inicio</label>
            <input type="datetime-local" id="fecha_inicio" name="fecha_inicio" class="form-control" value="{{ $oferta->fecha_inicio->format('Y-m-d\TH:i') }}" required>
        </div>
        <div class="form-group mb-3">
            <label for="capacidad">Capacidad</label>
            <input type="number" id="capacidad" name="capacidad" class="form-control" value="{{ $oferta->capacidad }}" required>
            <small class="form-text text-muted">Capacidad en kg</small>
        </div>

        <!-- ② NUEVO: Unidades -->
        <div class="form-group mb-3">
            <label for="unidades">Unidades</label>
            <input type="number" min="1" id="unidades" name="unidades"
                class="form-control @error('unidades') is-invalid @enderror"
                value="{{ old('unidades', $oferta->unidades ?? 1) }}"
                readonly disabled>
            @error('unidades')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">Dejar vacío si no aplica.</small>
        </div>

        <div class="form-group mb-3">
            <label for="tipo_despacho">Tipo de Despacho Aduanero (Opcional)</label>
            <select id="tipo_despacho" name="tipo_despacho" class="form-control">
                <option value="">No especificado</option>
                <option value="despacho_anticipado" {{ old('tipo_despacho', $oferta->tipo_despacho) == 'despacho_anticipado' ? 'selected' : '' }}>Despacho Anticipado</option>
                <option value="despacho_general" {{ old('tipo_despacho', $oferta->tipo_despacho) == 'despacho_general' ? 'selected' : '' }}>Despacho General</option>
                <option value="no_sabe_no_responde" {{ old('tipo_despacho', $oferta->tipo_despacho) == 'no_sabe_no_responde' ? 'selected' : '' }}>No sabe/No responde</option>
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="descripcion">Comentarios (Opcional)</label>
            <textarea id="descripcion" name="descripcion" class="form-control" rows="3">{{ old('descripcion', $oferta->descripcion) }}</textarea>
        </div>

        <div class="form-group mb-3">
            <label for="precio_referencial">Precio Referencial</label>
            <input type="number" id="precio_referencial" step="0.01" name="precio_referencial" class="form-control" value="{{ $oferta->precio_referencial }}" required>
            <small class="form-text text-muted">Precio en dólares</small>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/location-data.js') }}"></script>
<script src="{{ asset('js/location-selector.js') }}"></script>
<script>
// Inicializar selectores con valores existentes
document.addEventListener('DOMContentLoaded', function() {
    const origenValue = "{{ $oferta->origen }}";
    const destinoValue = "{{ $oferta->destino }}";
    
    // Función para parsear y establecer valores
    function setLocationValue(prefix, value) {
        if (!value) return;
        
        // Parsear el valor: "Ciudad, Departamento, País"
        const parts = value.split(',').map(p => p.trim());
        if (parts.length >= 3) {
            const city = parts[0];
            const state = parts[1];
            const country = parts[2];
            
            // Establecer país
            const countrySelect = document.getElementById(`${prefix}_pais`);
            if (countrySelect) {
                countrySelect.value = country;
                countrySelect.dispatchEvent(new Event('change'));
                
                // Esperar a que se pueble el select de departamento
                setTimeout(() => {
                    const stateSelect = document.getElementById(`${prefix}_departamento`);
                    if (stateSelect) {
                        stateSelect.value = state;
                        stateSelect.dispatchEvent(new Event('change'));
                        
                        // Esperar a que se pueble el select de ciudad
                        setTimeout(() => {
                            const citySelect = document.getElementById(`${prefix}_ciudad`);
                            if (citySelect) {
                                citySelect.value = city;
                                citySelect.dispatchEvent(new Event('change'));
                            }
                        }, 100);
                    }
                }, 100);
            }
        }
    }
    
    // Establecer valores después de que los selectores estén inicializados
    setTimeout(() => {
        setLocationValue('origen', origenValue);
        setLocationValue('destino', destinoValue);
    }, 200);
});
</script>
@endpush
