// Script para actualizar tabla de ofertas recibidas en tiempo real (usando polling)
(function() {
    'use strict';
    
    // Verificar si estamos en una página de oferta
    const tableBody = document.querySelector('.table tbody');
    if (!tableBody || !tableBody.querySelector('tr[data-bid-id]')) {
        return; // No hay tabla de bids
    }
    
    // Obtener la URL actual
    const currentUrl = window.location.pathname;
    
    // Función para actualizar las filas de la tabla
    async function actualizarTabla() {
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
            
            // Buscar el tbody en la respuesta
            const newTbody = tmp.querySelector('.table tbody');
            if (!newTbody) return;
            
            // Crear un mapa de filas existentes
            const existingRows = {};
            tableBody.querySelectorAll('tr[data-bid-id]').forEach(row => {
                existingRows[row.dataset.bidId] = row;
            });
            
            // Procesar cada fila nueva
            newTbody.querySelectorAll('tr[data-bid-id]').forEach(newRow => {
                const bidId = newRow.dataset.bidId;
                const existingRow = existingRows[bidId];
                
                if (existingRow) {
                    // Si la fila existe, verificar si cambió
                    if (existingRow.innerHTML !== newRow.innerHTML) {
                        existingRow.innerHTML = newRow.innerHTML;
                        // Animación de actualización
                        existingRow.classList.add('table-warning');
                        setTimeout(() => existingRow.classList.remove('table-warning'), 700);
                    }
                    // Marcar como procesada
                    delete existingRows[bidId];
                } else {
                    // Fila nueva - agregarla
                    tableBody.appendChild(newRow.cloneNode(true));
                    newRow.classList.add('table-success');
                    setTimeout(() => newRow.classList.remove('table-success'), 700);
                }
            });
            
            // Eliminar filas que ya no existen
            Object.values(existingRows).forEach(row => row.remove());
            
            // Actualizar el contador
            actualizarContador();
            
        } catch (e) {
            // Silenciar errores para no romper la UI
            console.error('Error actualizando tabla de ofertas:', e);
        }
    }
    
    function actualizarContador() {
        const cardTitle = document.querySelector('.card-title');
        if (!cardTitle) return;
        
        const count = tableBody.querySelectorAll('tr[data-bid-id]').length;
        const text = cardTitle.textContent;
        
        // Actualizar el contador en el título
        if (text.includes('(')) {
            cardTitle.textContent = text.replace(/\(\d+\)/, `(${count})`);
        }
    }
    
    // Actualizar cada segundo (igual que el dashboard)
    setInterval(actualizarTabla, 1000);
    
    console.log('Actualización automática de tabla de ofertas activada');
})();
