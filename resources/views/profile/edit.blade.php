@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Navegación de pestañas -->
            <ul class="nav nav-tabs profile-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link profile-tab active" href="#profile" data-bs-toggle="tab">Perfil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link profile-tab" href="#security" data-bs-toggle="tab">Seguridad</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link profile-tab" href="{{ route('profile.documents') }}">Documentos</a>
                </li>
            </ul>

            <!-- Contenido de las pestañas -->
            <div class="tab-content">
                <div class="tab-pane fade show active" id="profile">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="h4 mb-0">{{ __('Editar Perfil') }}</h2>
                        </div>
                        <div class="card-body">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="security">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="h4 mb-0">{{ __('Actualizar Contraseña') }}</h2>
                        </div>
                        <div class="card-body">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                    
                    <!-- Se ha eliminado la tarjeta de "Eliminar Cuenta" -->
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
    
    /* Estilos para los inputs del perfil */
    .tab-content input[type="text"],
    .tab-content input[type="email"],
    .tab-content input[type="password"],
    .tab-content textarea {
        background-color: #ffffff !important;
        color: #333333 !important;
        border: 1px solid #d1d5db !important;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        width: 100%;
    }
    
    .tab-content .form-control:focus {
        background-color: #ffffff !important;
        border-color: var(--color-primary) !important;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25) !important;
    }
    
    /* Estilos para etiquetas */
    .tab-content label {
        color: #333333 !important;
        font-weight: 500;
    }
</style>
@endpush
