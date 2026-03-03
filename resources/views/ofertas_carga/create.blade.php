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
                    
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Error al crear la publicación</h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form action="{{ route('ofertas_carga.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="tipo_carga" class="form-label">Tipo de Carga <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="right" title="Seleccione el tipo de carga que necesita transportar"></i></label>
                            <select class="form-control @error('tipo_carga') is-invalid @enderror" id="tipo_carga" name="tipo_carga" required>
                                <option value="">Seleccione un tipo de carga</option>
                                @foreach($cargoTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('tipo_carga') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
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
                                    <select id="origen_pais" class="form-control" autocomplete="off">
                                        <option value="">País</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select id="origen_departamento" class="form-control" disabled autocomplete="off">
                                        <option value="">Departamento/Región</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select id="origen_ciudad" class="form-control" disabled autocomplete="off">
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
                                    <select id="destino_pais" class="form-control" autocomplete="off">
                                        <option value="">País</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select id="destino_departamento" class="form-control" disabled autocomplete="off">
                                        <option value="">Departamento/Región</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select id="destino_ciudad" class="form-control" disabled autocomplete="off">
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
                            <input type="datetime-local" class="form-control @error('fecha_inicio') is-invalid @enderror" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio') }}" min="{{ now()->format('Y-m-d\TH:i') }}" required>
                            @error('fecha_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="invalid-feedback" id="fecha-error">La fecha debe ser posterior a la fecha actual</div>
                        </div>

                        <div class="mb-4">
                            <label for="peso" class="form-label">Peso (kg) <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="right" title="Peso total de la carga en kilogramos"></i></label>
                            <input type="number" step="1" min="0" max="999999" class="form-control @error('peso') is-invalid @enderror" id="peso" name="peso" value="{{ old('peso') }}" required style="appearance: textfield; -moz-appearance: textfield; -webkit-appearance: textfield;">
                            @error('peso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- ① NUEVO: Unidades -->
                        <div class="mb-4">
                            <label for="unidades" class="form-label">
                                Unidades <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="right" title="Cantidad de bultos/unidades de la carga"></i>
                            </label>
                            <input type="number" min="1" class="form-control bg-light @error('unidades') is-invalid @enderror"
                                id="unidades" name="unidades" value="1" readonly
                                style="appearance: textfield; -moz-appearance: textfield; -webkit-appearance: textfield;">
                            @error('unidades')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Cantidad de bultos o unidades de la carga.</div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="tipo_despacho">Tipo de Despacho Aduanero (Opcional)</label>
                            <select id="tipo_despacho" name="tipo_despacho" class="form-control">
                                <option value="" selected>No especificado</option>
                                <option value="despacho_anticipado" {{ old('tipo_despacho') == 'despacho_anticipado' ? 'selected' : '' }}>Despacho Anticipado</option>
                                <option value="despacho_general" {{ old('tipo_despacho') == 'despacho_general' ? 'selected' : '' }}>Despacho General</option>
                                <option value="no_sabe_no_responde" {{ old('tipo_despacho') == 'no_sabe_no_responde' ? 'selected' : '' }}>No sabe/No responde</option>
                            </select>
                            <small class="form-text text-muted">Selecciona una opción si aplica a tu ruta.</small>
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
<script src="{{ asset('js/location-data.js') }}"></script>
<script src="{{ asset('js/location-selector.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Establecer fecha mínima para el campo fecha_inicio
    const elFecha = document.getElementById('fecha_inicio');
    if (elFecha) {
        elFecha.min = new Date().toISOString().split('T')[0];
        
        // Validación de fecha
        elFecha.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
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

    // Validación de peso - evitar negativos y decimales
    const pesoInput = document.getElementById('peso');
    if (pesoInput) {
        pesoInput.addEventListener('input', function() {
            if (this.value < 0) this.value = 0;
            if (this.value !== '' && parseInt(this.value) > 999999) this.value = 999999;
            if (this.value.includes('.')) this.value = Math.floor(parseFloat(this.value));
        });
    }

    // Validación de presupuesto - evitar negativos y decimales
    const presupuestoInput = document.getElementById('presupuesto');
    if (presupuestoInput) {
        presupuestoInput.addEventListener('input', function() {
            if (this.value < 0) this.value = 0;
            if (this.value !== '' && parseInt(this.value) > 999999) this.value = 999999;
            if (this.value.includes('.')) this.value = Math.floor(parseFloat(this.value));
        });
    }

    // Validación de unidades
    const unidadesInput = document.getElementById('unidades');
    if (unidadesInput) {
        unidadesInput.addEventListener('input', function() {
            if (this.value !== '' && this.value < 1) this.value = 1;
        });
    }
});
</script>
@endpush
