// Clase para manejar las actualizaciones de estado del bid en tiempo real
window.BidStatusUpdater = class BidStatusUpdater {
    constructor(options = {}) {
        this.bidId = options.bidId || document.body.getAttribute('data-bid-id');
        this.userId = options.userId || document.body.getAttribute('data-user-id');
        this.userAId = options.userAId || document.body.getAttribute('data-user-a-id');
        this.userBId = options.userBId || document.body.getAttribute('data-user-b-id');
        
        this.statusBadge = document.querySelector('#bidStatusBadge');
        this.statusMessage = document.querySelector('.status-message');
        
        this.requestCompletionForm = document.getElementById('requestCompletionForm');
        
        this.confirmationAlert = document.getElementById('confirmationAlert');
        this.confirmationAlertText = this.confirmationAlert ? this.confirmationAlert.querySelector('.alert-text') : null;
        this.confirmationButtonsContainer = document.getElementById('confirmationButtons');

        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        this.channel = null;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectTimeout = null;
        
        if (!this.bidId) {
            return;
        }
        
        this.init();
    }
    
    async init() {
        this.setupFormHandlers();
        
        await this.setupPusherConnection();
    }
    
    setupFormHandlers() {
        const forms = document.querySelectorAll('form.form-ajax');
        
        forms.forEach((form, index) => {
            if (form.dataset.handled) {
                return;
            }
            
            form.dataset.handled = 'true';
            
            const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
            
            if (submitButton) {
                submitButton.dataset.originalHtml = submitButton.innerHTML;
            }
            
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const formData = new FormData(form);
                
                if (submitButton) {
                    submitButton.disabled = true;
                    const loadingText = submitButton.dataset.loadingText || 
                                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
                    submitButton.innerHTML = loadingText;
                }
                
                try {
                    const response = await fetch(form.action, {
                        method: form.method,
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken || ''
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok) {
                        if (data.bid) {
                            this.updateBidUI(data.bid);
                            this.showNotification(data.message || 'Acción completada con éxito', 'success');
                        }
                    } else {
                        this.showNotification(data.message || 'Ocurrió un error al procesar la solicitud', 'danger');
                    }
                } catch (error) {
                    this.showNotification('Ocurrió un error de red o del servidor.', 'danger');
                } finally {
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = submitButton.dataset.originalHtml;
                    }
                }
            });
        });
    }
    
    async setupPusherConnection() {
        if (typeof window.Echo === 'undefined') {
            setTimeout(() => this.setupPusherConnection(), 1000);
            return;
        }
        
        try {
            this.cleanup();
            
            this.channel = window.Echo.private(`bid.${this.bidId}`);
            
            this.channel.listen('.BidStatusUpdated', (data) => {
                if (data.bid) {
                    this.updateBidUI(data.bid);
                    if (data.message) {
                        // Extraer el contenido del mensaje si es un objeto
                        const messageContent = typeof data.message === 'object' ? data.message.content : data.message;
                        if (messageContent) {
                            this.showNotification(messageContent, 'info');
                        }
                    }
                }
            });
            
            this.reconnectAttempts = 0;
            
        } catch (error) {
            this.attemptReconnect();
        }
    }
    
    attemptReconnect() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            this.showNotification('No se pudo reconectar. Por favor, recarga la página para intentar de nuevo.', 'danger');
            return;
        }
        
        const delay = Math.min(1000 * Math.pow(2, this.reconnectAttempts) + Math.random() * 1000, 30000);
        this.reconnectAttempts++;
        
        if (this.reconnectTimeout) {
            clearTimeout(this.reconnectTimeout);
        }
        
        this.reconnectTimeout = setTimeout(() => {
            this.setupPusherConnection();
        }, delay);
    }
    
    getStatusText(bid) {
        const estado = bid.estado;
        const isUserA = this.userId == this.userAId;
        const hasUserConfirmed = (isUserA && bid.confirmacion_usuario_a) || (!isUserA && bid.confirmacion_usuario_b);
        const isOtherUserConfirmed = (isUserA && bid.confirmacion_usuario_b) || (!isUserA && bid.confirmacion_usuario_a);
        
        switch(estado) {
            case 'pendiente':
                return 'Pendiente';
            case 'aceptado':
                return 'Aceptado';
            case 'pendiente_confirmacion':
                if (hasUserConfirmed) {
                    return 'Pendiente Confirmación (Tú ✓)';
                } else if (isOtherUserConfirmed) {
                    return 'Pendiente Confirmación (La otra parte ✓)';
                }
                return 'Pendiente de confirmación';
            case 'terminado':
                return 'Terminado';
            case 'rechazado':
            case 'cancelado':
                return 'Rechazado';
            default:
                return estado;
        }
    }
    
    updateBidUI(bid) {
        if (this.statusBadge) {
            const statusClass = this.getStatusBadgeClass(bid.estado);
            this.statusBadge.className = `status-badge ${statusClass}`;
            this.statusBadge.textContent = this.getStatusText(bid);
        }

        const isUserA = this.userId == this.userAId;
        const hasUserConfirmed = (isUserA && bid.confirmacion_usuario_a) || (!isUserA && bid.confirmacion_usuario_b);
        const isOtherUserConfirmed = (isUserA && bid.confirmacion_usuario_b) || (!isUserA && bid.confirmacion_usuario_a);

        // Lógica para mostrar/ocultar el formulario de solicitud de finalización
        if (this.requestCompletionForm) {
            if (bid.estado === 'aceptado') {
                this.requestCompletionForm.classList.remove('d-none');
            } else {
                this.requestCompletionForm.classList.add('d-none');
            }
        }
        
        // Lógica para el alerta y los botones de confirmación
        if (this.confirmationAlert) {
            if (bid.estado === 'pendiente_confirmacion') {
                this.confirmationAlert.style.display = 'block';
                
                if (hasUserConfirmed) {
                    this.confirmationAlertText.textContent = 'Has confirmado la finalización de este trabajo. Esperando confirmación de la otra parte.';
                    if (this.confirmationButtonsContainer) {
                        this.confirmationButtonsContainer.style.display = 'none';
                    }
                } else if (isOtherUserConfirmed) {
                    this.confirmationAlertText.textContent = 'La otra parte ha solicitado la finalización de este trabajo. Por favor, confirma o rechaza la solicitud.';
                    if (this.confirmationButtonsContainer) {
                        this.confirmationButtonsContainer.style.display = 'block';
                    }
                }
            } else {
                this.confirmationAlert.style.display = 'none';
            }
        }
        
        // Mostrar/ocultar mensaje de terminado
        let terminadoAlert = document.querySelector('.alert-success.mt-3');
        if (bid.estado === 'terminado') {
            // Si no existe el mensaje de terminado, crearlo
            if (!terminadoAlert) {
                terminadoAlert = document.createElement('div');
                terminadoAlert.className = 'alert alert-success mt-3';
                terminadoAlert.innerHTML = '<i class="fas fa-check-circle me-2"></i>Este servicio ha sido completado exitosamente y está marcado como terminado.';
                
                // Insertarlo después del alert de confirmación o al final del card-body
                const cardBody = document.querySelector('.card-body');
                if (cardBody) {
                    cardBody.appendChild(terminadoAlert);
                }
            } else {
                terminadoAlert.style.display = 'block';
            }
        } else {
            // Ocultar el mensaje si el estado no es terminado
            if (terminadoAlert) {
                terminadoAlert.style.display = 'none';
            }
        }

        // Lógica para deshabilitar el chat
        const messageInput = document.getElementById('message-input');
        const sendMessageBtn = document.getElementById('send-message-btn');
        if (messageInput && sendMessageBtn) {
            const isDisabled = (bid.estado === 'terminado' || bid.estado === 'cancelado' || bid.estado === 'rechazado');
            messageInput.disabled = isDisabled;
            sendMessageBtn.disabled = isDisabled;
        }
        
        // Actualizar mensaje de chat deshabilitado
        const chatDisabledMsg = document.querySelector('.form-text.text-center.mt-2');
        if (chatDisabledMsg) {
            if (bid.estado === 'terminado') {
                chatDisabledMsg.style.display = 'block';
            } else {
                chatDisabledMsg.style.display = 'none';
            }
        }
    }
    
    getStatusBadgeClass(status) {
        switch(status) {
            case 'pendiente': return 'bg-secondary';
            case 'aceptado': return 'bg-primary';
            case 'pendiente_confirmacion': return 'bg-warning';
            case 'terminado': return 'bg-success';
            case 'rechazado':
            case 'cancelado':
                return 'bg-danger';
            default: return 'bg-secondary';
        }
    }
    
    showNotification(message, type = 'info') {
        // Convertir el mensaje a string si es un objeto
        let messageText = message;
        if (typeof message === 'object' && message !== null) {
            // Si es un objeto, intentar extraer un mensaje común
            messageText = message.message || message.text || message.error || JSON.stringify(message);
        }
        
        let notificationContainer = document.querySelector('.notification-container');
        
        if (!notificationContainer) {
            notificationContainer = document.createElement('div');
            notificationContainer.className = 'notification-container position-fixed top-0 end-0 p-3';
            notificationContainer.style.zIndex = '1100';
            document.body.appendChild(notificationContainer);
        }
        
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show`;
        notification.role = 'alert';
        notification.innerHTML = `
            ${messageText}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        `;
        
        notificationContainer.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 150);
        }, 5000);
    }
    
    cleanup() {
        if (this.channel) {
            try {
                window.Echo.leave(`private-bid.${this.bidId}`);
            } catch (e) {
                console.warn('Error al limpiar la suscripción anterior:', e);
            }
            this.channel = null;
        }
        
        if (this.reconnectTimeout) {
            clearTimeout(this.reconnectTimeout);
            this.reconnectTimeout = null;
        }
    }
    
    destroy() {
        this.cleanup();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const bidId = document.body.getAttribute('data-bid-id');
        const userId = document.body.getAttribute('data-user-id');
        const userAId = document.body.getAttribute('data-user-a-id');
        const userBId = document.body.getAttribute('data-user-b-id');
        
        if (bidId && userId) {
            window.bidStatusUpdater = new BidStatusUpdater({
                bidId: bidId,
                userId: userId,
                userAId: userAId,
                userBId: userBId
            });
        }
    }, 500);
});