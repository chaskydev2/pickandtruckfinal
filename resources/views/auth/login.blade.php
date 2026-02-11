<x-guest-layout>
    <!-- Session Status and Errors -->
    <div class="mb-4">
        <x-auth-session-status class="mb-4" :status="session('status')" />
        
        @if ($errors->any())
            <div style="background-color: #fee; border-left: 4px solid #ef4444; color: #991b1b; padding: 1rem; margin-bottom: 1rem; border-radius: 0.375rem;" role="alert">
                @foreach ($errors->all() as $error)
                    <p style="font-weight: bold; margin: 0;">{{ $error }}</p>
                @endforeach
            </div>
        @endif
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" style="display: inline-flex; align-items: center; background: transparent !important; cursor: pointer;">
                <input id="remember_me" type="checkbox" name="remember" 
                       style="width: 18px; 
                              height: 18px; 
                              cursor: pointer;
                              margin: 0;
                              vertical-align: middle;
                              accent-color: #3b82f6;">
                <span style="color: #000 !important; margin-left: 0.75rem; font-size: 0.875rem; font-weight: 600;">{{ __('Recordarme') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('¿Olvidaste tu contraseña?') }}
                </a>
            @endif

            <x-primary-button class="ml-3">
                {{ __('Ingresar') }}
            </x-primary-button>
        </div>
        
        <!-- Enlace de registro -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-300">
                {{ __('¿No tienes cuenta?') }}
                <a href="{{ route('register') }}" class="font-bold text-blue-400 hover:text-blue-300 transition-colors duration-200">
                    {{ __('Regístrate') }}
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>

<style>
    body {
        background-color: #1a2b4c !important; /* Azul oscuro */
    }
    
    .bg-gray-100 {
        background-color: #1a2b4c !important;
    }
    
    .text-gray-600, .text-gray-900 {
        color: #f0f0f0 !important;
    }
    
    /* Aumentar el contraste de los elementos para mejor legibilidad */
    .text-sm {
        color: #f0f0f0 !important;
    }
    
    /* Mantener el card del formulario con fondo claro para contraste */
    .bg-white {
        background-color: #ffffff !important;
    }
    
    /* Hacer que los enlaces sean más visibles en el fondo oscuro */
    a {
        color: #4299e1 !important;
    }
    
    a:hover {
        color: #63b3ed !important;
    }
    
    /* Asegurar que el texto dentro del formulario sea negro */
    form label {
        color: #000000 !important;
    }
    
    /* Asegurar que los inputs tengan fondo blanco */
    input[type="email"],
    input[type="password"],
    input[type="text"] {
        background-color: #ffffff !important;
        color: #000000 !important;
    }
    
    /* Restaurar color de texto en el formulario */
    .w-full.sm\:max-w-md .text-gray-600 {
        color: #4a5568 !important;
    }
    
    /* Asegurar que los textos en el card sean negros */
    .w-full.sm\:max-w-md label,
    .w-full.sm\:max-w-md span:not(.text-white),
    .w-full.sm\:max-w-md p:not(.text-white) {
        color: #000000 !important;
    }
    
    /* Estilo específico para botones de login */
    .ml-3 button,
    button.ml-3,
    .ml-3 .bg-gray-800,
    .ml-4 button,
    button.ml-4,
    .inline-flex,
    .bg-gray-800,
    .bg-indigo-600,
    button[type="submit"] {
        background-color: #1a2b4c !important;
        color: #ffffff !important;
        border-color: #1a2b4c !important;
        transition: background-color 0.3s ease;
    }
    
    .ml-3 button:hover,
    button.ml-3:hover,
    .ml-4 button:hover,
    button.ml-4:hover,
    .inline-flex:hover,
    .bg-gray-800:hover,
    .bg-indigo-600:hover,
    button[type="submit"]:hover {
        background-color: #2a3b5c !important;
        color: #ffffff !important;
    }
    
    /* Estilos para x-primary-button */
    .bg-gray-800, 
    .bg-indigo-600 {
        background-color: #1a2b4c !important;
        color: #ffffff !important;
    }
    
    /* Ajustar específicamente el texto del checkbox "Recuérdame" */
    label[for="remember_me"] span {
        color: #000000 !important;
    }
    
    /* Corregir también el color del texto para el enlace de olvidaste tu contraseña */
    .flex.items-center.justify-end a {
        color: #4299e1 !important;
    }
    
    /* Sobrescribir el selector general para asegurar que "Recuérdame" sea negro */
    .w-full.sm\:max-w-md label span.text-sm.text-gray-600 {
        color: #000000 !important;
    }
</style>
