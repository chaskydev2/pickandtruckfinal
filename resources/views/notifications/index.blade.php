@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h5 mb-0">Notificaciones</h2>
                    
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <form action="{{ route('notifications.markAllRead') }}" method="POST" class="d-inline" id="markAllReadForm">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-check-double me-1"></i> Marcar todas como leídas
                            </button>
                        </form>
                    @endif
                </div>

                @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const form = document.getElementById('markAllReadForm');
                        if (form) {
                            form.addEventListener('submit', function(e) {
                                e.preventDefault();
                                
                                fetch(this.action, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        window.location.reload();
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Error al marcar las notificaciones como leídas');
                                });
                            });
                        }
                    });
                </script>
                @endpush

                <div class="list-group list-group-flush">
                    @forelse($notifications as $notification)
                        <div class="list-group-item {{ $notification->read_at ? '' : 'bg-light' }}">
                            
                            {{-- Enlace principal que marca como leída --}}
                            <a href="{{ $notification->data['url'] ?? '#' }}" 
                            onclick="event.preventDefault(); if(window.markAsRead) { window.markAsRead('{{ $notification->id }}').then(() => window.location.href='{{ $notification->data['url'] ?? '#' }}'); }"
                            class="text-decoration-none text-dark d-block">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">
                                        <i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }} me-2"></i>
                                        <strong>{{ $notification->data['title'] ?? 'Notificación' }}</strong>
                                    </h5>
                                    <small>{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 ms-4">{{ $notification->data['message'] ?? '' }}</p>
                            </a>

                            {{-- Lógica para mostrar botones de acción --}}
                            @if(isset($notification->data['actions']) && is_array($notification->data['actions']))
                                <div class="mt-2 text-end border-top pt-2">
                                    @foreach($notification->data['actions'] as $action)
                                        @if(isset($action['is_delete']) && $action['is_delete'])
                                            {{-- Formulario para el botón de eliminar --}}
                                            <form action="{{ $action['url'] }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta publicación?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm {{ $action['class'] ?? 'btn-secondary' }}">
                                                    {{ $action['text'] }}
                                                </button>
                                            </form>
                                        @else
                                            {{-- Botón normal (ej. Modificar/Republicar) --}}
                                            <a href="{{ $action['url'] }}" class="btn btn-sm {{ $action['class'] ?? 'btn-primary' }}">
                                                {{ $action['text'] }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="list-group-item text-center text-muted">
                            <i class="fas fa-bell-slash fa-2x mb-2"></i>
                            <p>No tienes notificaciones.</p>
                        </div>
                    @endforelse
                </div>
                
                <div class="card-footer">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
