// En public/js/notifications.js

if (typeof window.RealTimeNotifications === 'undefined') {
    class RealTimeNotifications {
        // ... (el constructor y otros métodos no cambian) ...
        constructor(options = {}) {
            // Configuración predeterminada
            this.options = Object.assign({
                notificationBadgeSelector: '#notification-badge',
                notificationListSelector: '#notification-list',
                notificationCountSelector: '#notification-count',
                markAsReadUrl: '/notifications/:id/read',
                checkNotificationsUrl: '/notifications/check',
                updateInterval: 3000, // 3 segundos para actualizaciones más rápidas
                playSound: false // Desactivar sonido por defecto
            }, options);

            // Elementos del DOM
            this.notificationBadge = document.querySelector(this.options.notificationBadgeSelector);
            this.notificationList = document.querySelector(this.options.notificationListSelector);
            this.notificationCount = document.querySelector(this.options.notificationCountSelector);
            this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            // Estado
            this.lastUpdateTime = new Date();
            this.pageLoadTime = new Date(); // Timestamp al cargar la página; solo disparar toasts para notifs creadas DESPUÉS de esto
            this.notificationsCount = this.notificationBadge ? parseInt(this.notificationBadge.textContent || '0') : 0;
            this.lastNotificationIds = new Set(); // Guardar IDs de notificaciones ya procesadas
            this.isProcessingNotifications = false;
            this.isInitialCheck = true; // Primera carga: solo registrar IDs existentes, sin mostrar toasts

            // Asegurar que existe el token CSRF
            if (!this.csrfToken) {
                console.warn('CSRF token no encontrado. Las notificaciones podrían no funcionar correctamente.');
            }

            // Inicializar
            this.init();
        }

        init() {
            // Iniciar verificaciones periódicas
            this.startCheckingForNotifications();
        }

        startCheckingForNotifications() {
            console.log('✅ Sistema de notificaciones iniciado - Verificando cada', this.options.updateInterval / 1000, 'segundos');
            
            // Primera verificación inmediata
            setTimeout(() => {
                this.checkForNotifications();
            }, 500);

            // Configurar verificación periódica
            this.checkInterval = setInterval(() => this.checkForNotifications(), this.options.updateInterval);
        }

        stopCheckingForNotifications() {
            if (this.checkInterval) {
                clearInterval(this.checkInterval);
            }
        }

        checkForNotifications() {
            // Si no hay token CSRF, detenemos la petición
            if (!this.csrfToken) {
                console.warn('No se puede verificar notificaciones: token CSRF no encontrado');
                return;
            }

            // Añadir un parámetro para evitar caché
            const nocacheParam = `_nc=${new Date().getTime()}`;
            const url = `${this.options.checkNotificationsUrl}?${nocacheParam}`;

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                },
                credentials: 'same-origin' // Asegurar que las cookies se envíen
            })
                .then(response => {
                    if (!response.ok) {
                        // Si hay un error, no intentar parsear JSON
                        console.warn(`Error al verificar notificaciones: ${response.status} ${response.statusText}`);
                        return { count: 0, notifications: [] };
                    }
                    
                    // Verificar que la respuesta es JSON
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.includes("application/json")) {
                        return response.json();
                    } else {
                        console.warn('La respuesta no es JSON, se recibió:', contentType);
                        return { count: 0, notifications: [] };
                    }
                })
                .then(data => {
                    if (this.isProcessingNotifications) return;
                    this.isProcessingNotifications = true;

                    console.log('🔔 Verificación de notificaciones:', {
                        'Nuevas no leídas': data.count,
                        'Anterior': this.notificationsCount,
                        'Total recibidas': data.notifications?.length || 0
                    });

                    // Detectar notificaciones realmente nuevas por ID
                    const newNotifications = [];
                    if (data.notifications && Array.isArray(data.notifications)) {
                        data.notifications.forEach(notification => {
                            if (!this.lastNotificationIds.has(notification.id)) {
                                this.lastNotificationIds.add(notification.id);
                                // Solo es realmente nueva si fue creada DESPUÉS de que cargó la página y no está leída
                                const createdAt = notification.created_at ? new Date(notification.created_at) : null;
                                const isReallyNew = !notification.read_at && createdAt && createdAt >= this.pageLoadTime;
                                if (isReallyNew) {
                                    newNotifications.push(notification);
                                }
                            }
                        });
                    }

                    // Actualizar el contador de notificaciones
                    this.updateNotificationCount(data.count);
                    
                    // Actualizar la lista de notificaciones si está disponible
                    if (this.notificationList && data.notifications) {
                        this.updateNotificationList(data.notifications);
                    }

                    // Procesar nuevas notificaciones
                    if (newNotifications.length > 0) {
                        if (this.isInitialCheck) {
                            // Primera carga: solo registrar IDs previas, nunca disparar toasts
                            console.log('📋 Carga inicial: registrando', newNotifications.length, 'notificaciones existentes sin mostrar toasts.');
                        } else {
                            console.log('✨ ¡Nuevas notificaciones detectadas!', newNotifications.length);
                            newNotifications.forEach(notification => {
                                this.handleNewNotification(notification);
                            });

                            // Reproducir sonido si está habilitado
                            if (this.options.playSound) {
                                this.playNotificationSound();
                            }
                        }
                    }

                    // Actualizar el contador guardado
                    this.notificationsCount = data.count;
                    this.isInitialCheck = false; // A partir de aquí, notificaciones nuevas sí muestran toast
                    this.isProcessingNotifications = false;
                })
                .catch(error => {
                    console.error('Error al verificar notificaciones:', error);
                    // No detenemos el intervalo para seguir intentando
                });
        }

        updateNotificationCount(count) {
            // Actualizar el contador en el badge
            // Ensure we cap the display to 99+
            const displayCount = count > 99 ? '99+' : String(count);
            if (this.notificationBadge) {
                if (count > 0) {
                    this.notificationBadge.textContent = displayCount;
                    this.notificationBadge.classList.remove('hidden');
                } else {
                    this.notificationBadge.classList.add('hidden');
                }
            } else {
                // If badge element doesn't exist yet and we have notifications, create it
                if (count > 0) {
                    const trigger = document.getElementById('notifications-button');
                    if (trigger) {
                        const span = document.createElement('span');
                        span.id = 'notification-badge';
                        span.className = 'notif-badge';
                        span.textContent = displayCount;
                        trigger.appendChild(span);
                        this.notificationBadge = span;
                    }
                }
            }

            // Actualizar cualquier otro contador en la página
            if (this.notificationCount) {
                this.notificationCount.textContent = count;
            }
        }

        // --- MÉTODO MODIFICADO ---
        updateNotificationList(notifications) {
            // AÑADIMOS UN LOG PARA VER SI LA FUNCIÓN SE EJECUTA
            console.log('updateNotificationList fue llamada con:', notifications);

            if (!this.notificationList) {
                console.error('El elemento #notification-list no se encontró en el DOM.');
                return;
            }

            try {
                if (!notifications || notifications.length === 0) {
                    this.notificationList.innerHTML = `
                        <div class="px-4 py-3 text-sm text-center text-secondary">
                            No hay notificaciones nuevas
                        </div>`;
                    return;
                }

                const notificationsHtml = notifications.map(notification => {
                    const data = notification.data || {};
                    const url = data.url || '#';
                    const icon = data.icon || 'fas fa-bell text-primary';
                    const title = data.title || '';
                    const message = data.message || 'Nueva notificación';
                    const isReadClass = notification.read_at ? 'text-gray-500' : 'font-semibold';
                    const time = notification.timeAgo || '';

                    let actionsHtml = '';
                    if (data.actions && Array.isArray(data.actions)) {
                        actionsHtml += '<div class="mt-2 text-end border-top pt-2">';
                        data.actions.forEach(action => {
                            const btnClass = action.class || 'btn-secondary';
                            const btnText = action.text || 'Acción';
                            const actionUrl = action.url || '#';

                            if (action.is_delete) {
                                actionsHtml += `
                                    <form action="${actionUrl}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta publicación?');">
                                        <input type="hidden" name="_token" value="${this.csrfToken}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm ${btnClass}">${btnText}</button>
                                    </form>
                                `;
                            } else {
                                actionsHtml += `<a href="${actionUrl}" class="btn btn-sm ${btnClass}">${btnText}</a> `;
                            }
                        });
                        actionsHtml += '</div>';
                    }

                    return `
                        <div class="notification-item block px-4 py-3 text-sm hover:bg-gray-100 ${isReadClass} text-dark border-bottom w-100" style="white-space: normal; word-break: break-word;">
                            <a href="${url}" 
                               onclick="event.preventDefault(); window.markAsRead('${notification.id}').then(() => window.location.href='${url}');"
                               class="text-decoration-none text-dark d-block">
                                <div class="d-flex align-items-center">
                                    <div class="me-2"><i class="${icon} fa-lg"></i></div>
                                    <div class="flex-grow-1">
                                        <p class="mb-1">
                                            ${title ? `<strong class="d-block">${title}</strong>` : ''}
                                            ${message}
                                        </p>
                                        <small class="text-secondary">${time}</small>
                                    </div>
                                </div>
                            </a>
                            ${actionsHtml}
                        </div>
                    `;
                }).join('');

                this.notificationList.innerHTML = notificationsHtml;

            } catch (error) {
                console.error('Error al renderizar las notificaciones:', error);
            }
        }

        // ... (resto de los métodos no cambian) ...
        playNotificationSound() {
            // Desactivado para evitar errores y no reproducir sonidos
            return;
        }

        resolveNotificationTitle(data, status) {
            return resolveNotificationTitle(data, status);
        }

        handleNewNotification(notification) {
            if (!notification || !notification.data) return;

            const data = notification.data;
            const message = data.message || 'Nueva notificación';
            const type = data.type || 'info';
            const status = data.status;

            console.log('📬 Procesando nueva notificación:', {
                id: notification.id,
                type: notification.type,
                status: status,
                message: message,
                bid_id: data.bid_id,
                document_id: data.document_id
            });

            // Mostrar toast si la función está disponible
            if (typeof window.showToastNotification === 'function') {
                // Usar título del servidor; si es notificación antigua sin title, deducirlo
                const title = this.resolveNotificationTitle(data, status);

                // toastType y duration según el status del servidor
                let toastType = 'info';
                let duration = 6000;
                if (status === 'aceptado' || status === 'aprobado') {
                    toastType = 'success';
                    duration = 8000;
                } else if (status === 'rechazado') {
                    toastType = 'error';
                    duration = 10000;
                }

                window.showToastNotification(title, message, toastType, duration, notification.id);
                if (data.document_id && window.location.pathname.includes('/profile/document')) {
                    console.log('🔄 Recargando página en 2.5 segundos para mostrar cambios...');
                    setTimeout(() => {
                        console.log('🔄 Recargando página ahora...');
                        window.location.reload();
                    }, 2500);
                }
            } else {
                console.warn('⚠️ showToastNotification no está disponible');
            }
        }

        markAsRead(id) {
            // Si no hay token CSRF, rechazamos la promesa
            if (!this.csrfToken) {
                console.warn('No se puede marcar notificación como leída: token CSRF no encontrado');
                return Promise.reject('Token CSRF no encontrado');
            }

            // Construir la URL de marcar como leído
            const url = this.options.markAsReadUrl.replace(':id', id);

            // Enviar la petición para marcar como leída
            return fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Error HTTP: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Actualizar el contador
                        this.updateNotificationCount(data.count);
                        // Guardar el nuevo contador
                        this.notificationsCount = data.count;
                        return data;
                    }
                    return Promise.reject('La respuesta no indicó éxito');
                })
                .catch(error => {
                    console.error('Error al marcar notificación como leída:', error);
                    return Promise.reject(error);
                });
        }
    }

    window.RealTimeNotifications = RealTimeNotifications;
}

window.markAsRead = function (id) {
    if (window.notificationsManager) {
        return window.notificationsManager.markAsRead(id);
    }
    return Promise.resolve();
};

window.markAllAsRead = function () {
    // ... (este método no cambia) ...
    return fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            // Actualizar el contador de notificaciones
            const badge = document.getElementById('notification-badge');
            if (badge) {
                badge.textContent = '0';
                badge.classList.add('hidden');
            }

            // Actualizar la lista de notificaciones
            const notificationItems = document.querySelectorAll('.notification-item');
            notificationItems.forEach(item => {
                item.classList.remove('font-semibold');
                item.classList.add('text-gray-500');
            });

            return data;
        })
        .catch(error => {
            console.error('Error al marcar todas las notificaciones como leídas:', error);
            throw error;
        });
};

// Función global para resolver el título de una notificación
// Soporta notificaciones nuevas (con data.title del servidor) y antiguas (sin title)
function resolveNotificationTitle(data, status) {
    if (data && data.title) return data.title;
    const isBid = !!(data && data.bid_id);
    const isDoc = !!(data && data.document_id);
    // Fallback: detectar por contenido del mensaje cuando no hay bid_id ni document_id
    const msg = (data && data.message) ? data.message.toLowerCase() : '';
    const looksBid = isBid || msg.includes('oferta');
    const looksDoc = isDoc || msg.includes('documento');
    if (looksBid) {
        if (status === 'aceptado') return '✓ ¡Oferta Aceptada!';
        if (status === 'rechazado') return '✗ Oferta Rechazada';
        return '🔔 Nueva Oferta';
    }
    if (looksDoc) {
        if (status === 'aprobado') return '✓ Documento Aprobado';
        if (status === 'rechazado') return '✗ Documento Rechazado';
        return '📄 Actualización de Documento';
    }
    return '🔔 Nueva Notificación';
}

// Inicializar notificaciones en tiempo real con Echo/Pusher
document.addEventListener('DOMContentLoaded', function() {
    // Esperar a que Echo esté disponible
    const initializeEchoListeners = () => {
        if (typeof window.Echo === 'undefined') {
            console.log('Echo no está disponible aún, esperando...');
            setTimeout(initializeEchoListeners, 500);
            return;
        }

        // Obtener el ID del usuario autenticado
        const userId = document.querySelector('meta[name="user-id"]')?.content;
        
        if (!userId) {
            console.warn('No se encontró el ID del usuario. Las notificaciones en tiempo real no funcionarán.');
            return;
        }

        console.log('Inicializando listener de notificaciones para usuario:', userId);

        // Escuchar notificaciones en el canal privado del usuario
        window.Echo.private(`App.Models.User.${userId}`)
            .notification((notification) => {
                console.log('Nueva notificación recibida:', notification);
                
                // Actualizar el contador de notificaciones
                const badge = document.getElementById('notification-badge');
                if (badge) {
                    const currentCount = parseInt(badge.textContent || '0');
                    const newCount = currentCount + 1;
                    badge.textContent = newCount > 99 ? '99+' : newCount;
                    badge.classList.remove('hidden');
                }

                // Agregar la notificación al dropdown
                const notificationList = document.getElementById('notification-list');
                if (notificationList) {
                    const notificationHTML = createNotificationHTML(notification);
                    
                    // Si hay mensaje de "No hay notificaciones", removerlo
                    const emptyMessage = notificationList.querySelector('.text-center.text-muted');
                    if (emptyMessage) {
                        emptyMessage.remove();
                    }
                    
                    // Insertar la nueva notificación al inicio
                    notificationList.insertAdjacentHTML('afterbegin', notificationHTML);
                    
                    // Limitar a 5 notificaciones en el dropdown
                    const items = notificationList.querySelectorAll('.notification-item');
                    if (items.length > 5) {
                        items[items.length - 1].remove();
                    }
                }

                // ====== TOAST NOTIFICATION ======
                // Mostrar toast notification flotante (visible incluso si hay scroll)
                if (typeof window.showToastNotification === 'function') {
                    const nStatus = notification.status || '';
                    // Usar título del servidor; si es notificación antigua sin title, deducirlo
                    const toastTitle = resolveNotificationTitle(notification, nStatus);
                    const toastMessage = notification.message || 'Tienes una nueva actualización';
                    let toastType = 'info';
                    let toastDuration = 7000;
                    if (nStatus === 'aceptado' || nStatus === 'aprobado') { toastType = 'success'; toastDuration = 8000; }
                    else if (nStatus === 'rechazado') { toastType = 'error'; toastDuration = 10000; }

                    window.showToastNotification(toastTitle, toastMessage, toastType, toastDuration, notification.id || null);
                }

                // Mostrar notificación del navegador si están permitidas
                if ('Notification' in window && Notification.permission === 'granted') {
                    new Notification('PICK N TRUCK', {
                        body: notification.message,
                        icon: '/images/logo.png',
                        tag: `notification-${notification.id}`
                    });
                }
            })
            .listen('.NewBidCreated', (data) => {
                console.log('Nueva oferta recibida (NewBidCreated):', data);
                
                // Mostrar toast notification específica para nueva oferta
                if (typeof window.showToastNotification === 'function') {
                    const toastTitle = 'Nueva Oferta Recibida';
                    const toastMessage = `${data.user.name} te ha enviado una oferta de $${Number(data.monto).toLocaleString()}`;
                    window.showToastNotification(toastTitle, toastMessage, 'success', 8000);
                }
                
                // Actualizar contador de notificaciones
                const badge = document.getElementById('notification-badge');
                if (badge) {
                    const currentCount = parseInt(badge.textContent || '0');
                    const newCount = currentCount + 1;
                    badge.textContent = newCount > 99 ? '99+' : newCount;
                    badge.classList.remove('hidden');
                }
                
                // Mostrar notificación del navegador si están permitidas
                if ('Notification' in window && Notification.permission === 'granted') {
                    new Notification('PICK N TRUCK - Nueva Oferta', {
                        body: `${data.user.name} te ha enviado una oferta de $${Number(data.monto).toLocaleString()}`,
                        icon: '/images/logo.png',
                        tag: `bid-${data.bid_id}`,
                        requireInteraction: true
                    });
                }
            });
    };

    // Función para determinar el tipo de toast según la notificación
    function getNotificationToastType(notification) {
        const message = (notification.message || '').toLowerCase();
        const title = (notification.title || '').toLowerCase();
        const combined = message + ' ' + title;

        // Documentos aprobados
        if (combined.includes('aprobado') || combined.includes('approved')) {
            return 'success';
        }
        
        // Documentos rechazados
        if (combined.includes('rechazado') || combined.includes('rejected')) {
            return 'danger';
        }
        
        // Documentos en general
        if (combined.includes('documento') || combined.includes('document')) {
            return 'document';
        }
        
        // Advertencias
        if (combined.includes('advertencia') || combined.includes('warning') || 
            combined.includes('atención') || combined.includes('importante')) {
            return 'warning';
        }
        
        // Por defecto info
        return 'info';
    }

    // Función para crear el HTML de una notificación
    function createNotificationHTML(notification) {
        const icon = notification.icon || 'fas fa-bell text-primary';
        const message = notification.message || 'Nueva notificación';
        const url = notification.url || '#';
        const timeAgo = 'Justo ahora';
        
        return `
            <div class="notification-item block px-4 py-3 text-sm hover:bg-gray-100 font-semibold text-dark border-bottom w-100"
                style="white-space: normal; word-break: break-word;">
                <a href="${url}"
                    onclick="event.preventDefault(); if(window.markAsRead) { window.markAsRead('${notification.id}').then(() => window.location.href='${url}'); }"
                    class="text-decoration-none text-dark d-block notification-link">
                    <div class="d-flex align-items-center">
                        <div class="me-2">
                            <i class="${icon} fa-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-1">
                                ${notification.title ? `<strong class="d-block">${notification.title}</strong>` : ''}
                                ${message}
                            </p>
                            <small class="text-secondary">${timeAgo}</small>
                        </div>
                    </div>
                </a>
            </div>
        `;
    }

    // Iniciar listeners
    initializeEchoListeners();

    // Pedir permiso para notificaciones del navegador
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
});

// ============================================
// SISTEMA DE TOAST NOTIFICATIONS
// ============================================

/**
 * Muestra una notificación toast flotante
 * @param {string} title - Título de la notificación
 * @param {string} message - Mensaje de la notificación
 * @param {string} type - Tipo: 'success', 'info', 'warning', 'danger'
 * @param {number} duration - Duración en ms (0 = no auto-dismiss)
 */
function showToastNotification(title, message, type = 'info', duration = 5000, notificationId = null) {
    // Crear contenedor de toasts si no existe
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container';
        document.body.appendChild(toastContainer);
    }

    // Crear el elemento toast
    const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `toast-notification toast-${type}`;
    if (notificationId) {
        toast.dataset.notificationId = notificationId;
    }
    
    // Iconos según tipo
    const icons = {
        'success': 'fas fa-check-circle',
        'info': 'fas fa-info-circle',
        'warning': 'fas fa-exclamation-triangle',
        'danger': 'fas fa-times-circle',
        'document': 'fas fa-file-alt'
    };
    
    const icon = icons[type] || icons['info'];
    
    toast.innerHTML = `
        <div class="toast-content">
            <div class="toast-icon">
                <i class="${icon}"></i>
            </div>
            <div class="toast-body">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="closeToast('${toastId}')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        ${duration > 0 ? `<div class="toast-progress"></div>` : ''}
    `;
    
    // Añadir al contenedor
    toastContainer.appendChild(toast);
    
    // Animar entrada
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);
    
    // Auto-dismiss si tiene duración
    if (duration > 0) {
        const progressBar = toast.querySelector('.toast-progress');
        if (progressBar) {
            progressBar.style.animation = `toast-progress ${duration}ms linear`;
        }
        
        setTimeout(() => {
            closeToast(toastId);
        }, duration);
    }
    
    return toastId;
}

/**
 * Cierra una notificación toast
 * @param {string} toastId - ID del toast a cerrar
 */
function closeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        // Marcar notificación como leída al cerrar el toast
        const notificationId = toast.dataset.notificationId;
        if (notificationId) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            if (csrfToken) {
                fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                }).catch(() => {}); // silencioso si falla
            }
        }

        toast.classList.remove('show');
        toast.classList.add('hide');
        
        setTimeout(() => {
            toast.remove();
            
            // Limpiar contenedor si está vacío
            const container = document.getElementById('toast-container');
            if (container && container.children.length === 0) {
                container.remove();
            }
        }, 300);
    }
}

// Hacer funciones globales
window.showToastNotification = showToastNotification;
window.closeToast = closeToast;
