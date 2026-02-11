@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Navegación de pestañas -->
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

            <!-- Contenido -->
            <div class="card">
                <div class="card-header">
                    <h2 class="h4 mb-0">{{ __('Documentos Requeridos') }}</h2>
                    <a href="{{ route('profile.document-submission') }}" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i> Nuevo envío de documentos
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Documento</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requiredDocuments as $document)
                                    <tr>
                                        <td>
                                            {{ $document->name }}
                                            @if($document->description)
                                                <small class="d-block text-muted">{{ $document->description }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $userDoc = $userDocuments->firstWhere('required_document_id', $document->id);
                                            @endphp
                                            @if($userDoc)
                                                <span class="badge bg-{{ $userDoc->status === 'aprobado' ? 'success' : ($userDoc->status === 'rechazado' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($userDoc->status) }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">Pendiente</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $userDoc = $userDocuments->firstWhere('required_document_id', $document->id);
                                            @endphp
                                            @if($userDoc)
                                                @if($userDoc->status === 'rechazado')
                                                    <div class="d-flex gap-2 align-items-center flex-wrap">
                                                        @if($userDoc->file_path)
                                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="previewDocument('{{ asset($userDoc->file_path) }}', '{{ $document->name }}')">
                                                                <i class="fas fa-eye me-1"></i>Ver
                                                            </button>
                                                        @endif
                                                        <button type="button" class="btn btn-warning btn-sm" onclick="showReuploadModal({{ $document->id }}, '{{ $document->name }}')">
                                                            <i class="fas fa-redo me-1"></i>Reenviar
                                                        </button>
                                                    </div>
                                                @else
                                                    <div class="d-flex gap-2 align-items-center">
                                                        @if($userDoc->file_path)
                                                            <button type="button" class="btn btn-primary btn-sm" onclick="previewDocument('{{ asset($userDoc->file_path) }}', '{{ $document->name }}')">
                                                                <i class="fas fa-eye me-1"></i>Ver documento
                                                            </button>
                                                        @endif
                                                        <span class="text-muted small">
                                                            <i class="fas fa-check-circle"></i> 
                                                            Documento enviado {{ $userDoc->created_at->diffForHumans() }}
                                                        </span>
                                                    </div>
                                                @endif
                                            @else
                                                <button type="button" class="btn btn-primary btn-sm" onclick="showUploadModal({{ $document->id }}, '{{ $document->name }}')">
                                                    <i class="fas fa-upload me-1"></i>Seleccionar
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
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
            <div class="modal-body" id="documentPreviewBody" style="min-height: 500px; max-height: 80vh; overflow-y: auto; background-color: #f8f9fa;">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="downloadDocumentBtn" class="btn btn-primary" target="_blank">
                    <i class="fas fa-download me-1"></i>Descargar
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para subir documento -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadDocumentModalLabel">Subir documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="uploadForm" action="{{ route('profile.upload-document') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="document_id" id="upload_document_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="document_file" class="form-label">Seleccione el archivo</label>
                        <input type="file" class="form-control" id="document_file" name="document" accept=".pdf,.jpg,.jpeg,.png" required>
                        <div class="form-text">Formatos aceptados: PDF, JPG, PNG. Máximo 2MB</div>
                    </div>
                    <div id="uploadPreviewContainer" class="mt-3" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-outline-primary" id="previewUploadBtn" style="display: none;">
                        <i class="fas fa-eye me-1"></i>Vista previa
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitUploadBtn">
                        <i class="fas fa-upload me-1"></i>Subir
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .profile-tabs {
        background-color: transparent;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 0;
    }
    
    .profile-tab {
        color: var(--color-nav-background) !important;
        border: none !important;
        padding: 0.75rem 1.25rem;
        font-weight: 500;
        margin-bottom: -1px;
    }
    
    .profile-tab:hover {
        color: var(--color-nav-background) !important;
        background-color: transparent !important;
        border-bottom: 2px solid rgba(26, 26, 46, 0.5) !important;
    }
    
    .profile-tab.active {
        color: var(--color-nav-background) !important;
        background-color: transparent !important;
        border-bottom: 2px solid var(--color-nav-background) !important;
    }
    
    /* Estilos para inputs de formularios */
    input[type="file"], 
    input[type="text"],
    input[type="email"],
    input[type="password"],
    textarea,
    .form-control {
        background-color: #ffffff !important;
        color: #333333 !important;
        border: 1px solid #d1d5db !important;
    }
    
    .form-control:focus {
        background-color: #ffffff !important;
        border-color: var(--color-primary) !important;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25) !important;
    }
    
    #documentPreviewModal embed {
        border: none;
        border-radius: 0.25rem;
    }
    
    .preview-image {
        max-width: 100%;
        max-height: 70vh;
        border-radius: 0.25rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
</style>
@endpush

@push('scripts')
<script>
    // Función para previsualizar documentos
    function previewDocument(url, title) {
        const modal = new bootstrap.Modal(document.getElementById('documentPreviewModal'));
        const modalTitle = document.getElementById('documentPreviewModalLabel');
        const modalBody = document.getElementById('documentPreviewBody');
        const downloadBtn = document.getElementById('downloadDocumentBtn');
        
        modalTitle.textContent = title || 'Vista previa del documento';
        downloadBtn.href = url;
        
        // Mostrar loading
        modalBody.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-3 text-muted">Cargando documento...</p>
            </div>
        `;
        
        modal.show();
        
        // Determinar tipo de archivo
        const extension = url.split('.').pop().toLowerCase().split('?')[0];
        
        setTimeout(() => {
            if (extension === 'pdf') {
                modalBody.innerHTML = `<embed src="${url}" type="application/pdf" width="100%" height="600px" />`;
            } else if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
                modalBody.innerHTML = `
                    <div class="text-center">
                        <img src="${url}" class="preview-image" alt="${title}" />
                    </div>
                `;
            } else {
                modalBody.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        No se puede mostrar una vista previa de este tipo de archivo. 
                        <a href="${url}" target="_blank" class="alert-link">Haga clic aquí para descargarlo</a>.
                    </div>
                `;
            }
        }, 300);
    }
    
    // Función para mostrar modal de subida
    function showUploadModal(documentId, documentName) {
        const modal = new bootstrap.Modal(document.getElementById('uploadDocumentModal'));
        document.getElementById('uploadDocumentModalLabel').textContent = 'Subir: ' + documentName;
        document.getElementById('upload_document_id').value = documentId;
        document.getElementById('document_file').value = '';
        document.getElementById('uploadPreviewContainer').style.display = 'none';
        document.getElementById('previewUploadBtn').style.display = 'none';
        modal.show();
    }
    
    // Función para mostrar modal de resubida
    function showReuploadModal(documentId, documentName) {
        showUploadModal(documentId, documentName);
        document.getElementById('uploadDocumentModalLabel').textContent = 'Reenviar: ' + documentName;
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('document_file');
        const previewBtn = document.getElementById('previewUploadBtn');
        const previewContainer = document.getElementById('uploadPreviewContainer');
        const uploadForm = document.getElementById('uploadForm');
        const submitBtn = document.getElementById('submitUploadBtn');
        
        // Manejar selección de archivo
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    const file = this.files[0];
                    
                    // Validar tamaño
                    if (file.size > 2 * 1024 * 1024) {
                        alert('El archivo es demasiado grande. El tamaño máximo permitido es 2MB.');
                        this.value = '';
                        previewBtn.style.display = 'none';
                        previewContainer.style.display = 'none';
                        return;
                    }
                    
                    // Mostrar botón de vista previa
                    previewBtn.style.display = 'inline-block';
                    
                    // Generar vista previa inline
                    generateInlinePreview(file, previewContainer);
                } else {
                    previewBtn.style.display = 'none';
                    previewContainer.style.display = 'none';
                }
            });
        }
        
        // Manejar clic en vista previa
        if (previewBtn) {
            previewBtn.addEventListener('click', function() {
                if (fileInput.files.length > 0) {
                    showFilePreviewModal(fileInput.files[0]);
                }
            });
        }
        
        // Manejar envío del formulario
        if (uploadForm) {
            uploadForm.addEventListener('submit', function(e) {
                if (fileInput.files.length === 0) {
                    e.preventDefault();
                    alert('Por favor seleccione un archivo.');
                    return;
                }
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Subiendo...';
            });
        }
    });
    
    // Función para generar vista previa inline
    function generateInlinePreview(file, container) {
        container.innerHTML = '';
        container.style.display = 'none';
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            if (file.type.startsWith('image/')) {
                container.innerHTML = `
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <img src="${e.target.result}" class="img-thumbnail" style="max-height: 100px; max-width: 100px;" />
                                </div>
                                <div class="col">
                                    <div class="text-muted">
                                        <i class="fas fa-file-image me-1"></i>
                                        <strong>${file.name}</strong>
                                    </div>
                                    <small class="text-muted">Tamaño: ${(file.size / 1024).toFixed(2)} KB</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else if (file.type === 'application/pdf') {
                container.innerHTML = `
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="text-muted">
                                <i class="fas fa-file-pdf me-1 text-danger"></i>
                                <strong>${file.name}</strong>
                            </div>
                            <small class="text-muted">Tamaño: ${(file.size / 1024).toFixed(2)} KB</small>
                        </div>
                    </div>
                `;
            }
            container.style.display = 'block';
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
        downloadBtn.style.display = 'none';
        
        modalBody.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        `;
        
        modal.show();
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            if (file.type.startsWith('image/')) {
                modalBody.innerHTML = `
                    <div class="text-center">
                        <img src="${e.target.result}" class="preview-image" alt="${file.name}" />
                    </div>
                `;
            } else if (file.type === 'application/pdf') {
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
@endpush
