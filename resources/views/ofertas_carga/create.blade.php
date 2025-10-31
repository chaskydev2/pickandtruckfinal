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

                        <!-- Selector de Origen -->
                        <div class="mb-4">
                            <label class="form-label">Origen <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="right" title="Seleccione el país, departamento/región y ciudad donde se recoge la carga"></i></label>
                            <input type="hidden" id="origen" name="origen" class="@error('origen') is-invalid @enderror" required>
                            
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
                            <input type="hidden" id="destino" name="destino" class="@error('destino') is-invalid @enderror" required>
                            
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
                            <input type="number" step="1" min="0" max="999999" class="form-control @error('peso') is-invalid @enderror" id="peso" name="peso" required style="appearance: textfield; -moz-appearance: textfield; -webkit-appearance: textfield;">
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
                                id="unidades" name="unidades" value="1" readonly disabled
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
                    <input type="number" step="1" min="0" max="999999" class="form-control @error('presupuesto') is-invalid @enderror" 
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
                            <button type="submit" class="btn btn-success">
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
<script src="{{ asset('js/location-data.js') }}"></script>
<script src="{{ asset('js/location-selector.js') }}"></script>
<script>
// El resto de tu lógica
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
            if (this.value !== '' && parseInt(this.value) > 999999) this.value = 999999;
            // eliminar decimales si los pega
            if (this.value.includes('.')) this.value = Math.floor(parseFloat(this.value));
        });
    }

    // Evitar que se escriban más de 6 dígitos (999999)
    ['peso','presupuesto'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;

        // Evitar pegar texto con más dígitos o con decimales
        el.addEventListener('paste', function(e) {
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const digits = paste.replace(/[^0-9]/g, '');
            if (digits.length > 6) {
                e.preventDefault();
                this.value = digits.slice(0,6);
            }
        });

        // Evitar teclear más de 6 dígitos
        el.addEventListener('keydown', function(e) {
            // permitir teclas de control, navegación y edición
            const allowed = ['Backspace','Tab','ArrowLeft','ArrowRight','Delete','Home','End'];
            if (allowed.includes(e.key) || e.ctrlKey || e.metaKey) return;

            // permitir sólo números
            if (!/^[0-9]$/.test(e.key)) {
                e.preventDefault();
                return;
            }

            // Bloquear si ya hay 6 dígitos seleccionando no más que la selección
            const selection = window.getSelection();
            const current = this.value || '';
            const selStart = this.selectionStart || 0;
            const selEnd = this.selectionEnd || 0;
            const resultingLength = current.length - (selEnd - selStart) + 1;
            if (resultingLength > 6) e.preventDefault();
        });
    });

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
    
    // Antes de enviar el formulario, truncar decimales y forzar enteros
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
@endpush
