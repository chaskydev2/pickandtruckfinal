/**
 * Sistema de actualización automática de la página de documentos
 * Detecta cuando el admin aprueba/rechaza documentos y recarga la página automáticamente
 */
class DocumentAutoRefresh {
    constructor() {
        this.checkInterval = null;
        this.currentHash = null;
        this.checkUrl = '/profile/check-document-status';
        this.pollInterval = 5000; // Verificar cada 5 segundos
        this.isChecking = false;
    }

    start() {
        console.log('🔄 Iniciando verificación automática de documentos cada', this.pollInterval / 1000, 'segundos');
        
        // Primera verificación para obtener el hash inicial
        this.checkDocumentStatus(true);
        
        // Iniciar verificación periódica
        this.checkInterval = setInterval(() => {
            this.checkDocumentStatus(false);
        }, this.pollInterval);
    }

    stop() {
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
            this.checkInterval = null;
            console.log('🛑 Verificación automática detenida');
        }
    }

    checkDocumentStatus(isInitialCheck = false) {
        if (this.isChecking) return;
        
        this.isChecking = true;

        fetch(this.checkUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (isInitialCheck) {
                // Primera verificación: solo guardar el hash
                this.currentHash = data.hash;
                console.log('✅ Hash inicial de documentos registrado');
            } else {
                // Verificaciones posteriores: comparar hash
                if (this.currentHash !== null && data.hash !== this.currentHash) {
                    console.log('🔔 ¡Cambio detectado en documentos! Recargando página...');
                    console.log('Hash anterior:', this.currentHash);
                    console.log('Hash nuevo:', data.hash);
                    
                    // Mostrar mensaje antes de recargar
                    this.showReloadMessage(data.verified);
                    
                    // Recargar la página después de un breve delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            }
        })
        .catch(error => {
            console.error('Error al verificar estado de documentos:', error);
        })
        .finally(() => {
            this.isChecking = false;
        });
    }

    showReloadMessage(isVerified) {
        // Crear elemento de notificación
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 14px;
            animation: slideIn 0.3s ease-out;
        `;
        
        const message = isVerified 
            ? '✓ ¡Tus documentos han sido actualizados! Recargando...'
            : '↻ Actualizando estado de documentos...';
        
        notification.textContent = message;
        
        // Agregar animación
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
        document.body.appendChild(notification);
    }
}

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.documentAutoRefresh = new DocumentAutoRefresh();
        window.documentAutoRefresh.start();
    });
} else {
    window.documentAutoRefresh = new DocumentAutoRefresh();
    window.documentAutoRefresh.start();
}
