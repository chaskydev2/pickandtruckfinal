<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

<style>
    /* Estilo específico para botones en páginas de autenticación */
    body:has(.auth-background) button[type="submit"],
    body:has(.auth-background) .inline-flex {
        background-color: #1a2b4c !important;
        color: #ffffff !important;
        transition: background-color 0.3s ease;
    }
    
    body:has(.auth-background) button[type="submit"]:hover,
    body:has(.auth-background) .inline-flex:hover {
        background-color: #2a3b5c !important;
    }
</style>
