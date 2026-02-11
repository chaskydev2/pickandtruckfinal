@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h2 class="h4 mb-0">{{ __('Información de la Empresa') }}</h2>
                    @if(isset($empresa) && $empresa)
                        <a href="{{ route('empresas.show') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye me-1"></i> Ver Perfil
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning">
                            {{ session('warning') }}
                        </div>
                    @endif

                    <form action="{{ route('empresas.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de la Empresa *</label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $empresa->nombre ?? '') }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="logo">
                            <label for="logo" class="form-label">
                                Logo de la Empresa 
                                <span class="badge bg-info text-white" style="font-size: 0.75rem;">
                                    <i class="fas fa-square"></i> Imagen cuadrada requerida
                                </span>
                            </label>
                            @if(isset($empresa) && $empresa->logo)
                                <div class="mb-2">
                                    <p class="text-muted small mb-1">Logo actual:</p>
                                    <img src="{{ $empresa->logo }}" alt="Logo actual" class="img-thumbnail" 
                                         style="max-height: 150px; max-width: 150px; object-fit: cover; aspect-ratio: 1/1;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                   id="logo-input" name="logo" accept="image/*">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>Importante:</strong> Debe ser una imagen cuadrada (mismo ancho y alto). 
                                Ejemplos: 300x300px, 500x500px, 1000x1000px. 
                                Formatos: JPG, PNG, GIF. Máx: 2MB
                            </div>
                            @error('logo')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <!-- Vista previa del logo seleccionado -->
                            <div id="logo-preview" class="mt-3" style="display: none;">
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-eye me-1"></i>Vista previa:
                                </p>
                                <div class="position-relative d-inline-block">
                                    <img id="preview-image" src="" alt="Vista previa" class="img-thumbnail" 
                                         style="max-height: 150px; max-width: 150px; object-fit: cover; aspect-ratio: 1/1; border: 2px dashed #6c757d;">
                                    <div id="aspect-ratio-warning" class="alert alert-warning mt-2" style="display: none; max-width: 300px;">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        <small><strong>Advertencia:</strong> Esta imagen no es cuadrada. Se recomienda usar una imagen con dimensiones iguales (ej: 300x300px) para evitar deformación.</small>
                                    </div>
                                    <div id="aspect-ratio-success" class="alert alert-success mt-2" style="display: none; max-width: 300px;">
                                        <i class="fas fa-check-circle me-1"></i>
                                        <small><strong>¡Perfecto!</strong> Esta imagen es cuadrada y se verá correctamente en toda la plataforma.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $empresa->descripcion ?? '') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono', $empresa->telefono ?? '') }}">
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" value="{{ old('direccion', $empresa->direccion ?? '') }}">
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sitio_web" class="form-label">Sitio Web</label>
                            <input type="text" class="form-control @error('sitio_web') is-invalid @enderror" id="sitio_web" name="sitio_web" value="{{ old('sitio_web', $empresa->sitio_web ?? '') }}">
                            @error('sitio_web')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Guardar Información</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const logoInput = document.getElementById('logo-input');
    const previewContainer = document.getElementById('logo-preview');
    const previewImage = document.getElementById('preview-image');
    const warningDiv = document.getElementById('aspect-ratio-warning');
    const successDiv = document.getElementById('aspect-ratio-success');

    logoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // Verificar que es una imagen
            if (!file.type.startsWith('image/')) {
                alert('Por favor selecciona un archivo de imagen válido.');
                logoInput.value = '';
                previewContainer.style.display = 'none';
                return;
            }

            // Verificar tamaño (2MB max)
            if (file.size > 2048 * 1024) {
                alert('El archivo es demasiado grande. El tamaño máximo es 2MB.');
                logoInput.value = '';
                previewContainer.style.display = 'none';
                return;
            }

            // Crear URL para preview
            const reader = new FileReader();
            
            reader.onload = function(event) {
                previewImage.src = event.target.result;
                previewContainer.style.display = 'block';

                // Crear una imagen temporal para obtener dimensiones
                const img = new Image();
                img.onload = function() {
                    const width = img.width;
                    const height = img.height;
                    const isSquare = width === height;

                    // Mostrar advertencia o éxito según aspect ratio
                    if (isSquare) {
                        warningDiv.style.display = 'none';
                        successDiv.style.display = 'block';
                        successDiv.innerHTML = `
                            <i class="fas fa-check-circle me-1"></i>
                            <small><strong>¡Perfecto!</strong> Esta imagen es cuadrada (${width}x${height}px) y se verá correctamente en toda la plataforma.</small>
                        `;
                    } else {
                        successDiv.style.display = 'none';
                        warningDiv.style.display = 'block';
                        warningDiv.innerHTML = `
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <small><strong>Advertencia:</strong> Esta imagen no es cuadrada (${width}x${height}px). 
                            Se recomienda usar una imagen con dimensiones iguales (ej: 300x300px) para evitar deformación. 
                            El sistema rechazará esta imagen al intentar guardar.</small>
                        `;
                    }
                };
                img.src = event.target.result;
            };

            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
        }
    });
});
</script>

@endsection
