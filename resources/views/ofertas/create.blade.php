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
                    <h1 class="display-5 fw-bold mb-4 text-primary">Publicar Ruta</h1>

                    <form action="{{ route('ofertas.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="tipo_camion">Tipo de Camión <i class="fas fa-question-circle text-muted fst-italic" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-html="true" title="<em>Seleccione el tipo de camión que ofrece para el transporte</em>"></i></label>
                            <select id="tipo_camion" name="tipo_camion" class="form-control" required>
                                <option value="">Seleccione un tipo de camión</option>
                                @foreach($truckTypes as $truckType)
                                    <option value="{{ $truckType->id }}">{{ $truckType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Selector de Origen -->
                        <div class="form-group mb-3">
                            <label>Origen <i class="fas fa-question-circle text-muted fst-italic" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-html="true" title="<em>Seleccione el país, departamento/región y ciudad de origen</em>"></i></label>
                            <input type="hidden" id="origen" name="origen" required>
                            
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
                            <input type="hidden" id="destino" name="destino" required>
                            
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
                            <label for="fecha_inicio">Fecha de Inicio <i class="fas fa-question-circle text-muted fst-italic" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-html="true" title="<em>Fecha en la que estará disponible para iniciar el transporte</em>"></i></label>
                            <input type="datetime-local" id="fecha_inicio" name="fecha_inicio" class="form-control" min="{{ now()->format('Y-m-d\TH:i') }}" required>
                            <div class="invalid-feedback" id="fecha-error">La fecha debe ser posterior a la fecha actual</div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="capacidad">Capacidad <i class="fas fa-question-circle text-muted fst-italic" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-html="true" title="<em>Capacidad máxima de carga en kilogramos</em>"></i></label>
                            <input type="number" id="capacidad" name="capacidad" class="form-control" min="0" step="1" required>
                            <small class="form-text text-muted">Capacidad en kg</small>
                        </div>

                        <!-- ① NUEVO: Unidades -->
                        <div class="form-group mb-3">
                            <label for="unidades">Unidades
                                <i class="fas fa-question-circle text-muted fst-italic" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-html="true"
                                title="<em>Cantidad de bultos/unidades disponibles en esta ruta</em>"></i>
                            </label>
                            <input type="number" min="1" id="unidades" name="unidades"
                                class="form-control @error('unidades') is-invalid @enderror"
                                value="1" readonly disabled>
                            @error('unidades')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Dejar vacío si no aplica.</small>
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

                        <div class="form-group mb-3">
                            <label for="descripcion">Comentarios (Opcional)</label>
                            <textarea id="descripcion" name="descripcion" class="form-control" rows="3" placeholder="Ej: Ruta disponible solo en las mañanas, se aceptan mascotas, etc.">{{ old('descripcion') }}</textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label for="precio_referencial">Precio Referencial <i class="fas fa-question-circle text-muted fst-italic" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-html="true" title="<em>Precio estimado por el servicio de transporte en dólares</em>"></i></label>
                            <input type="number" id="precio_referencial" step="0.01" name="precio_referencial" class="form-control" required>
                            <small class="form-text text-muted">Precio en dólares</small>
                        </div>
                        
                        <div class="mt-5 d-flex justify-content-between">
                            <a href="{{ route('ofertas.index') }}" class="btn-pickn">
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
        document.getElementById('fecha_inicio').min = new Date().toISOString().split('T')[0];
        
        // Validación de fecha
        const fechaInicio = document.getElementById('fecha_inicio');
        fechaInicio.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                this.classList.add('is-invalid');
                document.getElementById('fecha-error').style.display = 'block';
            } else {
                this.classList.remove('is-invalid');
                document.getElementById('fecha-error').style.display = 'none';
            }
        });

        // Validación de unidades (no menos de 1 si se ingresa)
        const unidadesInput = document.getElementById('unidades');
        if (unidadesInput) {
            unidadesInput.addEventListener('input', function() {
                if (this.value !== '' && this.value < 1) this.value = 1;
            });
        }
    });
</script>
@endpush
