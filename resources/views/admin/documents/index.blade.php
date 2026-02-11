@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">
                    <i class="fas fa-file-alt me-2"></i>
                    Gestión de Documentos de Usuarios
                </h1>
                <div class="badge bg-primary fs-6">
                    {{ $documents->total() }} documentos pendientes/rechazados
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Filter tabs -->
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#pendientes">
                        <i class="fas fa-clock me-2"></i>
                        Pendientes ({{ $documents->where('status', 'pendiente')->count() }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#rechazados">
                        <i class="fas fa-times-circle me-2"></i>
                        Rechazados ({{ $documents->where('status', 'rechazado')->count() }})
                    </a>
                </li>
            </ul>

            <div class="card">
                <div class="card-body">
                    @if($documents->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay documentos pendientes de revisión</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Rol</th>
                                        <th>Empresa</th>
                                        <th>Documento</th>
                                        <th>Fecha Subida</th>
                                        <th>Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($documents as $document)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-2">
                                                        {{ substr($document->user->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $document->user->name }}</div>
                                                        <small class="text-muted">{{ $document->user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($document->user->role === 'carrier')
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-truck me-1"></i>TC
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-boxes me-1"></i>FFD
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $document->user->company_name ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $document->requiredDocument->name }}</strong>
                                                @if($document->requiredDocument->description)
                                                    <br><small class="text-muted">{{ $document->requiredDocument->description }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $document->created_at->format('d/m/Y H:i') }}</small>
                                            </td>
                                            <td>
                                                @if($document->status === 'pendiente')
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-clock me-1"></i>Pendiente
                                                    </span>
                                                @elseif($document->status === 'rechazado')
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times-circle me-1"></i>Rechazado
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i>Aprobado
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <!-- View document -->
                                                    <a href="{{ $document->file_path }}" 
                                                       target="_blank" 
                                                       class="btn btn-outline-primary"
                                                       title="Ver documento">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    <!-- Approve button -->
                                                    <button type="button" 
                                                            class="btn btn-outline-success"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#approveModal{{ $document->id }}"
                                                            title="Aprobar">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    
                                                    <!-- Reject button -->
                                                    <button type="button" 
                                                            class="btn btn-outline-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#rejectModal{{ $document->id }}"
                                                            title="Rechazar">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    
                                                    <!-- View all user documents -->
                                                    <a href="{{ route('admin.documents.user', $document->user_id) }}" 
                                                       class="btn btn-outline-secondary"
                                                       title="Ver todos los documentos del usuario">
                                                        <i class="fas fa-folder-open"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Approve Modal -->
                                        <div class="modal fade" id="approveModal{{ $document->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('admin.documents.approve', $document->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header bg-success text-white">
                                                            <h5 class="modal-title">
                                                                <i class="fas fa-check-circle me-2"></i>
                                                                Aprobar Documento
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>¿Está seguro que desea aprobar el documento <strong>{{ $document->requiredDocument->name }}</strong> de <strong>{{ $document->user->name }}</strong>?</p>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Comentarios (opcional)</label>
                                                                <textarea name="comments" 
                                                                          class="form-control" 
                                                                          rows="3"
                                                                          placeholder="Agregar comentarios..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="fas fa-check me-1"></i>Aprobar
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal{{ $document->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('admin.documents.reject', $document->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title">
                                                                <i class="fas fa-times-circle me-2"></i>
                                                                Rechazar Documento
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>¿Está seguro que desea rechazar el documento <strong>{{ $document->requiredDocument->name }}</strong> de <strong>{{ $document->user->name }}</strong>?</p>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label text-danger">
                                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                                    Motivo del rechazo (requerido)
                                                                </label>
                                                                <textarea name="comments" 
                                                                          class="form-control" 
                                                                          rows="3"
                                                                          required
                                                                          placeholder="Explique el motivo del rechazo para que el usuario pueda corregir el documento..."></textarea>
                                                                <small class="text-muted">El usuario recibirá una notificación con este comentario.</small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="fas fa-times me-1"></i>Rechazar
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $documents->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 18px;
}

.profile-tabs .nav-link {
    color: #6c757d;
    border: none;
    border-bottom: 2px solid transparent;
    padding: 0.75rem 1.5rem;
}

.profile-tabs .nav-link:hover {
    border-bottom-color: #667eea;
    color: #667eea;
}

.profile-tabs .nav-link.active {
    border-bottom-color: #667eea;
    color: #667eea;
    font-weight: 600;
}
</style>
@endsection
