@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="display-5 fw-bold mb-4 text-primary">Publicar Carga</h1>
                    <form action="{{ route('ofertas_carga.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="tipo_carga" class="form-label">Tipo de Carga <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="right" title="Seleccione el tipo de carga que necesita transportar"></i></label>
                            <select class="form-control @error('tipo_carga') is-invalid @enderror" id="tipo_carga" name="tipo_carga" required>
                                <option value="">Seleccione un tipo de carga</option>
                                @foreach($cargoTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('tipo_carga')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="origen" class="form-label">Origen <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="right" title="Ciudad o lugar donde se recoge la carga"></i></label>
                            <input type="text" class="form-control @error('origen') is-invalid @enderror" id="origen" name="origen" autocomplete="off" required>
                            @error('origen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="destino" class="form-label">Destino <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="right" title="Ciudad o lugar donde se entrega la carga"></i></label>
                            <input type="text" class="form-control @error('destino') is-invalid @enderror" id="destino" name="destino" autocomplete="off" required>
                            @error('destino')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="fecha_inicio" class="form-label">Fecha de Inicio <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="right" title="Fecha en la que la carga estará lista para ser transportada"></i></label>
                            <input type="datetime-local" class="form-control @error('fecha_inicio') is-invalid @enderror" id="fecha_inicio" name="fecha_inicio" min="{{ now()->format('Y-m-d\TH:i') }}" required>
                            @error('fecha_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="invalid-feedback" id="fecha-error">La fecha debe ser posterior a la fecha actual</div>
                        </div>

                        <div class="mb-4">
                            <label for="peso" class="form-label">Peso (kg) <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="right" title="Peso total de la carga en kilogramos"></i></label>
                            <input type="number" step="0.01" class="form-control @error('peso') is-invalid @enderror" id="peso" name="peso" required style="appearance: textfield; -moz-appearance: textfield; -webkit-appearance: textfield;">
                            @error('peso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- ① NUEVO: Unidades -->
                        <div class="mb-4">
                            <label for="unidades" class="form-label">
                                Unidades <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="right" title="Cantidad de bultos/unidades de la carga"></i>
                            </label>
                            <input type="number" min="1" class="form-control @error('unidades') is-invalid @enderror"
                                id="unidades" name="unidades" value="{{ old('unidades') }}" 
                                style="appearance: textfield; -moz-appearance: textfield; -webkit-appearance: textfield;">
                            @error('unidades')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Dejar vacío si no aplica.</div>
                        </div>

                        <div class="mb-4">
                            <label for="descripcion" class="form-label">Comentarios (Opcional) <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="right" title="Añada detalles adicionales como: requiere refrigeración, es frágil, horarios especiales, etc."></i></label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3" placeholder="Ej: La carga requiere refrigeración, frágil, etc.">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Añade cualquier detalle o requisito especial para el transporte.</div>
                        </div>

                        <div class="mb-4">
                            <label for="presupuesto" class="form-label">Presupuesto (USD) <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="right" title="Monto máximo en dólares que está dispuesto a pagar por el servicio de transporte"></i></label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" class="form-control @error('presupuesto') is-invalid @enderror" 
                                       id="presupuesto" name="presupuesto" value="{{ old('presupuesto') }}" required style="appearance: textfield; -moz-appearance: textfield; -webkit-appearance: textfield;">
                                <span class="input-group-text">USD</span>
                            </div>
                            @error('presupuesto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Ingrese el presupuesto que está dispuesto a pagar por el transporte.</div>
                        </div>

                        <div class="mt-5 d-flex justify-content-between">
                            <a href="{{ route('ofertas_carga.index') }}" class="btn-pickn">
                                <i class="fas fa-arrow-left me-1"></i> Volver
                            </a>
                            <button type="submit" class="btn-pickn">
                                <i class="fas fa-save me-1"></i> Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')

<script>
function initAutocomplete() {
    const origenInput  = document.getElementById('origen');
    const destinoInput = document.getElementById('destino');

    if (origenInput && window.google?.maps?.places) {
        new google.maps.places.Autocomplete(origenInput, { types: ['geocode'] });
    }
    if (destinoInput && window.google?.maps?.places) {
        new google.maps.places.Autocomplete(destinoInput, { types: ['geocode'] });
    }
}

// El resto de tu lógica va fuera de initAutocomplete
document.addEventListener('DOMContentLoaded', function () {
    // Inicializar tooltips de Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Establecer fecha mínima
    const elFecha = document.getElementById('fecha_inicio');
    if (elFecha) {
        elFecha.min = new Date().toISOString().split('T')[0];
        elFecha.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const today = new Date(); today.setHours(0,0,0,0);
            const error = document.getElementById('fecha-error');
            if (selectedDate < today) {
                this.classList.add('is-invalid');
                if (error) error.style.display = 'block';
            } else {
                this.classList.remove('is-invalid');
                if (error) error.style.display = 'none';
            }
        });
    }

    // Validación de peso
    const pesoInput = document.getElementById('peso');
    if (pesoInput) {
        pesoInput.addEventListener('input', function() {
            if (this.value < 0) this.value = 0;
        });
    }

    // Validación de unidades
    const unidadesInput = document.getElementById('unidades');
    if (unidadesInput) {
        unidadesInput.addEventListener('input', function() {
            if (this.value !== '' && this.value < 1) this.value = 1;
        });
    }

    // Validación de dimensiones
    ['largo','ancho','alto'].forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', function() {
                if (this.value < 0) this.value = 0;
            });
        }
    });
});
</script>
@endpush
