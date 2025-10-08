@extends('layouts.app')

@php
    use App\Helpers\UserHelper;
    
    // Obtener la inicial del nombre de la empresa
    $initial = UserHelper::getInitials($empresa->nombre);
    $bgColor = UserHelper::getRandomColor();
@endphp

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @if(session('success'))
                <div class="alert alert-success mb-4">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="card border-0 rounded-lg overflow-hidden shadow">
                <!-- Encabezado de perfil con imagen de fondo y logo -->
                <div class="card-header position-relative p-0">
                    <div class="text-white py-5 bg-nav">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="empresa-logo-container rounded-circle bg-white p-2 shadow position-relative" style="width: 120px; height: 120px; display: flex; align-items: center; justify-content: center;">
                                        @if($empresa->logo)
                                            <img src="{{ $empresa->logo }}" alt="{{ $empresa->nombre }}" class="img-fluid" style="max-width: 100px; max-height: 100px; object-fit: contain; border-radius: 50%;">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold"
                                                style="width:100px; height:100px; background-color: {{ $bgColor }}; font-size:40px;">
                                                {{ $initial }}
                                            </div>
                                        @endif
                                        
                                        <!-- Botón simple para editar el logo -->
                                        <a href="{{ route('empresas.edit') }}#logo" class="btn btn-sm btn-primary position-absolute" style="bottom:0; right:0; z-index:20;">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col">
                                    <h1 class="h3 mb-1 text-white">{{ $empresa->nombre }}</h1>
                                    <div class="d-inline-block px-3 py-1 mb-2 rounded-pill bg-white bg-opacity-25">
                                        <span class="text-white fw-medium">
                                            <i class="fas {{ $empresa->user->role === 'carrier' ? 'fa-truck' : 'fa-warehouse' }} me-1"></i>
                                            {{ $empresa->user->role === 'carrier' ? 'Transportista' : 'Forwarder' }}
                                        </span>
                                    </div>
                                    @if($empresa->verificada)
                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Empresa Verificada</span>
                                    @endif
                                </div>
                                <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                    <a href="{{ route('empresas.edit') }}" class="btn btn-primary fw-bold">
                                        <i class="fas fa-edit me-1"></i> Editar Información
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contenido del perfil -->
                <div class="card-body py-4">
                    <div class="row">
                        <!-- Columna de información principal -->
                        <div class="col-md-8">
                            <div class="mb-4">
                                <h2 class="h5 fw-bold border-bottom pb-2 mb-3">Acerca de la Empresa</h2>
                                <p class="mb-0">{{ $empresa->descripcion ?? 'No hay descripción disponible.' }}</p>
                            </div>

                            <!-- Estadísticas -->
                            <div class="mb-4">
                                <h2 class="h5 fw-bold border-bottom pb-2 mb-3">Actividad</h2>
                                <div class="row g-3 text-center">
                                    <div class="col-4">
                                        <div class="p-3 border rounded-3 bg-light h-100 shadow-sm">
                                            <h3 class="h2 mb-1 fw-bold text-primary">{{ $empresa->user->ofertasCarga->count() }}</h3>
                                            <p class="small text-muted mb-0">Ofertas de Carga</p>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-3 border rounded-3 bg-light h-100 shadow-sm">
                                            <h3 class="h2 mb-1 fw-bold text-primary">{{ $empresa->user->ofertasRuta->count() }}</h3>
                                            <p class="small text-muted mb-0">Ofertas de Ruta</p>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-3 border rounded-3 bg-light h-100 shadow-sm">
                                            <h3 class="h2 mb-1 fw-bold text-primary">{{ $empresa->user->bids->count() }}</h3>
                                            <p class="small text-muted mb-0">Pujas Realizadas</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna de detalles de contacto -->
                        <div class="col-md-4">
                            <div class="card h-100 border-0 bg-light shadow-sm">
                                <div class="card-body">
                                    <h2 class="h5 fw-bold mb-3">Información de Contacto</h2>
                                    
                                    <ul class="list-unstyled">
                                        <li class="mb-3">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <i class="fas fa-envelope text-primary fa-fw me-2"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">Correo Electrónico</div>
                                                    <div>{{ $empresa->user->email }}</div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="mb-3">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <i class="fas fa-phone text-primary fa-fw me-2"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">Teléfono</div>
                                                    <div>{{ $empresa->telefono ?? 'No disponible' }}</div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="mb-3">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <i class="fas fa-map-marker-alt text-primary fa-fw me-2"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">Dirección</div>
                                                    <div>{{ $empresa->direccion ?? 'No disponible' }}</div>
                                                </div>
                                            </div>
                                        </li>
                                        @if($empresa->sitio_web)
                                        <li class="mb-3">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <i class="fas fa-globe text-primary fa-fw me-2"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">Sitio Web</div>
                                                    <div>
                                                        <a href="{{ $empresa->sitio_web }}" target="_blank" class="text-primary">
                                                            {{ $empresa->sitio_web }}
                                                            <i class="fas fa-external-link-alt fa-xs ms-1"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        @endif
                                        <li>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <i class="fas fa-calendar text-primary fa-fw me-2"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">Miembro desde</div>
                                                    <div>{{ $empresa->created_at->format('d/m/Y') }}</div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .empresa-logo-container {
        border: 3px solid white;
        margin-top: -30px;
        z-index: 10;
        position: relative;
    }
    
    /* Estilos eliminados para simplificar el botón */
    
    @media (max-width: 767px) {
        .empresa-logo-container {
            margin: 0 auto 1rem;
        }
    }
</style>
@endpush
