/**
 * Manejo de pestañas de perfil
 */
document.addEventListener('DOMContentLoaded', function() {
    // Habilitar tabs de Bootstrap
    const triggerTabList = [].slice.call(document.querySelectorAll('.profile-tab'));
    
    // Activar la pestaña correspondiente cuando se carga la página
    if (window.location.hash) {
        const activeTab = document.querySelector('.profile-tab[href="' + window.location.hash + '"]');
        if (activeTab) {
            activeTab.click();
        }
    }
    
    triggerTabList.forEach(function (triggerEl) {
        const tabTrigger = new bootstrap.Tab(triggerEl);
        
        triggerEl.addEventListener('click', function (event) {
            if (this.getAttribute('href').startsWith('#')) {
                event.preventDefault();
                tabTrigger.show();
            }
        });
    });
});
