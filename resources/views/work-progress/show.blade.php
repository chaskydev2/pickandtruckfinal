@extends('layouts.app')

@push('body_attrs')
    data-bid-id="{{ $bid->id }}"
    data-user-id="{{ auth()->id() }}"
    data-user-a-id="{{ $bid->user_id }}"
    data-user-b-id="{{ $bid->bideable->user_id }}"
@endpush

@push('styles')
    <style>
        .status-badge {
            font-size: 0.9rem;
            padding: 0.5em 0.8em;
            border-radius: 0.25rem;
            transition: all 0.3s ease;
        }

        .status-updating {
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 0.7;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.7;
            }
        }

        /* Ajustes suaves del alert */
        .confirmation-alert {
            border-width: 1px;
            border-left-width: .35rem;
        }

        .confirmation-alert.alert-info {
            border-left-color: #0dcaf0;
        }

        .confirmation-alert.alert-warning {
            border-left-color: #ffc107;
        }

        .confirmation-alert.alert-primary {
            border-left-color: #0d6efd;
        }

        .confirmation-alert .alert-heading {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: .5rem;
        }

        .confirmation-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .confirmation-actions .btn {
            min-width: 220px;
        }
    </style>
@endpush

@section('content')
    @php
        $yo = auth()->id();
        // A = transportista (quien creó el bid), B = dueño de la publicación (bideable->user_id)
        $esUsuarioA = $yo === $bid->user_id;
        $esUsuarioB = $yo === $bid->bideable->user_id;

        // Confirma según lado
        $usuarioActualConfirmo =
            ($esUsuarioA && $bid->confirmacion_usuario_a) || ($esUsuarioB && $bid->confirmacion_usuario_b);
        $otroUsuarioConfirmo =
            ($esUsuarioA && $bid->confirmacion_usuario_b) || ($esUsuarioB && $bid->confirmacion_usuario_a);

        $puedoSolicitar = $bid->estado === 'aceptado' && Gate::check('requestCompletion', $bid);
        $puedoConfirmar = $bid->estado === 'pendiente_confirmacion' && Gate::check('confirmCompletion', $bid);
        $puedoRechazar = $puedoConfirmar; // misma policy

        // Badge de estado
        $statusClass =
            $bid->estado === 'terminado'
                ? 'bg-success'
                : ($bid->estado === 'aceptado'
                    ? 'bg-primary'
                    : ($bid->estado === 'pendiente_confirmacion'
                        ? 'bg-warning'
                        : 'bg-secondary'));

        // Texto de estado
        if ($bid->estado === 'terminado') {
            $statusText = 'Terminado';
        } elseif ($bid->estado === 'aceptado') {
            $statusText = 'Aceptado';
        } elseif ($bid->estado === 'pendiente_confirmacion') {
            $statusText =
                $bid->confirmacion_usuario_a && $bid->confirmacion_usuario_b
                    ? 'Pendiente Confirmación (Ambos ✓)'
                    : 'Pendiente Confirmación';
        } else {
            $statusText = 'Estado Desconocido';
        }

        // Color del alert contextual
        $alertClass = $usuarioActualConfirmo
            ? 'alert-primary' // tú ya confirmaste
            : ($otroUsuarioConfirmo
                ? 'alert-warning' // el otro confirmó, te toca
                : 'alert-info'); // ambos pendientes
    @endphp

    <div class="container py-4">
        <div class="row g-4">
            <div class="col-md-8 order-1 order-md-0">
                {{-- CARD: Estado y acciones del servicio --}}
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Detalles del Servicio</h5>
                        <span id="bidStatusBadge" class="status-badge {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </div>

                    <div class="card-body">
                        <p class="status-message small text-muted mb-3"></p>

                        <div class="row">
                            <div class="col-md-6">
                                <h6>Detalles de la Oferta</h6>
                                <dl class="mb-0">
                                    <dt>Ruta</dt>
                                    <dd>{{ $bid->bideable->origen }} → {{ $bid->bideable->destino }}</dd>

                                    <dt>Fecha Programada</dt>
                                    <dd>{{ optional($bid->fecha_hora)->format('d/m/Y H:i') }}</dd>

                                    <dt>Monto Acordado</dt>
                                    <dd>${{ number_format($bid->monto, 2) }}</dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <h6>Participantes</h6>
                                <dl class="mb-0">
                                    <dt>Cliente</dt>
                                    <dd>{{ $bid->bideable->user->name }}</dd>
                                    <dt>Transportista</dt>
                                    <dd>{{ $bid->user->name }}</dd>
                                </dl>
                            </div>
                        </div>

                        {{-- Acción: Solicitar finalización (solo si aceptado y policy lo permite) --}}
                        @if ($puedoSolicitar)
                            <div id="requestCompletionForm" class="mt-4">
                                <form action="{{ route('work.request-completion', $bid) }}" method="POST"
                                    class="mt-3 form-ajax">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-lg w-100 bid-action-button"
                                        id="requestCompletionBtn" data-action="request_completion"
                                        data-loading-text="<i class='fas fa-spinner fa-spin me-2'></i> Procesando...">
                                        <i class="fas fa-flag-checkered me-2"></i> Solicitar Finalización del Trabajo
                                    </button>
                                </form>
                            </div>
                        @endif

                        {{-- Bloque de confirmación cuando está en pendiente_confirmacion --}}
                        @if ($bid->estado === 'pendiente_confirmacion')
                            <div id="confirmationAlert" class="alert {{ $alertClass }} mt-3 confirmation-alert"
                                role="alert" aria-live="polite">
                                <div class="alert-heading">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>
                                        @if ($usuarioActualConfirmo)
                                            Confirmación enviada
                                        @elseif($otroUsuarioConfirmo)
                                            Acción requerida
                                        @else
                                            Pendiente de confirmación
                                        @endif
                                    </strong>
                                </div>

                                <div class="alert-text mb-2">
                                    @if ($usuarioActualConfirmo)
                                        Has confirmado la finalización de este trabajo. Esperando confirmación de la otra
                                        parte.
                                    @elseif($otroUsuarioConfirmo)
                                        La otra parte ha confirmado la finalización de este trabajo. Por favor, confirma o
                                        rechaza la solicitud.
                                    @else
                                        El trabajo está pendiente de confirmación por ambas partes.
                                    @endif
                                </div>

                                {{-- Botones para el usuario que aún no confirma (si la policy lo permite) --}}
                                @if ($puedoConfirmar || $puedoRechazar)
                                    @if (!$usuarioActualConfirmo)
                                        <div id="confirmationButtons" class="confirmation-actions mt-2">
                                            <p class="mb-2 w-100">¿Deseas confirmar la finalización de este trabajo?</p>

                                            @can('confirmCompletion', $bid)
                                                <form action="{{ route('work.confirm-completion', $bid) }}" method="POST"
                                                    class="form-ajax" data-method="confirm">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success bid-action-button"
                                                        data-action="confirm_completion"
                                                        data-loading-text="<i class='fas fa-spinner fa-spin me-1'></i> Procesando...">
                                                        <i class="fas fa-check-circle me-1"></i> Confirmar Finalización
                                                    </button>
                                                </form>

                                                <form action="{{ route('work.reject-completion', $bid) }}" method="POST"
                                                    class="form-ajax" data-method="reject">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger bid-action-button"
                                                        data-action="reject_completion"
                                                        data-loading-text="<i class='fas fa-spinner fa-spin me-1'></i> Procesando...">
                                                        <i class="fas fa-times-circle me-1"></i> Rechazar
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endif

                        {{-- Mensaje final si ya está terminado --}}
                        @if ($bid->estado === 'terminado')
                            <div class="alert alert-success mt-3">
                                <i class="fas fa-check-circle me-2"></i>
                                Este servicio ha sido completado exitosamente y está marcado como terminado.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- CARD: Información técnica del servicio --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Información del Servicio</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Tipo:</strong>
                            {{ $bid->bideable_type === 'App\\Models\\OfertaCarga' ? 'Transporte de Carga' : 'Servicio de Ruta' }}
                        </p>

                        @if ($bid->bideable_type === 'App\\Models\\OfertaCarga')
                            <p class="mb-1"><strong>Tipo de Carga:</strong>
                                {{ $bid->bideable->cargoType->name ?? 'No especificado' }}</p>
                            <p class="mb-1"><strong>Peso:</strong> {{ number_format($bid->bideable->peso, 2) }} kg</p>
                            @if (!is_null($bid->bideable->unidades))
                                <p class="mb-1"><strong>Unidades:</strong> {{ number_format($bid->bideable->unidades) }}
                                </p>
                            @endif
                            <p class="mb-1"><strong>Presupuesto:</strong>
                                ${{ number_format($bid->bideable->presupuesto, 2) }}</p>
                            @if ($bid->bideable->es_contenedor)
                                <p class="mb-0"><strong>Contenedor:</strong> Sí</p>
                            @endif
                        @else
                            <p class="mb-1"><strong>Tipo de Camión:</strong>
                                {{ $bid->bideable->truckType->name ?? 'No especificado' }}</p>
                            <p class="mb-1"><strong>Capacidad:</strong> {{ number_format($bid->bideable->capacidad, 2) }}
                                kg</p>
                            @if (!is_null($bid->bideable->unidades))
                                <p class="mb-1"><strong>Unidades:</strong> {{ number_format($bid->bideable->unidades) }}
                                </p>
                            @endif
                            <p class="mb-0"><strong>Precio Referencial:</strong>
                                ${{ number_format($bid->bideable->precio_referencial, 2) }}</p>
                        @endif

                        <hr>
                        <p class="mb-1"><strong>Comentarios del Transportista:</strong></p>
                        <div class="alert alert-light mb-0">
                            {{ $bid->comentario ?: 'Sin comentarios adicionales.' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: Chat --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Chat del Servicio</h5>
                    </div>
                    <div class="card-body">
                        <div id="chat-messages" class="chat-messages mb-4" style="height: 400px; overflow-y: auto;">
                            @foreach ($chat->messages as $message)
                                <div class="message mb-3 {{ $message->user_id === Auth::id() ? 'text-end' : '' }}"
                                    data-id="{{ $message->id }}">
                                    @if (isset($message->is_system) && $message->is_system)
                                        <div class="d-inline-block p-2 rounded bg-light text-center w-100 text-muted small">
                                            <i class="fas fa-info-circle me-1"></i>{{ $message->content }}
                                        </div>
                                    @else
                                        <div class="d-inline-block p-2 rounded {{ $message->user_id === Auth::id() ? 'bg-primary text-white' : 'bg-light' }}"
                                            style="max-width: 80%;">
                                            <div class="small fw-bold mb-1">{{ $message->user->name }}</div>
                                            {{ $message->content }}
                                            <div
                                                class="small text-{{ $message->user_id === Auth::id() ? 'light' : 'muted' }}">
                                                {{ $message->created_at->format('H:i') }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <form id="message-form" action="{{ route('chats.message', $chat) }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="_ajax" value="true">
                            <div class="input-group">
                                <input type="text" name="message" id="message-input" class="form-control"
                                    placeholder="Escribe un mensaje..." required
                                    {{ $bid->estado === 'terminado' ? 'disabled' : '' }}>
                                <button type="submit" id="send-message-btn" class="btn btn-primary bid-action-button"
                                    data-action="send_message" {{ $bid->estado === 'terminado' ? 'disabled' : '' }}>
                                    <span class="button-text">Enviar</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true"></span>
                                </button>
                            </div>
                            @if ($bid->estado === 'terminado')
                                <div class="form-text text-center mt-2">El chat está deshabilitado porque el servicio ha
                                    sido terminado.</div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar chat luego de que los scripts globales estén listos
            setTimeout(function() {
                const chatElement = document.querySelector('#chat-messages');
                if (!chatElement) {
                    console.warn('Elemento del chat no encontrado en work-progress');
                    return;
                }
                if (typeof window.ChatHandler === 'undefined') {
                    console.error(
                        'Error: La clase ChatHandler no está disponible. Asegúrate de cargarla en app.js.'
                    );
                    return;
                }

                try {
                    const chat = new window.ChatHandler({
                        chatContainerSelector: '#chat-messages',
                        messageFormSelector: '#message-form',
                        messageInputSelector: '#message-input',
                        sendButtonSelector: '#send-message-btn',
                        chatId: {{ $chat->id }},
                        userId: {{ Auth::id() }},
                        isCompact: true,
                        isWorkCompleted: {{ $bid->estado === 'terminado' ? 'true' : 'false' }}
                    });

                    chat.init().catch(error => {
                        console.error('Error al inicializar el chat en work-progress:', error);
                    });

                    window.workProgressChat = chat; // Para depuración
                } catch (error) {
                    console.error('Error general al inicializar el chat:', error);
                }
            }, 500);
        });
    </script>

    {{-- Módulo que escucha BidStatusUpdated / maneja .form-ajax / badges, etc. --}}
    <script src="{{ asset('js/work-status.js') }}" defer></script>
@endpush
