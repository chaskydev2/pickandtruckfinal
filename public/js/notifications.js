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
                updateInterval: 10000, // 10 segundos
                playSound: false // Desactivar sonido por defecto
            }, options);

            // Elementos del DOM
            this.notificationBadge = document.querySelector(this.options.notificationBadgeSelector);
            this.notificationList = document.querySelector(this.options.notificationListSelector);
            this.notificationCount = document.querySelector(this.options.notificationCountSelector);
            this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            // Estado
            this.lastUpdateTime = new Date();
            this.notificationsCount = this.notificationBadge ? parseInt(this.notificationBadge.textContent || '0') : 0;

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
                        throw new Error(`Error HTTP: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // Actualizar el contador de notificaciones
                    this.updateNotificationCount(data.count);

                    // Actualizar la lista de notificaciones si está disponible
                    if (this.notificationList && data.notifications) {
                        this.updateNotificationList(data.notifications);
                    }

                    // Notificar si hay nuevas notificaciones
                    if (data.count > this.notificationsCount && this.options.playSound) {
                        this.playNotificationSound();
                    }

                    // Actualizar el contador guardado
                    this.notificationsCount = data.count;
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
