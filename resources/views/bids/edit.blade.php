@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Editar tu Oferta</h5>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <h6>Detalles de la {{ $type === 'ruta' ? 'Ruta' : 'Carga' }}</h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Origen:</strong> {{ $model->origen }}</p>
                                <p><strong>Destino:</strong> {{ $model->destino }}</p>
                                <p><strong>Fecha propuesta:</strong> {{ $model->fecha_inicio->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                @if($type === 'ruta')
                                    <p><strong>Tipo de Camión:</strong> {{ $model->truckType->name }}</p>
                                    <p><strong>Capacidad:</strong> {{ number_format($model->capacidad, 0) }} kg</p>
                                    <p><strong>Precio Referencial:</strong> ${{ number_format($model->precio_referencial, 2) }}</p>
                                @else
                                    <p><strong>Tipo de Carga:</strong> {{ $model->cargoType->name }}</p>
                                    <p><strong>Peso:</strong> {{ number_format($model->peso, 0) }} kg</p>
                                    <p><strong>Presupuesto:</strong> ${{ number_format($model->presupuesto, 2) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('bids.update', $bid) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="monto" class="form-label">Tu Oferta ($)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" 
                                       class="form-control @error('monto') is-invalid @enderror" 
                                       name="monto" 
                                       value="{{ old('monto', $bid->monto) }}" 
                                       min="1" 
                                       step="0.01" 
                                       required>
                            </div>
                            <div class="form-text">
                                {{ $type === 'ruta' ? 'Precio referencial' : 'Presupuesto' }}: 
                                ${{ number_format($type === 'ruta' ? $model->precio_referencial : $model->presupuesto, 2) }}
                            </div>
                            @error('monto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="fecha_hora" class="form-label">Fecha Propuesta</label>
                            <input type="date" 
                                class="form-control @error('fecha_hora') is-invalid @enderror" 
                                name="fecha_hora" 
                                value="{{ old('fecha_hora', $bid->fecha_hora->format('Y-m-d')) }}" 
                                required>
                            @error('fecha_hora')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="unidades" class="form-label">Unidades</label>
                            <input type="number" 
                                class="form-control" 
                                id="unidades" 
                                min="1" 
                                step="1" 
                                value="1">
                            <small class="text-muted">Cantidad de unidades para esta oferta</small>
                        </div>

                        <div class="mb-3">
                            <label for="comentario" class="form-label">Comentario (opcional)</label>
                            <textarea class="form-control @error('comentario') is-invalid @enderror" 
                                      name="comentario" 
                                      id="comentario"
                                      rows="4" 
                                      maxlength="300">{{ old('comentario', $bid->comentario) }}</textarea>
                            @error('comentario')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Máximo 300 caracteres permitidos.</small>
                            <div class="text-end text-muted" id="charCount">300 caracteres restantes</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route($type === 'ruta' ? 'ofertas.show' : 'ofertas_carga.show', $model->id) }}" class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Actualizar Oferta
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
document.addEventListener('DOMContentLoaded', function() {
    const comentarioInput = document.getElementById('comentario');
    const charCount = document.getElementById('charCount');
    const submitBtn = document.querySelector('button[type="submit"]');

    comentarioInput.addEventListener('input', function() {
        const remaining = 300 - comentarioInput.value.length;
        charCount.textContent = `${remaining} caracteres restantes`;

        if (remaining < 0) {
            submitBtn.disabled = true;
            charCount.classList.add('text-danger');
        } else {
            submitBtn.disabled = false;
            charCount.classList.remove('text-danger');
        }
    });
});
</script>
@endpush
