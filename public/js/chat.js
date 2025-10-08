// Definimos la clase ChatHandler directamente en el objeto global window
window.ChatHandler = class ChatHandler {
    constructor(options) {
        // Elementos del DOM
        this.chatContainer = document.querySelector(options.chatContainerSelector || '.chat-container');
        this.messageForm = document.querySelector(options.messageFormSelector || '#message-form');
        this.messageInput = document.querySelector(options.messageInputSelector || '#message-input');
        this.notificationContainer = document.createElement('div');
        this.notificationContainer.className = 'chat-notification';
        this.chatContainer.prepend(this.notificationContainer);

        // Obtener el chatId del data attribute si no se proporciona
        this.chatId = options.chatId || (this.chatContainer && this.chatContainer.dataset.chatId);

        // Obtener el userId del data attribute si no se proporciona
        this.userId = options.userId || (this.chatContainer && this.chatContainer.dataset.userId) ||
            (document.body.dataset.userId) || null;

        // Array para almacenar las funciones de limpieza
        this.cleanupFunctions = [];

        // Validar que tengamos los datos necesarios
        if (!this.chatId || !this.userId) {
            console.error('ChatHandler: Faltan parámetros requeridos (chatId o userId)');
            return;
        }

        // Configuración
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        this.isLoadingMessages = false;
        this.isSubmitting = false;
        this.lastMessageContent = '';
        this.lastMessageTime = 0;
        this.channel = null; // Canal principal para la pestaña maestra
        this.slaveChatChannel = null; // Nuevo: Canal principal para la pestaña esclava
        this.isMasterTab = false;
        this.slavePingInterval = null;
        this.slaveTimeout = null;
        this.masterPingInterval = null;
        this.presenceChannel = null; // Canal de presencia para la lógica maestro/esclavo
        this.broadcastChannel = typeof BroadcastChannel !== 'undefined' ? new BroadcastChannel(`chat-sync-${this.chatId}`) : null;

        // Configuración específica para el chat en work-progress
        this.isCompact = options.isCompact || false;
    }

    async init() {
        try {
            // Primero configuramos los event listeners
            this.setupEventListeners();

            // Cargar mensajes existentes
            await this.loadNewMessages();
            this.scrollToBottom();

            // Configurar Pusher y la lógica maestro/esclavo
            await this.setupPusher();

            console.log('Chat inicializado correctamente');
            return this;
        } catch (error) {
            console.error('Error al inicializar el chat:', error);
            throw error;
        }
    }

    // Método para agregar un mensaje del sistema al chat
    addSystemMessage(content) {
        if (!content) return;

        const message = {
            id: 'sys-' + Date.now(),
            content: content,
            is_system: true,
            created_at: new Date().toISOString(),
            user: {
                name: 'Sistema',
                id: 0
            }
        };

        this.addMessageToChat(message, false);
        return message;
    }

    destroy() {
        // Limpiar intervalos
        if (this.slavePingInterval) clearInterval(this.slavePingInterval);
        if (this.slaveTimeout) clearTimeout(this.slaveTimeout);
        if (this.masterPingInterval) clearInterval(this.masterPingInterval);
        if (this.checkMasterTimeout) clearInterval(this.checkMasterTimeout); // Limpiar para el esclavo

        // Ejecutar funciones de limpieza registradas
        this.cleanupFunctions.forEach(func => func());
        this.cleanupFunctions = []; // Vaciar el array

        // Desconectar de Pusher si está conectado (aunque cleanupFunctions debería encargarse)
        // Redundante si cleanupFunctions lo hace, pero no hace daño
        if (this.channel) {
            window.Echo.leave(`private-chat.${this.chatId}`); // Asegurarse de dejar el canal correcto
            this.channel = null;
        }
        if (this.slaveChatChannel) { // Limpiar canal del esclavo
            window.Echo.leave(`private-chat.${this.chatId}`);
            this.slaveChatChannel = null;
        }
        if (this.presenceChannel) { // Limpiar canal de presencia
            window.Echo.leave(`presence-chat.${this.chatId}`);
            this.presenceChannel = null;
        }

        // Limpiar event listeners
        if (this.messageForm) {
            this.messageForm.onsubmit = null;
            // Remover el event listener del botón de enviar
            const sendButton = document.getElementById('send-message-btn');
            if (sendButton) {
                sendButton.removeEventListener('click', this._boundSendButtonHandler); // Asumiendo que has guardado la referencia
            }
        }
        if (this.messageInput) {
            this.messageInput.onkeydown = null;
        }

        // Limpiar BroadcastChannel
        if (this.broadcastChannel) {
            this.broadcastChannel.close();
            this.broadcastChannel = null;
        }

        console.log('ChatHandler destruido y recursos liberados.');
    }

    setupEventListeners() {
        if (!this.messageForm) {
            console.warn('No se encontró el formulario de mensaje');
            return;
        }

        const sendButton = document.getElementById('send-message-btn');
        if (!sendButton) {
            console.warn('No se encontró el botón de enviar mensaje');
            return;
        }

        // Usamos una función bind para poder remover el event listener más tarde en destroy()
        this._boundSendButtonHandler = async () => {
            if (this.isSubmitting) {
                console.log('Ya se está enviando un mensaje, espera...');
                return;
            }

            const messageText = this.messageInput ? this.messageInput.value.trim() : '';
            const currentTime = Date.now();

            if (!messageText) {
                console.log('El mensaje está vacío');
                return;
            }

            // Prevenir envíos duplicados rápidos
            if (messageText === this.lastMessageContent && (currentTime - this.lastMessageTime) < 3000) {
                console.log('Mensaje duplicado, espera un momento...');
                return;
            }

            this.lastMessageContent = messageText;
            this.lastMessageTime = currentTime;
            this.isSubmitting = true;

            // Actualizar la UI para mostrar que se está enviando
            const buttonText = sendButton.querySelector('.button-text');
            const spinner = sendButton.querySelector('.spinner-border');

            if (buttonText) buttonText.textContent = 'Enviando...';
            if (spinner) spinner.classList.remove('d-none');
            sendButton.disabled = true;

            try {
                console.log('Enviando mensaje:', messageText);
                const sentMessageData = await this.handleSubmit(messageText);

                if (sentMessageData && sentMessageData.message) {
                    this.addMessageToChat(sentMessageData.message, true);
                }

                // Limpiar el campo de entrada si el envío fue exitoso
                if (this.messageInput) {
                    this.messageInput.value = '';
                }

                console.log('Mensaje enviado exitosamente');
            } catch (error) {
                console.error('Error al enviar el mensaje:', error);
                alert('Error al enviar el mensaje: ' + (error.message || 'Error desconocido'));
            } finally {
                this.isSubmitting = false;

                // Restaurar el botón de envío
                if (buttonText) buttonText.textContent = 'Enviar';
                if (spinner) spinner.classList.add('d-none');
                sendButton.disabled = false;

                if (this.messageInput) {
                    this.messageInput.focus();
                }
            }
        };

        sendButton.addEventListener('click', this._boundSendButtonHandler);

        if (this.messageInput) {
            this.messageInput.onkeydown = (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();

                    // Verificar si ya se está procesando un envío
                    if (this.isSubmitting) {
                        console.log('Ya se está enviando un mensaje, espera...');
                        return;
                    }

                    const messageText = this.messageInput.value.trim();
                    const currentTime = Date.now();

                    // Validar mensaje vacío
                    if (!messageText) {
                        console.log('El mensaje está vacío');
                        return;
                    }

                    // Prevenir envíos duplicados rápidos (3 segundos)
                    if (messageText === this.lastMessageContent && (currentTime - this.lastMessageTime) < 3000) {
                        console.log('Mensaje duplicado, espera un momento...');
                        return;
                    }

                    // Actualizar estado para prevenir envíos duplicados
                    this.lastMessageContent = messageText;
                    this.lastMessageTime = currentTime;
                    this.isSubmitting = true;

                    // Actualizar UI
                    const sendButton = document.getElementById('send-message-btn');
                    if (sendButton) {
                        const buttonText = sendButton.querySelector('.button-text');
                        const spinner = sendButton.querySelector('.spinner-border');

                        if (buttonText) buttonText.textContent = 'Enviando...';
                        if (spinner) spinner.classList.remove('d-none');
                        sendButton.disabled = true;
                    }

                    // Enviar el mensaje
                    this.handleSubmit(messageText)
                        .then((sentMessageData) => {
                            if (this.messageInput) this.messageInput.value = '';
                            if (sentMessageData && sentMessageData.message) {
                                this.addMessageToChat(sentMessageData.message, true);
                            }
                        })
                        .catch(error => {
                            console.error('Error al enviar el mensaje:', error);
                            alert('Error al enviar el mensaje: ' + (error.message || 'Error desconocido'));
                        })
                        .finally(() => {
                            // Restaurar estado
                            this.isSubmitting = false;

                            // Restaurar UI
                            if (sendButton) {
                                const buttonText = sendButton.querySelector('.button-text');
                                const spinner = sendButton.querySelector('.spinner-border');

                                if (buttonText) buttonText.textContent = 'Enviar';
                                if (spinner) spinner.classList.add('d-none');
                                sendButton.disabled = false;
                            }

                            // Enfocar el campo de entrada
                            if (this.messageInput) {
                                this.messageInput.focus();
                            }
                        });
                }
            };
        }
    }


    async determineMasterTab() {
        return new Promise((resolve) => {
            const tabId = 'tab_' + Math.random().toString(36).substr(2, 6);
            const presenceChannelName = `presence-chat.${this.chatId}`;

            console.log(`[${tabId}] Verificando si ya hay un maestro para el chat ${this.chatId}`);

            // Usar un canal de presencia para determinar el maestro
            // IMPORTANTE: Unirse al canal de presencia para la lógica Maestro/Esclavo
            this.presenceChannel = window.Echo.join(presenceChannelName);

            // Intentar unirse al canal de presencia
            this.presenceChannel.here((members) => {
                // Si no hay miembros, soy el primero (maestro)
                if (members.length === 0) {
                    console.log(`[${tabId}] No hay otros miembros, convirtiéndome en maestro`);
                    resolve(true);
                } else {
                    // Ya hay un maestro, soy esclavo
                    console.log(`[${tabId}] Ya hay un maestro activo, convirtiéndome en esclavo`);
                    // NO dejar el canal de presencia aquí si el esclavo lo va a usar para recibir pings del maestro
                    // o para detectar la caída del maestro.
                    resolve(false);
                }
            }).joining((member) => {
                console.log(`[${tabId}] Nuevo miembro se unió al canal de presencia:`, member);
            }).leaving((member) => {
                console.log(`[${tabId}] Miembro dejó el canal de presencia:`, member);
            });

            // Configurar un timeout en caso de que la conexión falle o no haya respuesta
            const timeout = setTimeout(() => {
                console.log(`[${tabId}] Timeout al verificar miembros en canal de presencia, asumiendo como maestro`);
                resolve(true);
            }, 3000);

            // Limpiar al destruir
            this.cleanupFunctions.push(() => {
                clearTimeout(timeout);
                try {
                    if (this.presenceChannel) {
                        window.Echo.leave(presenceChannelName);
                        this.presenceChannel = null;
                    }
                } catch (e) {
                    console.error('Error al dejar el canal de presencia en cleanup:', e);
                }
            });
        });
    }

    async setupPusher() {
        console.log('Iniciando setupPusher...');

        // Verificar si window.Echo está disponible
        if (typeof window.Echo === 'undefined') {
            console.error('Error: window.Echo no está definido');
            return;
        }

        console.log('window.Echo está definido: ', window.Echo);
        console.log('window.Echo.connector: ', window.Echo.connector);

        // Verificar el estado de la conexión de Pusher
        const connectionState = window.Echo.connector.pusher.connection.state;
        console.log('Estado de conexión de Pusher:', connectionState);

        // Si no estamos conectados, esperar a que se conecte
        if (connectionState !== 'connected') {
            console.log('Esperando conexión de Pusher...');

            // Desvincular manejadores previos para evitar duplicados
            window.Echo.connector.pusher.connection.unbind('connected');

            window.Echo.connector.pusher.connection.bind('connected', () => {
                console.log('Conexión de Pusher establecida');
                this.continueWithPusherSetup();
            });

            return;
        }

        // Si ya estamos conectados, continuar con la configuración
        this.continueWithPusherSetup();
    }

    async continueWithPusherSetup() {
        console.log('Continuando con la configuración de Pusher...');
        this.isMasterTab = await this.determineMasterTab();
        console.log('Es pestaña maestra?', this.isMasterTab);

        if (this.isMasterTab) {
            console.log('Configurando como pestaña maestra...');
            this.setupMasterPusherConnection();
        } else {
            console.log('Configurando como pestaña esclava...');
            if (this.broadcastChannel) { // Usamos la instancia de broadcastChannel del constructor
                this.setupSlaveConnection();
            } else {
                console.error('ERROR: broadcastChannel no está disponible para configuración esclava');
                // Fallback: si broadcastChannel no está disponible, esta pestaña actuará como maestro.
                // Esto podría llevar a múltiples maestros en algunos navegadores/versiones, pero garantiza funcionalidad.
                console.warn('Fallback: broadcastChannel no disponible, esta pestaña actuará como maestro para el chat en tiempo real.');
                this.setupMasterPusherConnection();
            }
        }
    }

    // Método para manejar la reconexión con backoff exponencial
    async attemptReconnect(attempt = 1) {
        const maxAttempts = 5;
        const baseDelay = 1000; // 1 segundo
        const maxDelay = 30000; // 30 segundos

        // Calcular retraso exponencial con retroceso aleatorio
        const delay = Math.min(Math.pow(2, attempt) * baseDelay + Math.random() * baseDelay, maxDelay);

        console.log(`Intentando reconectar en ${delay}ms... (Intento ${attempt}/${maxAttempts})`);
        this.showNotification(`Intentando reconectar... (${attempt}/5)`, 'warning');

        // Si superamos el número máximo de intentos, detenemos
        if (attempt > maxAttempts) {
            console.error('Número máximo de intentos de reconexión alcanzado');
            this.showNotification('No se pudo reconectar. Por favor, recarga la página para intentar de nuevo.', 'danger');
            return;
        }

        // Limpiar cualquier timeout anterior
        if (this.reconnectTimeout) {
            clearTimeout(this.reconnectTimeout);
        }

        // Programar el siguiente intento
        this.reconnectTimeout = setTimeout(() => {
            this.setupMasterPusherConnection().catch(error => {
                console.error('Error en el intento de reconexión:', error);
                this.attemptReconnect(attempt + 1);
            });
        }, delay);
    }

    // Función para mostrar notificaciones al usuario
    showNotification(message, type = 'info') {
        // Limpiar notificaciones existentes
        this.notificationContainer.innerHTML = '';

        // Crear elemento de notificación
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show mb-3`;
        notification.role = 'alert';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        `;

        this.notificationContainer.appendChild(notification);

        // Auto-ocultar después de 5 segundos para mensajes informativos
        if (type !== 'danger') {
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
    }

    async setupMasterPusherConnection() {
        console.log('Configurando conexión maestra de Pusher para el chat:', this.chatId);
        this.showNotification('Conectando al chat...', 'info');

        // Limpiar cualquier configuración anterior
        if (this.masterPingInterval) clearInterval(this.masterPingInterval);
        if (this.channel) {
            try {
                console.log('Limpiando canal existente...');
                window.Echo.leave(`private-chat.${this.chatId}`);
            } catch (e) {
                console.error('Error al limpiar canal existente:', e);
                this.showNotification('Error al reconectar el chat. Intentando de nuevo...', 'warning');
            }
        }

        // Verificar autenticación
        if (!this.userId) {
            console.error('Error: Usuario no autenticado. No se puede suscribir al canal privado.');
            return;
        }

        // Configurar manejadores de conexión
        const connection = window.Echo.connector.pusher.connection;

        // Si no estamos conectados, esperar a la conexión
        if (connection.state !== 'connected') {
            console.log('Esperando conexión con Pusher...');

            // Configurar manejador de conexión
            const onConnected = () => {
                console.log('Conexión de Pusher establecida. Estado:', connection.state);
                connection.unbind('connected', onConnected);
                this.setupMasterPusherConnection();
            };

            // Eliminar manejadores anteriores para evitar duplicados
            connection.unbind('connected');
            connection.bind('connected', onConnected);

            // Configurar manejador de error de conexión
            const onError = (error) => {
                console.error('Error en la conexión de Pusher:', error);
                this.showNotification('Se perdió la conexión. Intentando reconectar...', 'warning');
                this.attemptReconnect();
            };

            connection.unbind('error', onError);
            connection.bind('error', onError);

            return;
        }

        try {
            console.log(`[MAESTRO] Iniciando configuración para el chat: ${this.chatId}`);

            // 1. Primero intentamos con el canal de presencia
            console.log('Intentando unirse al canal de presencia...');
            this.presenceChannel = window.Echo.join(`presence-chat.${this.chatId}`)
                .here((users) => {
                    console.log('Usuarios en el canal de presencia:', users);
                    this.showNotification('Conectado al chat', 'success');
                })
                .joining((user) => {
                    console.log('Usuario conectado:', user);
                    this.showNotification(`${user.name} se ha conectado`, 'info');
                })
                .leaving((user) => {
                    console.log('Usuario desconectado:', user);
                    this.showNotification(`${user.name} se ha desconectado`, 'info');
                })
                .error((error) => {
                    console.error('Error en el canal de presencia:', error);
                    this.showNotification('Error en la conexión del chat', 'danger');
                });

            // Pequeña pausa para asegurar que la conexión de presencia se establezca
            await new Promise(resolve => setTimeout(resolve, 1000));

            // 2. Luego intentamos con el canal privado
            console.log('Intentando suscribirse al canal privado...');
            // Cambiado de private-chat a chat para que coincida con la configuración del servidor
            const channelName = `chat.${this.chatId}`;
            this.channel = window.Echo.private(channelName);

            // Configurar manejador de mensajes con el nombre de evento completo
            this.channel.listen('.new.message', (e) => {
                console.log('Nuevo mensaje recibido (maestro):', e);
                if (e?.message) {
                    if (this.broadcastChannel) {
                        this.broadcastChannel.postMessage({
                            type: 'new_message',
                            chatId: this.chatId,
                            message: e.message
                        });
                    }
                    this.addMessageToChat(e.message, e.message.user_id === this.userId);
                }
            });

            // Para depuración: Verificar la suscripción
            console.log('Suscrito al canal privado:', channelName);
            console.log('Escuchando evento: new.message');

            // Configurar manejador de errores mejorado
            this.channel.error((error) => {
                console.error('Error en el canal de Pusher:', error);

                if (error?.type === 'AuthError') {
                    console.warn('Error de autenticación. Verificando estado...');
                    // Detener todos los intentos de reconexión
                    if (this.reconnectTimeout) {
                        clearTimeout(this.reconnectTimeout);
                    }
                    // Mostrar mensaje al usuario
                    alert('Error de autenticación. Por favor, recarga la página.');
                    return;
                }

                // Para otros errores, intentar reconexión con backoff exponencial
                this.attemptReconnect();
            });

            console.log('Conexión establecida correctamente');
            this.startMasterPing();
            this.pusherInitialized = true;

        } catch (error) {
            console.error('Error crítico en setupMasterPusherConnection:', error);
            this.attemptReconnect();
        }

        // Configurar manejador de desconexión
        connection.unbind('disconnected'); // Evitar duplicados
        connection.bind('disconnected', () => {
            console.log('Conexión de Pusher desconectada (maestro)');
            clearInterval(this.masterPingInterval);
            // Intentar reconectar con backoff
            this.attemptReconnect();
        });

        // Configurar limpieza al destruir
        this.cleanupFunctions.push(() => {
            console.log(`[MAESTRO] Limpiando suscripción de Pusher`);
            if (this.channel) {
                window.Echo.leave(`private-chat.${this.chatId}`);
                this.channel = null;
            }
            clearInterval(this.masterPingInterval);
        });
    }

    startMasterPing() {
        // Limpiar cualquier intervalo existente
        clearInterval(this.masterPingInterval);

        // Configurar el intervalo de ping
        this.masterPingInterval = setInterval(() => {
            if (this.broadcastChannel) {
                this.broadcastChannel.postMessage({
                    type: 'ping_master',
                    chatId: this.chatId,
                    timestamp: Date.now()
                });
                console.log('Ping enviado a pestañas esclavas');
            }
        }, 3000); // Enviar ping cada 3 segundos

        // Limpiar al destruir
        this.cleanupFunctions.push(() => {
            clearInterval(this.masterPingInterval);
        });
    }

    setupSlaveConnection() {
        console.log('Configurando como pestaña esclava para el chat:', this.chatId);

        // Limpiar cualquier configuración anterior
        if (this.slavePingInterval) clearInterval(this.slavePingInterval);
        if (this.slaveTimeout) clearTimeout(this.slaveTimeout);
        if (this.checkMasterTimeout) clearInterval(this.checkMasterTimeout);

        // La suscripción al canal de presencia ya se hizo en determineMasterTab()
        // this.presenceChannel = window.Echo.join(presenceChannelName);

        console.log('Conectado al canal de presencia como esclavo:', `presence-chat.${this.chatId}`);

        // *** USAR EL MISMO NOMBRE DE CANAL QUE EN EL SERVIDOR ***
        const chatChannelName = `chat.${this.chatId}`;

        try {
            // Verificar si el usuario está autenticado antes de intentar suscribirse
            if (!this.userId) {
                console.warn('Usuario no autenticado. No se puede suscribir al canal privado.');
                return;
            }

            // Intentar suscribirse al canal privado
            this.slaveChatChannel = window.Echo.private(chatChannelName);

            // Configurar el manejador de mensajes con el nombre de evento completo
            this.slaveChatChannel
                .listen('.new.message', (data) => {
                    console.log('Nuevo mensaje recibido vía Pusher (esclavo en canal principal):', data);
                    if (data?.message) {
                        this.addMessageToChat(data.message, data.message.user_id === this.userId);
                    } else {
                        console.warn('Mensaje recibido sin formato esperado:', data);
                    }
                });

            console.log('Esclavo suscrito correctamente al canal:', chatChannelName);
            console.log('Esclavo escuchando evento: .new.message');

            // Manejar errores del canal
            this.slaveChatChannel.error((error) => {
                console.error('Error en el canal de Pusher (esclavo):', error);
                // Si hay un error de autenticación, no seguir intentando
                if (error.type === 'AuthError') {
                    console.warn('Error de autenticación. Verifica que el usuario tenga permisos para este chat.');
                    return;
                }
                // Para otros errores, intentar reconvertir en maestro
                setTimeout(() => this.setupPusher(), 5000);
            });
        } catch (error) {
            console.error('Error al configurar el canal esclavo:', error);
            // En caso de error, intentar convertirse en maestro después de un retraso
            setTimeout(() => this.setupPusher(), 5000);
        }

        // Configurar limpieza para el canal de chat del esclavo
        this.cleanupFunctions.push(() => {
            if (this.slaveChatChannel) {
                window.Echo.leave(chatChannelName);
                this.slaveChatChannel = null;
            }
        });

        // Listener para mensajes de BroadcastChannel (del maestro)
        if (this.broadcastChannel) {
            this.broadcastChannel.onmessage = (event) => {
                switch (event.data.type) {
                    case 'ping_master':
                        // console.log('Ping del maestro recibido. Reiniciando timeout esclavo.');
                        clearTimeout(this.slaveTimeout);
                        this.slaveTimeout = setTimeout(() => {
                            console.log('Tiempo de espera agotado, maestro no responde. Intentando convertirme en maestro.');
                            clearInterval(this.checkMasterTimeout); // Detener chequeo previo
                            this.setupPusher(); // Re-iniciar el proceso para intentar ser maestro
                        }, 5000); // Dar 5 segundos al maestro para ping
                        break;
                    case 'new_message':
                        console.log('Mensaje reenviado por maestro via BroadcastChannel:', event.data.message);
                        break;
                }
            };
        }

        this.checkMasterTimeout = setInterval(() => {
            if (this.presenceChannel) {
                // Verificar si hay miembros en el canal de presencia (incluyendo el maestro)
                this.presenceChannel.here((members) => {
                    // Aquí asumimos que el maestro es el único que permanece en el canal de presencia
                    // o que hay una lógica para identificarlo.
                    // Si no hay miembros, o el maestro específico ha desaparecido:
                    if (members.length === 0 || !members.some(m => m.id === 'master_id_o_logica_maestra')) {
                        console.log('No hay maestros activos en el canal de presencia, intentando convertirme en maestro.');
                        clearInterval(this.checkMasterTimeout);
                        this.setupPusher(); // Re-iniciar para que esta pestaña intente ser maestra
                    }
                });
            }
        }, 10000); // Verificar cada 10 segundos

        // Configurar un timeout inicial para que el esclavo espere el primer ping del maestro
        this.slaveTimeout = setTimeout(() => {
            console.log('Tiempo de espera inicial agotado, maestro no ha enviado ping. Intentando convertirme en maestro.');
            clearInterval(this.checkMasterTimeout);
            this.setupPusher();
        }, 5000); // Dar un margen inicial al maestro

        // Limpiar al destruir
        this.cleanupFunctions.push(() => {
            console.log('[ESCLAVO] Limpiando suscripción de presencia y timers');
            clearInterval(this.checkMasterTimeout);
            clearTimeout(this.slaveTimeout);
            if (this.presenceChannel) {
                window.Echo.leave(presenceChannelName);
                this.presenceChannel = null;
            }
            if (this.broadcastChannel) {
                this.broadcastChannel.onmessage = null; // Limpiar el handler
            }
        });
    }

    handleNewMessage(message) {
        console.log('Manejando nuevo mensaje:', message);
        if (!message) return;

        // Verificar si el mensaje es para este chat
        if (parseInt(message.chat_id) !== parseInt(this.chatId)) {
            console.log('Mensaje para otro chat, ignorando...');
            return;
        }

        // Determinar si es un mensaje propio
        const isOwnMessage = message.user_id === this.userId;

        // Agregar el mensaje al chat
        this.addMessageToChat(message, isOwnMessage);
    }

    addMessageToChat(message, isOwnMessage = null) {
        // Verificar que el contenedor exista
        if (!this.chatContainer) {
            console.error('No se encontró el contenedor del chat');
            return;
        }

        // Si no se especifica isOwnMessage, determinarlo basado en el ID del usuario
        if (isOwnMessage === null && message.user_id) {
            isOwnMessage = (message.user_id == this.userId);
        }

        // Asegurarse de que isOwnMessage sea un booleano
        isOwnMessage = Boolean(isOwnMessage);

        console.log('Agregando mensaje al chat:', {
            messageId: message.id,
            userId: message.user_id,
            currentUserId: this.userId,
            isOwnMessage: isOwnMessage
        });

        // Evitar duplicados
        const existingMessage = this.chatContainer.querySelector(`[data-message-id="${message.id}"]`);
        if (existingMessage) {
            console.log('Mensaje duplicado, ignorando...', message.id);
            return;
        }

        // Crear elemento del mensaje
        const messageElement = document.createElement('div');
        messageElement.className = `message mb-3 ${isOwnMessage ? 'text-end' : ''}`;
        messageElement.dataset.messageId = message.id;
        messageElement.dataset.senderId = message.user_id;
        messageElement.dataset.isOwn = isOwnMessage ? 'true' : 'false';

        // Mensaje del sistema - Usar el mismo formato que los mensajes normales
        if (message.is_system) {
            const messageTime = message.created_at ? new Date(message.created_at) : new Date();
            const formattedTime = messageTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            messageElement.innerHTML = `
                <div class="d-inline-block p-2 rounded bg-light" style="max-width: 80%;">
                    <div class="small text-muted">${message.content}</div>
                    <div class="small text-muted">${formattedTime}</div>
                </div>
            `;
        } else {
            // Formatear la hora
            const messageTime = message.created_at ? new Date(message.created_at) : new Date();
            const formattedTime = messageTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            // Asegurarse de que el nombre del usuario esté disponible
            const userName = (message.user && message.user.name) ? message.user.name : 'Usuario';

            // Estructura del mensaje normal
            messageElement.innerHTML = `
                <div class="d-inline-block p-2 rounded ${isOwnMessage ? 'bg-primary text-white' : 'bg-light'}" style="max-width: 80%;">
                    <div class="small fw-bold mb-1">${isOwnMessage ? 'Tú' : userName}</div>
                    <div class="message-content">${message.content}</div>
                    <div class="small ${isOwnMessage ? 'text-light' : 'text-muted'}">
                        ${formattedTime}
                    </div>
                </div>
            `;
        }

        // Agregar el mensaje al contenedor
        this.chatContainer.appendChild(messageElement);
        this.scrollToBottom();
    }

    scrollToBottom() {
        if (this.chatContainer) {
            this.chatContainer.scrollTop = this.chatContainer.scrollHeight;
        }
    }

    async handleSubmit(messageText) {
        if (!this.messageForm) {
            console.error('Error: No se encontró el formulario de mensaje');
            return;
        }

        // Mostrar indicador de carga
        const submitButton = this.messageForm.querySelector('button[type="submit"]');
        const spinner = submitButton ? submitButton.querySelector('.spinner-border') : null;
        const buttonText = submitButton ? submitButton.querySelector('.button-text') : null;

        if (submitButton) {
            submitButton.disabled = true;
            if (spinner) spinner.classList.remove('d-none');
            if (buttonText) buttonText.textContent = 'Enviando...';
        }

        try {
            const url = this.messageForm.getAttribute('action');
            if (!url) {
                throw new Error('No se pudo determinar la URL de envío del mensaje');
            }

            const formData = new FormData();
            formData.append('message', messageText);
            formData.append('_token', this.csrfToken);
            formData.append('_ajax', 'true');

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `Error HTTP: ${response.status}`);
            }

            const responseData = await response.json();

            // Limpiar el campo de entrada
            if (this.messageInput) {
                this.messageInput.value = '';
                this.messageInput.focus();
            }

            return responseData;
        } catch (error) {
            console.error('Error al enviar el mensaje:', error);
            alert('Error al enviar el mensaje: ' + error.message);
            this.isSubmitting = false;

            // Re-habilitar el botón de enviar en caso de error
            const submitButton = this.messageForm.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = false;
                // Asumiendo que el botón tenía un spinner o texto para restaurar
                const buttonText = submitButton.querySelector('.button-text');
                const spinner = submitButton.querySelector('.spinner-border');
                if (buttonText) buttonText.textContent = 'Enviar';
                if (spinner) spinner.classList.add('d-none');
            }
            throw error; // Re-lanza el error para que el setupEventListeners lo maneje en el catch
        }
    }

    async loadNewMessages(initialLoad = true) {
        if (this.isLoadingMessages) return Promise.resolve();
        this.isLoadingMessages = true;

        try {
            const response = await fetch(`/chats/${this.chatId}/messages`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                },
                cache: 'no-store'
            });

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const data = await response.json();
            console.log('Mensajes cargados desde el servidor:', data.messages?.length || 0);

            if (data.messages && data.messages.length > 0) {
                // Si es la carga inicial, limpiar el contenedor primero
                if (initialLoad) {
                    this.chatContainer.innerHTML = '';
                }

                // Ordenar mensajes por fecha (por si acaso)
                const sortedMessages = [...data.messages].sort((a, b) =>
                    new Date(a.created_at) - new Date(b.created_at)
                );

                // Procesar mensajes
                for (const message of sortedMessages) {
                    // Verificar si el mensaje ya existe
                    const existingMessage = this.chatContainer.querySelector(`[data-message-id="${message.id}"]`);
                    if (!existingMessage) {
                        const isOwnMessage = message.user_id == this.userId;
                        this.addMessageToChat(message, isOwnMessage);
                    }
                }

                // Solo hacer scroll si es la carga inicial o hay mensajes nuevos
                if (initialLoad || data.messages.length > 0) {
                    this.scrollToBottom();
                }
            }

            return data.messages || [];
        } catch (error) {
            console.error('Error al cargar mensajes:', error);
            throw error;
        } finally {
            this.isLoadingMessages = false;
        }
    }
}