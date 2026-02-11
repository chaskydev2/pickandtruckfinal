@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <!-- Navegación de pestañas si el usuario está verificado -->
            @if(auth()->user()->verified)
            <ul class="nav nav-tabs profile-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link profile-tab" href="{{ route('profile.edit') }}">Perfil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link profile-tab" href="{{ route('profile.edit') }}#security" 
                       onclick="setTimeout(() => document.querySelector('#security-tab').click(), 100)">Seguridad</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link profile-tab active" href="{{ route('profile.documents') }}">Documentos</a>
                </li>
            </ul>
            @endif

            @if(!auth()->user()->verified)
                <div class="alert alert-warning">
                    <h4 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Verificación requerida</h4>
                    <p>Su cuenta necesita ser verificada para acceder a todas las funciones de la plataforma. Por favor, suba los documentos requeridos a continuación.</p>
                    
                    @if(session('warning') || session('success') || session('error'))
                        <hr>
                        <p class="mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ session('warning') ?: (session('error') ?: (session('success') ?: '')) }}
                        </p>
                    @endif
                </div>
            @elseif(session('warning') || session('success') || session('error'))
                <div class="alert alert-{{ session('success') ? 'success' : (session('error') ? 'danger' : 'warning') }}">
                    <i class="fas fa-info-circle me-1"></i>
                    {{ session('warning') ?: (session('error') ?: (session('success') ?: '')) }}
                </div>
            @endif
            
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                    <h2 class="h4 mb-0">{{ __('Envío de Documentos') }}</h2>
                    @if(auth()->user()->verified)
                        <a href="{{ route('profile.documents') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Volver a Documentos
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    <!-- Estado de verificación -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                @if(auth()->user()->verified)
                                    <i class="fas fa-check-circle text-success fa-2x"></i>
                                @else
                                    <i class="fas fa-clock text-warning fa-2x"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1">Estado de verificación</h5>
                                <p class="mb-0">
                                    @if(auth()->user()->verified)
                                        <span class="text-success">Su cuenta está verificada</span>
                                    @else
                                        <span class="text-warning">Pendiente de verificación</span>
                                    @endif
                                </p>
                                
                                @php
                                    $totalDocs = count($requiredDocuments);
                                    $approvedDocs = $userDocuments->where('status', 'aprobado')->count();
                                    $pendingDocs = $userDocuments->whereIn('status', ['pendiente'])->count();
                                    $rejectedDocs = $userDocuments->where('status', 'rechazado')->count();
                                    $noDocs = $totalDocs - $approvedDocs - $pendingDocs - $rejectedDocs;
                                    
                                    $approvedPercent = $totalDocs > 0 ? ($approvedDocs / $totalDocs) * 100 : 0;
                                    $pendingPercent = $totalDocs > 0 ? ($pendingDocs / $totalDocs) * 100 : 0;
                                    $rejectedPercent = $totalDocs > 0 ? ($rejectedDocs / $totalDocs) * 100 : 0;
                                    $noDocsPercent = $totalDocs > 0 ? ($noDocs / $totalDocs) * 100 : 0;
                                @endphp
                                
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $approvedPercent }}%" aria-valuenow="{{ $approvedPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $pendingPercent }}%" aria-valuenow="{{ $pendingPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $rejectedPercent }}%" aria-valuenow="{{ $rejectedPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    <div class="progress-bar bg-secondary" role="progressbar" style="width: {{ $noDocsPercent }}%" aria-valuenow="{{ $noDocsPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between small mt-1 text-muted">
                                    <span>Aprobados: {{ $approvedDocs }}/{{ $totalDocs }}</span>
                                    <span>Pendientes: {{ $pendingDocs }}</span>
                                    <span>Rechazados: {{ $rejectedDocs }}</span>
                                    <span>Sin enviar: {{ $noDocs }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Por favor suba los documentos requeridos para verificar su cuenta. Formatos aceptados: PDF, JPG, PNG (máx. 2MB).
                    </div>

                    <div class="document-list mt-4">
                        @foreach($requiredDocuments as $document)
                            @php
                                $userDoc = $userDocuments->firstWhere('required_document_id', $document->id);
                                $statusClass = $userDoc ? 
                                    ($userDoc->status === 'aprobado' ? 'border-success' : 
                                    ($userDoc->status === 'rechazado' ? 'border-danger' : 'border-warning')) : 
                                    'border-secondary';
                                $statusBadge = $userDoc ? 
                                    ($userDoc->status === 'aprobado' ? 'bg-success' : 
                                    ($userDoc->status === 'rechazado' ? 'bg-danger' : 'bg-warning')) : 
                                    'bg-secondary';
                                $statusText = $userDoc ? 
                                    ucfirst($userDoc->status) : 
                                    'Pendiente de envío';
                            @endphp

                            <div class="card mb-3 document-card {{ $statusClass }}">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <h5 class="card-title">{{ $document->name }}</h5>
                                            @if($document->description)
                                                <p class="card-text text-muted small">{{ $document->description }}</p>
                                            @endif
                                            @if($document->notes)
                                                <p class="card-text text-muted small"><i class="fas fa-info-circle me-1"></i>{{ $document->notes }}</p>
                                            @endif
                                            
                                            {{-- Botón de descarga para Carta de Aceptación --}}
                                            @if(stripos($document->name, 'Carta de aceptación') !== false || $document->id == 6)
                                                <div class="mb-2">
                                                    <a href="{{ asset('templates/carta-aceptacion-pickntruck.pdf') }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       download="Carta_de_Aceptacion_PickNTruck.pdf"
                                                       target="_blank">
                                                        <i class="fas fa-download me-1"></i>Descargar Plantilla PDF
                                                    </a>
                                                    <small class="d-block text-muted mt-1">
                                                        <i class="fas fa-info-circle me-1"></i>Imprima en hoja membretada, complete, firme y suba el PDF escaneado
                                                    </small>
                                                </div>
                                            @endif
                                            
                                            <span class="badge {{ $statusBadge }} mb-2">{{ $statusText }}</span>
                                        </div>
                                        <div class="col-md-7">
                                            @if($userDoc && $userDoc->status === 'rechazado' && $userDoc->comments)
                                                <div class="alert alert-danger p-2 small mb-3">
                                                    <strong>Motivo del rechazo:</strong> {{ $userDoc->comments }}
                                                </div>
                                            @endif
                                            
                                            @if($userDoc && $userDoc->status === 'aprobado')
                                                <div class="text-success">
                                                    <i class="fas fa-check-circle me-1"></i> Documento verificado
                                                    <small class="d-block text-muted mt-1">
                                                        Enviado el {{ $userDoc->updated_at->format('d/m/Y') }}
                                                    </small>
                                                </div>
                                                
                                                 <!-- Mostrar el documento usando la URL completa -->
                                                @if($userDoc->file_path)
                                                    <div class="mt-2">
                                                        <button type="button" class="btn btn-primary btn-sm fw-bold" onclick="previewDocument('{{ asset($userDoc->file_path) }}', '{{ $document->name }}')">
                                                            <i class="fas fa-eye me-1"></i> Ver documento
                                                        </button>
                                                    </div>
                                                @endif
                                            @else
                                                <form action="{{ route('profile.upload-document') }}" method="POST" enctype="multipart/form-data" class="upload-form" data-doc-id="{{ $document->id }}">
                                                    @csrf
                                                    <input type="hidden" name="document_id" value="{{ $document->id }}">
                                                    <div class="input-group">
                                                        <input type="file" name="document" class="form-control form-control-sm document-input" accept=".pdf,.jpg,.jpeg,.png" required data-doc-id="{{ $document->id }}">
                                                        <button type="button" class="btn btn-outline-secondary btn-sm preview-btn" data-doc-id="{{ $document->id }}" style="display: none;">
                                                            <i class="fas fa-eye me-1"></i>Vista previa
                                                        </button>
                                                        <button type="submit" class="btn btn-primary btn-sm fw-bold">
                                                            <i class="fas fa-upload me-1"></i>{{ $userDoc ? 'Reenviar' : 'Enviar' }}
                                                        </button>
                                                    </div>
                                                    <div class="form-text">Formatos aceptados: PDF, JPG, PNG. Máx. 2MB</div>
                                                    <div class="preview-container mt-2" id="preview-{{ $document->id }}" style="display: none;"></div>
                                                </form>
                                                
                                                <!-- Mostrar el documento usando la URL completa si existe -->
                                                @if($userDoc && $userDoc->file_path)
                                                    <div class="mt-2">
                                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="previewDocument('{{ asset($userDoc->file_path) }}', '{{ $document->name }}')">
                                                            <i class="fas fa-eye me-1"></i> Ver documento enviado
                                                        </button>
                                                        
                                                        @if(config('app.debug'))
                                                        <div class="mt-2 small text-muted">
                                                            <strong>ID:</strong> {{ $userDoc->id }}<br>
                                                            <strong>URL:</strong> {{ $userDoc->file_path }}<br>
                                                            <strong>Estado:</strong> {{ $userDoc->status }}<br>
                                                            <strong>Actualizado:</strong> {{ $userDoc->updated_at }}
                                                        </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="card mt-4 bg-light border-0">
                        <div class="card-body">
                            <h5>Instrucciones</h5>
                            <ul class="small text-muted mb-0">
                                <li>Todos los documentos deben estar claramente legibles y no tener recortes.</li>
                                <li>Los documentos serán revisados por nuestro equipo en un plazo de 24-48 horas hábiles.</li>
                                <li>Si un documento es rechazado, se le notificará el motivo y deberá enviarlo nuevamente.</li>
                                <li>Una vez que todos los documentos estén aprobados, su cuenta será verificada automáticamente.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                 
                

                @if(!auth()->user()->verified)
                <div class="card-footer bg-white border-top-0 text-center">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-link text-muted">Cerrar sesión</button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de previsualización de documentos -->
<div class="modal fade" id="documentPreviewModal" tabindex="-1" aria-labelledby="documentPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentPreviewModalLabel">Vista previa del documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="documentPreviewBody" style="min-height: 500px; max-height: 80vh; overflow-y: auto;">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="downloadDocumentBtn" class="btn btn-primary" target="_blank" download>
                    <i class="fas fa-download me-1"></i>Descargar
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Función global para previsualizar documentos ya subidos
    function previewDocument(url, title) {
        const modal = new bootstrap.Modal(document.getElementById('documentPreviewModal'));
        const modalTitle = document.getElementById('documentPreviewModalLabel');
        const modalBody = document.getElementById('documentPreviewBody');
        const downloadBtn = document.getElementById('downloadDocumentBtn');
        
        modalTitle.textContent = title || 'Vista previa del documento';
        downloadBtn.href = url;
        
        // Extraer el nombre del archivo de la URL y establecerlo como nombre de descarga
        const fileName = url.split('/').pop();
        const extension = fileName.split('.').pop().toLowerCase();
        const sanitizedTitle = (title || 'documento').replace(/[^a-z0-9]/gi, '_').toLowerCase();
        downloadBtn.setAttribute('download', sanitizedTitle + '.' + extension);
        
        // Mostrar loading
        modalBody.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-3 text-muted">Cargando documento...</p>
            </div>
        `;
        
        modal.show();
        
        // Determinar tipo de archivo
        
        if (extension === 'pdf') {
            modalBody.innerHTML = `<embed src="${url}" type="application/pdf" width="100%" height="600px" />`;
        } else if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
            modalBody.innerHTML = `
                <div class="text-center">
                    <img src="${url}" class="img-fluid" alt="${title}" style="max-height: 70vh;" />
                </div>
            `;
        } else {
            modalBody.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No se puede mostrar una vista previa de este tipo de archivo. 
                    <a href="${url}" target="_blank" class="alert-link">Haga clic aquí para abrirlo en una nueva pestaña</a>.
                </div>
            `;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Mejorar la experiencia de usuario para los formularios de carga
        const forms = document.querySelectorAll('.upload-form');
        forms.forEach(form => {
            const fileInput = form.querySelector('.document-input');
            const submitBtn = form.querySelector('button[type="submit"]');
            const previewBtn = form.querySelector('.preview-btn');
            const docId = form.dataset.docId;
            const previewContainer = document.getElementById(`preview-${docId}`);
            
            // Manejo AJAX del formulario
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const originalBtnText = submitBtn.innerHTML;
                
                // Deshabilitar botón y mostrar loading
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Enviando...';
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    // Verificar si la respuesta es exitosa
                    if (!response.ok) {
                        return response.text().then(text => {
                            console.error('Error del servidor:', text);
                            throw new Error(`Error ${response.status}: ${response.statusText}`);
                        });
                    }
                    
                    // Verificar que sea JSON
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.includes("application/json")) {
                        return response.json();
                    } else {
                        throw new Error('La respuesta del servidor no es JSON');
                    }
                })
                .then(data => {
                    if (data.success) {
                        // Mostrar mensaje de éxito con toast profesional
                        if (window.showToastNotification) {
                            window.showToastNotification(
                                '✓ ¡Documento Enviado!', 
                                data.message || 'Tu documento ha sido subido correctamente y está en revisión.',
                                'success',
                                4000
                            );
                        } else {
                            alert(data.message || 'Documento subido correctamente');
                        }

                        // Actualizar el estado del documento en la UI sin recargar
                        const badge = form.closest('.card').querySelector('.badge');
                        if (badge) {
                            badge.className = 'badge bg-warning text-dark';
                            badge.textContent = 'Pendiente';
                        }
                        
                        // Deshabilitar el formulario ya que el documento fue enviado
                        form.querySelector('input[type="file"]').disabled = true;
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '✓ Enviado';
                        submitBtn.classList.remove('btn-primary');
                        submitBtn.classList.add('btn-success');
                    } else {
                        throw new Error(data.error || 'Error al subir el documento');
                    }
                })
                .catch(error => {
                    console.error('Error completo:', error);
                    const errorMsg = error.message || 'Error al subir el documento. Por favor, intente nuevamente.';
                    
                    if (window.showToastNotification) {
                        window.showToastNotification(
                            '✕ Error al Subir',
                            errorMsg,
                            'error',
                            6000
                        );
                    } else {
                        alert('Error: ' + errorMsg);
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                });
            });
            
            // Manejar selección de archivo
            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        const file = this.files[0];
                        const fileSize = (file.size / (1024 * 1024)).toFixed(2);
                        
                        // Verificar tamaño máximo permitido
                        if (file.size > 2 * 1024 * 1024) {
                            alert('El archivo es demasiado grande. El tamaño máximo permitido es 2MB.');
                            this.value = '';
                            previewBtn.style.display = 'none';
                            previewContainer.style.display = 'none';
                            return;
                        }
                        
                        // Mostrar botón de vista previa
                        previewBtn.style.display = 'inline-block';
                        
                        // Generar vista previa automática
                        generatePreview(file, previewContainer);
                    } else {
                        previewBtn.style.display = 'none';
                        previewContainer.style.display = 'none';
                    }
                });
                
                // Manejar clic en botón de vista previa
                if (previewBtn) {
                    previewBtn.addEventListener('click', function() {
                        if (fileInput.files.length > 0) {
                            const file = fileInput.files[0];
                            showFilePreviewModal(file);
                        }
                    });
                }
            }
            
            form.addEventListener('submit', function(e) {
                if (fileInput && fileInput.files.length === 0) {
                    e.preventDefault();
                    alert('Por favor seleccione un archivo para subir.');
                    return;
                }
                
                // Deshabilitar el botón de envío para prevenir envíos múltiples
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...';
                }
            });
        });
    });
    
    // Función para generar vista previa inline
    function generatePreview(file, container) {
        container.innerHTML = '';
        container.style.display = 'none';
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const fileType = file.type;
            
            if (fileType.startsWith('image/')) {
                container.innerHTML = `
                    <div class="card">
                        <div class="card-body p-2">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <img src="${e.target.result}" class="img-thumbnail" style="max-height: 80px; max-width: 80px;" />
                                </div>
                                <div class="col">
                                    <small class="text-muted">
                                        <i class="fas fa-file-image me-1"></i>
                                        <strong>${file.name}</strong> (${(file.size / 1024).toFixed(2)} KB)
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                container.style.display = 'block';
            } else if (fileType === 'application/pdf') {
                container.innerHTML = `
                    <div class="card">
                        <div class="card-body p-2">
                            <small class="text-muted">
                                <i class="fas fa-file-pdf me-1 text-danger"></i>
                                <strong>${file.name}</strong> (${(file.size / 1024).toFixed(2)} KB)
                            </small>
                        </div>
                    </div>
                `;
                container.style.display = 'block';
            }
        };
        
        if (file.type.startsWith('image/')) {
            reader.readAsDataURL(file);
        } else {
            reader.readAsArrayBuffer(file);
        }
    }
    
    // Función para mostrar vista previa en modal
    function showFilePreviewModal(file) {
        const modal = new bootstrap.Modal(document.getElementById('documentPreviewModal'));
        const modalTitle = document.getElementById('documentPreviewModalLabel');
        const modalBody = document.getElementById('documentPreviewBody');
        const downloadBtn = document.getElementById('downloadDocumentBtn');
        
        modalTitle.textContent = file.name;
        downloadBtn.style.display = 'none'; // Ocultar botón de descarga para archivos locales
        
        modalBody.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        `;
        
        modal.show();
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const fileType = file.type;
            
            if (fileType.startsWith('image/')) {
                modalBody.innerHTML = `
                    <div class="text-center">
                        <img src="${e.target.result}" class="img-fluid" alt="${file.name}" style="max-height: 70vh;" />
                    </div>
                `;
            } else if (fileType === 'application/pdf') {
                modalBody.innerHTML = `<embed src="${e.target.result}" type="application/pdf" width="100%" height="600px" />`;
            } else {
                modalBody.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Archivo seleccionado: <strong>${file.name}</strong> (${(file.size / 1024).toFixed(2)} KB)
                    </div>
                `;
            }
        };
        
        reader.readAsDataURL(file);
    }
</script>

<!-- Script para actualización automática cuando el admin aprueba/rechaza documentos -->
<script src="/js/document-auto-refresh.js"></script>
@endpush

@push('styles')
<style>
    .form-text {
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }
    
    .document-card {
        transition: all 0.2s ease;
        border-left-width: 4px;
    }
    
    .document-card:hover {
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
    }
    
    .border-success {
        border-left-color: var(--bs-success) !important;
    }
    
    .border-warning {
        border-left-color: var(--bs-warning) !important;
    }
    
    .border-danger {
        border-left-color: var(--bs-danger) !important;
    }
    
    .border-secondary {
        border-left-color: var(--bs-secondary) !important;
    }
    
    .progress {
        border-radius: 4px;
        overflow: hidden;
    }
    
    .alert-danger.small {
        font-size: 0.8rem;
        padding: 0.5rem;
        margin-bottom: 0.75rem;
    }

    .profile-tabs {
        background-color: transparent;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 0;
    }
    
    .profile-tab {
        color: white !important;
        border: none !important;
        padding: 0.75rem 1.25rem;
        font-weight: 500;
        margin-bottom: -1px;
    }
    
    .profile-tab:hover {
        color: rgba(255, 255, 255, 0.8) !important;
        background-color: transparent !important;
        border-bottom: 2px solid rgba(255, 255, 255, 0.5) !important;
    }
    
    .profile-tab.active {
        color: white !important;
        background-color: transparent !important;
        border-bottom: 2px solid white !important;
    }
    
    .preview-container {
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .preview-btn {
        transition: all 0.2s ease;
    }
    
    .preview-btn:hover {
        transform: scale(1.05);
    }
    
    #documentPreviewModal .modal-body {
        background-color: #f8f9fa;
    }
    
    #documentPreviewModal embed {
        border: none;
        border-radius: 0.25rem;
    }
    
    .img-thumbnail {
        border: 2px solid #dee2e6;
        transition: all 0.2s ease;
    }
    
    .img-thumbnail:hover {
        border-color: #0d6efd;
        transform: scale(1.05);
        cursor: pointer;
    }
</style>
@endpush
