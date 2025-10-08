@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
                <div class="card">
                <div class="card-header">{{ __('Registro') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Información del Usuario -->
                        <h5 class="mb-3">Información Personal</h5>

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Nombre') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Correo Electrónico') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Contraseña') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirmar Contraseña') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <!-- Información de la Empresa -->
                        <hr class="my-4">
                        <h5 class="mb-3">Información de la Empresa</h5>

                        <div class="row mb-3">
                            <label for="empresa_nombre" class="col-md-4 col-form-label text-md-end">{{ __('Nombre de la Empresa') }}</label>

                            <div class="col-md-6">
                                <input id="empresa_nombre" type="text" class="form-control @error('empresa_nombre') is-invalid @enderror" name="empresa_nombre" value="{{ old('empresa_nombre') }}" required>

                                @error('empresa_nombre')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="empresa_logo" class="col-md-4 col-form-label text-md-end">{{ __('Logo de la Empresa') }}</label>

                            <div class="col-md-6">
                                <input id="empresa_logo" type="file" class="form-control @error('empresa_logo') is-invalid @enderror" name="empresa_logo" accept="image/*">
                                <div class="form-text">Formatos aceptados: JPG, PNG, GIF. Max: 2MB</div>

                                @error('empresa_logo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="empresa_descripcion" class="col-md-4 col-form-label text-md-end">{{ __('Descripción') }}</label>

                            <div class="col-md-6">
                                <textarea id="empresa_descripcion" class="form-control @error('empresa_descripcion') is-invalid @enderror" name="empresa_descripcion" rows="3">{{ old('empresa_descripcion') }}</textarea>

                                @error('empresa_descripcion')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="empresa_telefono" class="col-md-4 col-form-label text-md-end">{{ __('Teléfono') }}</label>

                            <div class="col-md-6">
                                <input id="empresa_telefono" type="text" class="form-control @error('empresa_telefono') is-invalid @enderror" name="empresa_telefono" value="{{ old('empresa_telefono') }}">

                                @error('empresa_telefono')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="empresa_direccion" class="col-md-4 col-form-label text-md-end">{{ __('Dirección') }}</label>

                            <div class="col-md-6">
                                <input id="empresa_direccion" type="text" class="form-control @error('empresa_direccion') is-invalid @enderror" name="empresa_direccion" value="{{ old('empresa_direccion') }}">

                                @error('empresa_direccion')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="empresa_sitio_web" class="col-md-4 col-form-label text-md-end">{{ __('Sitio Web') }}</label>

                            <div class="col-md-6">
                                <input id="empresa_sitio_web" type="text" class="form-control @error('empresa_sitio_web') is-invalid @enderror" name="empresa_sitio_web" value="{{ old('empresa_sitio_web') }}">

                                @error('empresa_sitio_web')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Registrarse') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
