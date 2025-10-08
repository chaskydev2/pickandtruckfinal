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
                                                        <a href="{{ asset($userDoc->file_path) }}" target="_blank" class="btn btn-primary btn-sm fw-bold">
                                                            <i class="fas fa-eye me-1"></i> Ver documento
                                                        </a>
                                                    </div>
                                                @endif
                                            @else
                                                <form action="{{ route('profile.upload-document') }}" method="POST" enctype="multipart/form-data" class="upload-form">
                                                    @csrf
                                                    <input type="hidden" name="document_id" value="{{ $document->id }}">
                                                    <div class="input-group">
                                                        <input type="file" name="document" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" required>
                                                        <button type="submit" class="btn btn-primary btn-sm fw-bold">
                                                            <i class="fas fa-upload me-1"></i>{{ $userDoc ? 'Reenviar' : 'Enviar' }}
                                                        </button>
                                                    </div>
                                                    <div class="form-text">Formatos aceptados: PDF, JPG, PNG. Máx. 2MB</div>
                                                </form>
                                                
                                                <!-- Mostrar el documento usando la URL completa si existe -->
                                                @if($userDoc && $userDoc->file_path)
                                                    <div class="mt-2">
                                                        <a href="{{ asset($userDoc->file_path) }}" target="_blank" class="btn btn-primary btn-sm fw-bold">
                                                            <i class="fas fa-eye me-1"></i> Ver documento enviado
                                                        </a>
                                                        
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mejorar la experiencia de usuario para los formularios de carga
        const forms = document.querySelectorAll('.upload-form');
        forms.forEach(form => {
            const fileInput = form.querySelector('input[type="file"]');
            const submitBtn = form.querySelector('button[type="submit"]');
            
            // Mostrar nombre del archivo seleccionado sin cambiar el texto del botón
            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        const fileSize = (this.files[0].size / (1024 * 1024)).toFixed(2);
                        
                        // Verificar tamaño máximo permitido
                        if (this.files[0].size > 2 * 1024 * 1024) {
                            alert('El archivo es demasiado grande. El tamaño máximo permitido es 2MB.');
                            this.value = '';
                            return;
                        }
                        
                        // No modificar el texto del botón, solo mantener Enviar o Reenviar
                        // Podemos mostrar el nombre del archivo en otro lugar si es necesario
                    }
                });
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
</script>
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
</style>
@endpush
