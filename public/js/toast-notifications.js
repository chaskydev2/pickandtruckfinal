/**
 * Sistema de Notificaciones Toast Flotantes
 * Muestra notificaciones visibles en toda la página, incluso con scroll
 */

// Contenedor de toasts
let toastContainer = null;

/**
 * Inicializa el contenedor de toasts
 */
function initToastContainer() {
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container';
        document.body.appendChild(toastContainer);
    }
    return toastContainer;
}

/**
 * Muestra una notificación toast
 * @param {string} title - Título de la notificación
 * @param {string} message - Mensaje de la notificación
 * @param {string} type - Tipo: 'success', 'danger', 'warning', 'info', 'document'
 * @param {number} duration - Duración en milisegundos (0 = no auto-cerrar)
 */
window.showToastNotification = function(title, message, type = 'info', duration = 5000) {
    const container = initToastContainer();
    
    // Crear el elemento toast
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    
    // Determinar el icono según el tipo
    let icon = 'fa-bell';
    let iconColor = '#3b82f6';
    
    switch(type) {
        case 'success':
            icon = 'fa-check-circle';
            iconColor = '#10b981';
            break;
        case 'danger':
        case 'error':
            icon = 'fa-exclamation-circle';
            iconColor = '#ef4444';
            break;
        case 'warning':
            icon = 'fa-exclamation-triangle';
            iconColor = '#f59e0b';
            break;
        case 'document':
            icon = 'fa-file-alt';
            iconColor = '#8b5cf6';
            break;
        case 'info':
        default:
            icon = 'fa-info-circle';
            iconColor = '#3b82f6';
            break;
    }
    
    // Construir el HTML del toast
    toast.innerHTML = `
        <div class="toast-icon" style="color: ${iconColor};">
            <i class="fas ${icon}"></i>
        </div>
        <div class="toast-content">
            <div class="toast-title">${title}</div>
            <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close" aria-label="Cerrar">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Agregar al contenedor
    container.appendChild(toast);
    
    // Animación de entrada
    requestAnimationFrame(() => {
        toast.classList.add('toast-show');
    });
    
    // Botón de cerrar
    const closeBtn = toast.querySelector('.toast-close');
    closeBtn.addEventListener('click', () => {
        removeToast(toast);
    });
    
    // Auto-cerrar si se especifica duración
    if (duration > 0) {
        setTimeout(() => {
            removeToast(toast);
        }, duration);
    }
    
    // Reproducir sonido de notificación si está disponible
    try {
        playNotificationSound();
    } catch (e) {
        // Silenciar errores de audio
    }
    
    return toast;
};

/**
 * Remueve un toast con animación
 */
function removeToast(toast) {
    toast.classList.remove('toast-show');
    toast.classList.add('toast-hide');
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 300);
}

/**
 * Reproduce un sonido de notificación (opcional)
 */
function playNotificationSound() {
    // Puedes agregar un archivo de sonido aquí si lo deseas
    // const audio = new Audio('/sounds/notification.mp3');
    // audio.volume = 0.3;
    // audio.play().catch(() => {}); // Ignorar errores
}

/**
 * Solicita permisos de notificación del navegador
 */
function requestNotificationPermission() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                console.log('Permisos de notificación concedidos');
            }
        });
    }
}

// Solicitar permisos de notificación al primer click del usuario
document.addEventListener('click', () => {
    requestNotificationPermission();
}, { once: true });

/**
 * Muestra toast simple (alias)
 */
window.showToast = function(message, type = 'info', duration = 4000) {
    return window.showToastNotification('Notificación', message, type, duration);
};

/**
 * Shortcuts para tipos específicos
 */
window.showSuccessToast = function(message, duration = 4000) {
    return window.showToastNotification('Éxito', message, 'success', duration);
};

window.showErrorToast = function(message, duration = 5000) {
    return window.showToastNotification('Error', message, 'danger', duration);
};

window.showWarningToast = function(message, duration = 5000) {
    return window.showToastNotification('Advertencia', message, 'warning', duration);
};

window.showInfoToast = function(message, duration = 4000) {
    return window.showToastNotification('Información', message, 'info', duration);
};

// Inicializar al cargar la página
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initToastContainer);
} else {
    initToastContainer();
}

// Log para confirmar que el sistema está cargado
console.log('Sistema de notificaciones toast inicializado');
