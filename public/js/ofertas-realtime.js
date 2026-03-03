// Actualización en tiempo real de ofertas recibidas
(function() {
    'use strict';
    if (!document.getElementById('bids-owner-card')) return;
    // Recargar la página cada 8 segundos para mostrar nuevas ofertas
    setInterval(function() { window.location.reload(); }, 8000);
})();
