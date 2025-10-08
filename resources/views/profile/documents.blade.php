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
                                                    <form action="{{ route('profile.upload-document') }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2">
                                                        @csrf
                                                        <input type="hidden" name="document_id" value="{{ $document->id }}">
                                                        <input type="file" name="document" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" required>
                                                        <button type="submit" class="btn btn-warning btn-sm">Reenviar</button>
                                                    </form>
                                                @else
                                                    <span class="text-muted">
                                                        <i class="fas fa-check-circle"></i> 
                                                        Documento enviado {{ $userDoc->created_at->diffForHumans() }}
                                                    </span>
                                                @endif
                                            @else
                                                <form action="{{ route('profile.upload-document') }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2">
                                                    @csrf
                                                    <input type="hidden" name="document_id" value="{{ $document->id }}">
                                                    <input type="file" name="document" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" required>
                                                    <button type="submit" class="btn btn-primary btn-sm">Subir</button>
                                                </form>
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
</style>
@endpush
