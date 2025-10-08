import './bootstrap';

if (!window.Alpine) {
    import('alpinejs').then(Alpine => {
        window.Alpine = Alpine.default;
        window.Alpine.start();
    });
}

if (!window.__PUSHER_INITIALIZED__) {
    window.__PUSHER_INITIALIZED__ = true;

    const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY || '';
    const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER || 'sa1';

    if (!pusherKey) {
        console.error('Error: VITE_PUSHER_APP_KEY no definida.');
    }

    Promise.all([
        import('pusher-js'),
        import('laravel-echo')
    ]).then(([PusherModule, EchoModule]) => {
        const Pusher = PusherModule.default;
        const Echo = EchoModule.default;

        window.Pusher = Pusher;

        if (typeof window.BroadcastChannel !== 'undefined' && !window.broadcastChannel) {
            window.broadcastChannel = new BroadcastChannel('pusher_channel');
        }

        const echoConfig = {
            broadcaster: 'pusher',
            key: pusherKey,
            wsHost: `ws-${pusherCluster}.pusher.com`,
            cluster: pusherCluster,
            wsPort: 80,
            wssPort: 443,
            forceTLS: true,
            encrypted: true,
            disableStats: true,
            enabledTransports: ['ws', 'wss'],
            activityTimeout: 120000,
            pongTimeout: 10000,
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
            },
            authorizer: (channel, options) => {
                return {
                    authorize: (socketId, callback) => {
                        axios.post('/broadcasting/auth', {
                            socket_id: socketId,
                            channel_name: channel.name
                        })
                            .then(response => {
                                callback(false, response.data);
                            })
                            .catch(error => {
                                callback(true, error);
                            });
                    }
                };
            },
        };

        window.Echo = new Echo(echoConfig);

        const pusher = window.Echo.connector.pusher;

        pusher.connection.bind('connected', () => {
            document.dispatchEvent(new Event('echo:ready'));
        });

        pusher.connection.bind('error', (error) => {
            console.error('Error de conexión Pusher:', error);
        });

        // ELIMINAMOS LA IMPORTACIÓN DINÁMICA DE CHAT.JS AQUÍ
        // Para que se cargue de forma global si no es un módulo ES

        const initializeChatInstance = () => {
            // Asegúrate de que ChatHandler ya esté disponible globalmente (ej: window.ChatHandler)
            if (typeof window.ChatHandler !== 'undefined') { // CAMBIO CLAVE: Usa window.ChatHandler
                const chatContainerElement = document.querySelector('.chat-container');
                if (chatContainerElement && !window.chatInitialized) {
                    const chatId = chatContainerElement.dataset.chatId;
                    const userId = chatContainerElement.dataset.userId;

                    if (!chatId || !userId) {
                        console.error('Error: Faltan datos (chatId o userId) en el contenedor del chat.');
                        return;
                    }

                    // Verificar si ya hay una instancia de chat y limpiarla si es necesario
                    if (window.chat && typeof window.chat.destroy === 'function') {
                        window.chat.destroy();
                    }

                    // Crear nueva instancia del chat
                    window.chat = new window.ChatHandler({
                        chatContainerSelector: '.chat-container',
                        messageFormSelector: '#message-form',
                        messageInputSelector: '#message-input',
                        chatId: chatId,
                        userId: parseInt(userId)
                    }).init();

                    window.chatInitialized = true;
                }
            } else {
                console.warn('window.ChatHandler no está disponible. Asegúrate de que chat.js se cargue correctamente como script global.');
            }
        };

        document.addEventListener('echo:ready', initializeChatInstance);

        if (document.readyState === 'complete' || (document.readyState !== 'loading' && !document.documentElement.doScroll)) {
            if (window.Echo && window.Echo.connector.pusher.connection.state === 'connected' && !window.chatInitialized) {
                initializeChatInstance();
            }
        } else {
            document.addEventListener('DOMContentLoaded', () => {
                if (window.Echo && window.Echo.connector.pusher.connection.state === 'connected' && !window.chatInitialized) {
                    initializeChatInstance();
                }
            });
        }

    }).catch(error => {
        console.error('Error al cargar módulos Pusher/Echo:', error);
    });
}

// Estas líneas están bien si no usas Alpine o WorkStatusUpdater
if (typeof RealTimeChat === 'undefined') {
    window.RealTimeChat = class RealTimeChat {
        constructor() { }
    };
}

if (typeof WorkStatusUpdater === 'undefined') {
    window.WorkStatusUpdater = class WorkStatusUpdater {
        constructor() { }
    };
}