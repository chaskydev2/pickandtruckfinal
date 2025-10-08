@extends('layouts.app')

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
        <div class="form-group mb-3">
            <label for="origen">Origen</label>
            <input type="text" id="origen" name="origen" class="form-control" value="{{ $oferta->origen }}" required>
        </div>
        <div class="form-group mb-3">
            <label for="destino">Destino</label>
            <input type="text" id="destino" name="destino" class="form-control" value="{{ $oferta->destino }}" required>
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
                value="{{ old('unidades', $oferta->unidades) }}">
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

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDg5hWgPIKdJBMjLodd5Ttu-f6JRSsw8fY&libraries=places&callback=initAutocomplete"></script>
<script>
    function initAutocomplete() {
        var origenInput = document.getElementById('origen');
        var destinoInput = document.getElementById('destino');

        var origenAutocomplete = new google.maps.places.Autocomplete(origenInput);
        var destinoAutocomplete = new google.maps.places.Autocomplete(destinoInput);
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof google !== 'undefined' && google.maps) {
            initAutocomplete();
        } else {
            console.error('Google Maps JavaScript API not loaded.');
        }
    });

    // Validación de unidades (no menos de 1 si se ingresa)
    document.addEventListener('DOMContentLoaded', function () {
        const unidadesInput = document.getElementById('unidades');
        if (unidadesInput) {
            unidadesInput.addEventListener('input', function() {
                if (this.value !== '' && this.value < 1) this.value = 1;
            });
        }
    });
</script>
@endsection
