@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="display-5 fw-bold mb-4 text-primary">Editar Oferta de Carga</h1>

                    <form action="{{ route('ofertas_carga.update', $oferta) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="tipo_carga" class="form-label">Tipo de Carga</label>
                            <select class="form-control @error('tipo_carga') is-invalid @enderror" id="tipo_carga" name="tipo_carga" required>
                                <option value="">Seleccione un tipo de carga</option>
                                @foreach($cargoTypes as $type)
                                    <option value="{{ $type->id }}" {{ $oferta->tipo_carga == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_carga')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="origen" class="form-label">Origen</label>
                            <input type="text" class="form-control @error('origen') is-invalid @enderror" id="origen" name="origen" value="{{ $oferta->origen }}" required>
                            @error('origen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="destino" class="form-label">Destino</label>
                            <input type="text" class="form-control @error('destino') is-invalid @enderror" id="destino" name="destino" value="{{ $oferta->destino }}" required>
                            @error('destino')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                            <input type="datetime-local" class="form-control @error('fecha_inicio') is-invalid @enderror" id="fecha_inicio" name="fecha_inicio" value="{{ $oferta->fecha_inicio->format('Y-m-d\TH:i') }}" required>
                            @error('fecha_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="peso" class="form-label">Peso (kg)</label>
                            <input type="number" step="0.01" class="form-control @error('peso') is-invalid @enderror" id="peso" name="peso" value="{{ old('peso', $oferta->peso) }}" required style="appearance: textfield; -moz-appearance: textfield; -webkit-appearance: textfield;">
                            @error('peso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- ③ NUEVO: Unidades -->
                        <div class="mb-3">
                            <label for="unidades" class="form-label">Unidades</label>
                            <input type="number" min="1" class="form-control @error('unidades') is-invalid @enderror"
                                id="unidades" name="unidades" 
                                value="{{ old('unidades', $oferta->unidades) }}"
                                style="appearance: textfield; -moz-appearance: textfield; -webkit-appearance: textfield;">
                            @error('unidades')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Dejar vacío si no aplica.</div>
                        </div>

                        <!-- ④ NUEVO: Es contenedor -->
                        <div class="mb-3 form-check">
                            <input class="form-check-input @error('es_contenedor') is-invalid @enderror" type="checkbox" value="1"
                                id="es_contenedor" name="es_contenedor" 
                                {{ old('es_contenedor', $oferta->es_contenedor) ? 'checked' : '' }}>
                            <label class="form-check-label" for="es_contenedor">¿Es contenedor?</label>
                            @error('es_contenedor')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Comentarios (Opcional)</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3" placeholder="Ej: La carga requiere refrigeración, frágil, etc.">{{ old('descripcion', $oferta->descripcion) }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="presupuesto" class="form-label">Presupuesto (USD)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" class="form-control @error('presupuesto') is-invalid @enderror" 
                                       id="presupuesto" name="presupuesto" value="{{ old('presupuesto', $oferta->presupuesto) }}" required style="appearance: textfield; -moz-appearance: textfield; -webkit-appearance: textfield;">
                                <span class="input-group-text">USD</span>
                            </div>
                            @error('presupuesto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-5 d-flex justify-content-between">
                            <a href="{{ route('ofertas_carga.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-success">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDg5hWgPIKdJBMjLodd5Ttu-f6JRSsw8fY&libraries=places"></script>
<script>
function initAutocomplete() {
    // Inicializar autocompletado para origen
    const origenInput = document.getElementById('origen');
    const origenAutocomplete = new google.maps.places.Autocomplete(origenInput, {
        types: ['geocode'],
        fields: ['formatted_address']
    });

    // Inicializar autocompletado para destino
    const destinoInput = document.getElementById('destino');
    const destinoAutocomplete = new google.maps.places.Autocomplete(destinoInput, {
        types: ['geocode'],
        fields: ['formatted_address']
    });

    // Validación de unidades (no negativas)
    const unidadesInput = document.getElementById('unidades');
    if (unidadesInput) {
        unidadesInput.addEventListener('input', function() {
            if (this.value !== '' && this.value < 1) this.value = 1;
        });
    }

    // Manejar la selección de lugares
    origenAutocomplete.addListener('place_changed', function() {
        const place = origenAutocomplete.getPlace();
        if (place.formatted_address) {
            origenInput.value = place.formatted_address;
        }
    });

    destinoAutocomplete.addListener('place_changed', function() {
        const place = destinoAutocomplete.getPlace();
        if (place.formatted_address) {
            destinoInput.value = place.formatted_address;
        }
    });
}

// Iniciar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initAutocomplete);

// Prevenir el cambio de valor con scroll en campos numéricos
document.querySelectorAll('input[type=number]').forEach(input => {
    input.addEventListener('wheel', function(e) {
        e.preventDefault();
    });
    
    // También prevenir el aumento/decremento con teclas de flecha arriba/abajo
    input.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
            e.preventDefault();
        }
    });
});
</script>
@endpush
