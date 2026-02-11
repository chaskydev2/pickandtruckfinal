@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="mb-4">
                <a href="{{ route('admin.documents.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
                    <i class="fas fa-arrow-left me-1"></i> Volver a la lista
                </a>
                
                <div class="card bg-light">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle-lg me-3">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h2 class="h4 mb-1">{{ $user->name }}</h2>
                                        <div class="text-muted">
                                            <i class="fas fa-envelope me-1"></i>{{ $user->email }}
                                            @if($user->phone)
                                                <span class="ms-3"><i class="fas fa-phone me-1"></i>{{ $user->phone }}</span>
                                            @endif
                                        </div>
                                        @if($user->company_name)
                                            <div class="mt-1">
                                                <i class="fas fa-building me-1"></i>
                                                <strong>{{ $user->company_name }}</strong>
                                                @if($user->country || $user->city)
                                                    <span class="text-muted">
                                                        - {{ $user->city }}{{ $user->city && $user->country ? ', ' : '' }}{{ $user->country }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                @if($user->role === 'carrier')
                                    <span class="badge bg-info fs-5">
                                        <i class="fas fa-truck me-1"></i>Trucking Company (TC)
                                    </span>
                                @else
                                    <span class="badge bg-warning fs-5">
                                        <i class="fas fa-boxes me-1"></i>Freight Forwarder  (FFD)
                                    </span>
                                @endif
                                <div class="mt-2">
                                    @if($user->verified)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Usuario Verificado
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-clock me-1"></i>No Verificado
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Documents Summary -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-center border-success">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <h3 class="mb-0">{{ $documents->where('status', 'aprobado')->count() }}</h3>
                            <small class="text-muted">Aprobados</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center border-warning">
                        <div class="card-body">
                            <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                            <h3 class="mb-0">{{ $documents->where('status', 'pendiente')->count() }}</h3>
                            <small class="text-muted">Pendientes</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center border-danger">
                        <div class="card-body">
                            <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                            <h3 class="mb-0">{{ $documents->where('status', 'rechazado')->count() }}</h3>
                            <small class="text-muted">Rechazados</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents List -->
            <div class="card">
                <div class="card-header">
                    <h3 class="h5 mb-0">
                        <i class="fas fa-file-alt me-2"></i>
                        Documentos del Usuario
                    </h3>
                </div>
                <div class="card-body">
                    @if($documents->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">El usuario aún no ha subido documentos</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Documento</th>
                                        <th>Fecha Subida</th>
                                        <th>Estado</th>
                                        <th>Comentarios</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($documents as $document)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $document->requiredDocument->name }}</strong>
                                                    @if($document->requiredDocument->description)
                                                        <br><small class="text-muted">{{ $document->requiredDocument->description }}</small>
                                                    @endif
                                                    @if($document->requiredDocument->required)
                                                        <span class="badge bg-danger ms-2">Requerido</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                {{ $document->created_at->format('d/m/Y H:i') }}
                                                @if($document->updated_at != $document->created_at)
                                                    <br><small class="text-muted">
                                                        Actualizado: {{ $document->updated_at->format('d/m/Y H:i') }}
                                                    </small>
                                                @endif
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
                                            <td>
                                                @if($document->comments)
                                                    <small>{{ Str::limit($document->comments, 50) }}</small>
                                                    @if(strlen($document->comments) > 50)
                                                        <button type="button" 
                                                                class="btn btn-link btn-sm p-0"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#commentsModal{{ $document->id }}">
                                                            Ver más
                                                        </button>
                                                    @endif
                                                @else
                                                    <small class="text-muted">Sin comentarios</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ $document->file_path }}" 
                                                       target="_blank" 
                                                       class="btn btn-outline-primary"
                                                       title="Ver documento">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    @if($document->status !== 'aprobado')
                                                        <button type="button" 
                                                                class="btn btn-outline-success"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#approveModal{{ $document->id }}"
                                                                title="Aprobar">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif
                                                    
                                                    @if($document->status !== 'rechazado')
                                                        <button type="button" 
                                                                class="btn btn-outline-danger"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#rejectModal{{ $document->id }}"
                                                                title="Rechazar">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Comments Modal -->
                                        @if($document->comments && strlen($document->comments) > 50)
                                            <div class="modal fade" id="commentsModal{{ $document->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Comentarios</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>{{ $document->comments }}</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

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
                                                            <p>¿Está seguro que desea aprobar el documento <strong>{{ $document->requiredDocument->name }}</strong>?</p>
                                                            
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
                                                            <p>¿Está seguro que desea rechazar el documento <strong>{{ $document->requiredDocument->name }}</strong>?</p>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label text-danger">
                                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                                    Motivo del rechazo (requerido)
                                                                </label>
                                                                <textarea name="comments" 
                                                                          class="form-control" 
                                                                          rows="3"
                                                                          required
                                                                          placeholder="Explique el motivo del rechazo..."></textarea>
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
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle-lg {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 28px;
}

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
</style>
@endsection
