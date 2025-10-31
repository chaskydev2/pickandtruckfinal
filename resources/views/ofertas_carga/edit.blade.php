@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/location-selector.css') }}">
@endpush

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

                        <!-- Selector de Origen -->
                        <div class="mb-4">
                            <label class="form-label">Origen <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="right" title="Seleccione el país, departamento/región y ciudad donde se recoge la carga"></i></label>
                            <input type="hidden" id="origen" name="origen" class="@error('origen') is-invalid @enderror" value="{{ $oferta->origen }}" required>
                            
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
                            @error('origen')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Selector de Destino -->
                        <div class="mb-4">
                            <label class="form-label">Destino <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="right" title="Seleccione el país, departamento/región y ciudad donde se entrega la carga"></i></label>
                            <input type="hidden" id="destino" name="destino" class="@error('destino') is-invalid @enderror" value="{{ $oferta->destino }}" required>
                            
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
                            @error('destino')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
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
                            <input type="number" step="1" min="0" max="999999" class="form-control @error('peso') is-invalid @enderror" id="peso" name="peso" value="{{ old('peso', $oferta->peso) }}" required style="appearance: textfield; -moz-appearance: textfield; -webkit-appearance: textfield;">
                            @error('peso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- ③ NUEVO: Unidades -->
                        <div class="mb-3">
                            <label for="unidades" class="form-label">Unidades</label>
                            <input type="number" min="1" class="form-control @error('unidades') is-invalid @enderror"
                                id="unidades" name="unidades" 
                                value="{{ old('unidades', $oferta->unidades ?? 1) }}"
                                readonly disabled
                                style="appearance: textfield; -moz-appearance: textfield; -webkit-appearance: textfield;">
                            @error('unidades')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Dejar vacío si no aplica.</div>
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
                    <input type="number" step="1" min="0" max="999999" class="form-control @error('presupuesto') is-invalid @enderror" 
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
<script>


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
<script>
// Truncar decimales y limitar valores en el formulario de edición
document.addEventListener('DOMContentLoaded', function() {
    const pesoInput = document.getElementById('peso');
    const presupuestoInput = document.getElementById('presupuesto');
    [pesoInput, presupuestoInput].forEach(el => {
        if (!el) return;
        el.addEventListener('input', function() {
            if (this.value.includes('.')) this.value = Math.floor(parseFloat(this.value));
            if (this.value !== '' && parseInt(this.value) > 999999) this.value = 999999;
        });
    });

    // Evitar pegar/escribir más de 6 dígitos en peso/presupuesto
    ['peso','presupuesto'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;

        el.addEventListener('paste', function(e) {
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const digits = paste.replace(/[^0-9]/g, '');
            if (digits.length > 6) {
                e.preventDefault();
                this.value = digits.slice(0,6);
            }
        });

        el.addEventListener('keydown', function(e) {
            const allowed = ['Backspace','Tab','ArrowLeft','ArrowRight','Delete','Home','End'];
            if (allowed.includes(e.key) || e.ctrlKey || e.metaKey) return;
            if (!/^[0-9]$/.test(e.key)) { e.preventDefault(); return; }

            const current = this.value || '';
            const selStart = this.selectionStart || 0;
            const selEnd = this.selectionEnd || 0;
            const resultingLength = current.length - (selEnd - selStart) + 1;
            if (resultingLength > 6) e.preventDefault();
        });
    });

    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            ['peso','presupuesto','unidades'].forEach(id => {
                const el = document.getElementById(id);
                if (el && el.value !== '') {
                    el.value = Math.floor(Number(el.value));
                }
            });
        });
    }
});
</script>
</script>
@endpush
