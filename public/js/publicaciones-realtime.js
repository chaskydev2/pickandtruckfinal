// Script para actualizar publicaciones en tiempo real (usando polling)
(function() {
    'use strict';
    
    // Verificar si estamos en una página de publicaciones (index)
    const publicationsList = document.querySelector('#publications-list');
    if (!publicationsList) {
        return; // No estamos en la página de publicaciones
    }
    
    // Obtener la URL actual con todos sus parámetros
    const currentUrl = window.location.href;
    
    // Función para actualizar las publicaciones
    async function actualizarPublicaciones() {
        try {
            const res = await fetch(currentUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!res.ok) return;
            
            const text = await res.text();
            const tmp = document.createElement('div');
            tmp.innerHTML = text;
            
            // Buscar el contenedor de publicaciones en la respuesta
            const newPublicationsList = tmp.querySelector('#publications-list');
            if (!newPublicationsList) return;
            
            // Crear un mapa de tarjetas existentes
            const existingCards = {};
            publicationsList.querySelectorAll('[data-oferta-id]').forEach(card => {
                existingCards[card.dataset.ofertaId] = card;
            });
            
            // Procesar cada tarjeta nueva
            newPublicationsList.querySelectorAll('[data-oferta-id]').forEach(newCard => {
                const ofertaId = newCard.dataset.ofertaId;
                const existingCard = existingCards[ofertaId];
                
                if (existingCard) {
                    // Si la tarjeta existe, verificar si cambió
                    if (existingCard.innerHTML !== newCard.innerHTML) {
                        existingCard.innerHTML = newCard.innerHTML;
                        // Animación de actualización
                        existingCard.style.transition = 'background-color 0.3s';
                        existingCard.style.backgroundColor = '#fff3cd';
                        setTimeout(() => {
                            existingCard.style.backgroundColor = '';
                        }, 700);
                    }
                    // Marcar como procesada
                    delete existingCards[ofertaId];
                } else {
                    // Tarjeta nueva - agregarla al inicio
                    publicationsList.insertBefore(newCard.cloneNode(true), publicationsList.firstChild);
                    // Animación de nueva tarjeta
                    const addedCard = publicationsList.firstChild;
                    addedCard.style.transition = 'background-color 0.3s';
                    addedCard.style.backgroundColor = '#d1e7dd';
                    setTimeout(() => {
                        addedCard.style.backgroundColor = '';
                    }, 700);
                }
            });
            
            // Eliminar tarjetas que ya no existen
            Object.values(existingCards).forEach(card => {
                card.style.transition = 'opacity 0.3s';
                card.style.opacity = '0';
                setTimeout(() => card.remove(), 300);
            });
            
            // Actualizar el contador de "Mis Publicaciones" si existe
            actualizarContador(tmp);
            
        } catch (e) {
            // Silenciar errores para no romper la UI
            console.error('Error actualizando publicaciones:', e);
        }
    }
    
    function actualizarContador(tmpDoc) {
        const currentBadge = document.querySelector('.nav-link .badge');
        if (!currentBadge) return;
        
        const newBadge = tmpDoc.querySelector('.nav-link .badge');
        if (newBadge && currentBadge.textContent !== newBadge.textContent) {
            currentBadge.textContent = newBadge.textContent;
            currentBadge.className = newBadge.className;
        }
    }
    
    // Actualizar cada segundo (igual que el dashboard)
    setInterval(actualizarPublicaciones, 1000);
    
    console.log('Actualización automática de publicaciones activada');
})();
