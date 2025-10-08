<a href="{{ $notification->data['url'] ?? '#' }}" 
   class="dropdown-item {{ !$notification->read_at ? 'bg-light' : '' }}"
   onclick="event.preventDefault(); document.getElementById('mark-notification-{{ $notification->id }}').submit();">
    
    @if(isset($notification->data['icon']))
        <i class="{{ $notification->data['icon'] }} me-2"></i>
    @else
        <i class="fas fa-bell text-primary me-2"></i>
    @endif
    
    <div class="d-flex flex-column">
        <span class="fw-bold">{{ $notification->data['message'] }}</span>
        
        @if(isset($notification->data['origen']) && isset($notification->data['destino']))
            <small class="text-muted">
                {{ $notification->data['origen'] }} → {{ $notification->data['destino'] }}
            </small>
        @endif
        
        @if(isset($notification->data['dias_restantes']))
            <small class="badge bg-warning text-dark mt-1">
                Vence en {{ $notification->data['dias_restantes'] }} día(s)
            </small>
        @endif
        
        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
    </div>
</a>

<form id="mark-notification-{{ $notification->id }}" action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST" class="d-none">
    @csrf
</form>
