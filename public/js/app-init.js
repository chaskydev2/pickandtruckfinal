/**
 * Scripts de inicialización para la aplicación
 */

// Manejo de errores de carga de recursos (para evitar errores 404 en consola)
document.addEventListener('DOMContentLoaded', function() {
    // Añadir manejador global de errores para recursos fallidos
    window.addEventListener('error', function(event) {
        if (event.target && (event.target.tagName === 'SCRIPT' || event.target.tagName === 'LINK')) {
            console.warn('No se pudo cargar el recurso:', event.target.src || event.target.href);
            // Evita que se muestre el error en la consola (opcional)
            event.preventDefault();
        }
    }, true);
    
    // Redireccionar 404s del lado del cliente
    const handleLinkClick = function(e) {
        const href = this.getAttribute('href');
        if (href && href.startsWith('/') && !href.startsWith('//') && !href.startsWith('/assets/')) {
            // Solo para enlaces internos
            e.preventDefault();
            
            // Verificar si la ruta termina en / y redirigir sin ella (para prevenir 404)
            if (href !== '/' && href.endsWith('/')) {
                window.location.href = href.slice(0, -1);
            } else {
                window.location.href = href;
            }
        }
    };
    
    // Aplicar a todos los enlaces internos
    document.querySelectorAll('a[href^="/"]').forEach(link => {
        link.addEventListener('click', handleLinkClick);
    });
});
