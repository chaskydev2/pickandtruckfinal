<x-guest-layout>
    <div class="max-w-md w-full p-8 bg-white rounded-lg shadow-md">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">
                Cuenta Bloqueada
            </h2>
            <p class="text-gray-700 mb-6">
                Lo sentimos, su cuenta ha sido bloqueada. Si cree que esto es un error, por favor contáctese con el administrador.
            </p>
        </div>
        
        <div class="mt-4">
            <a href="{{ $contactUrl }}" class="btn btn-primary w-100 mb-2">
                Contactar al administrador
            </a>

            <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100">
                Volver al inicio de sesión
            </a>
        </div>
    </div>
</x-guest-layout>
