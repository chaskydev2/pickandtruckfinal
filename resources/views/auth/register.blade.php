<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nombre')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
            <div class="form-text text-muted mt-1 text-gray-600 hover:text-gray-900 text-sm">Ingresa tu nombre completo.
            </div>
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <div class="form-text text-muted mt-1 text-gray-600 hover:text-gray-900 text-sm">
                Debe ser un correo válido y único. Ejemplo: usuario@email.com
            </div>
        </div>

        <!-- Role Selection -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('Tipo de cuenta')" />
            <div class="relative">
                <select id="role" name="role" class="form-select mt-1" required>
                    <option value="">Seleccione un tipo de cuenta</option>
                    <option value="{{ App\Models\User::ROLE_FORWARDER }}"
                        {{ old('role') == App\Models\User::ROLE_FORWARDER ? 'selected' : '' }}>Forwarder</option>
                    <option value="{{ App\Models\User::ROLE_CARRIER }}"
                        {{ old('role') == App\Models\User::ROLE_CARRIER ? 'selected' : '' }}>Carrier</option>
                </select>
                <style>
                    select#role {
                        color: #1f2937 !important;
                    }

                    select#role option {
                        background-color: white;
                        color: #1f2937;
                        padding: 0.5rem;
                    }
                </style>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
            <div class="form-text text-muted mt-1 text-gray-600 hover:text-gray-900 text-sm">
                Elige el tipo de cuenta que mejor se adapte a tu perfil.
            </div>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <div class="form-text text-muted mt-1 text-gray-600 hover:text-gray-900 text-sm">
                La contraseña debe tener al menos 8 caracteres.
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            <div class="form-text text-muted mt-1 text-gray-600 hover:text-gray-900 text-sm">
                Repite la contraseña exactamente igual para confirmar.
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}">
                {{ __('¿Ya estás registrado?') }}
            </a>

            <x-primary-button class="ml-4">
                {{ __('Registrarse') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

<style>
    body {
        background-color: #1a2b4c !important;
        /* Azul oscuro */
    }

    .bg-gray-100 {
        background-color: #1a2b4c !important;
    }

    .text-gray-600,
    .text-gray-900 {
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

    /* Estilo específico para botones de registro */
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
</style>
