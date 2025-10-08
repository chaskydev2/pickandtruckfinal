/**
 * Script para mejorar la navegación al perfil de empresa
 */
document.addEventListener('DOMContentLoaded', function() {
    // Obtener los enlaces a Mi Empresa
    const empresaLinks = document.querySelectorAll('#empresa-link, #responsive-empresa-link');
    
    // Agregar manejador de eventos a cada enlace
    empresaLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Prevenir la navegación normal
            e.preventDefault();
            
            // Agregar un parámetro de consulta para evitar la caché
            const timestamp = new Date().getTime();
            const url = this.getAttribute('href') + '?t=' + timestamp;
            
            // Navegar a la URL
            window.location.href = url;
        });
    });
});
