<div class="btn-group" role="group">
    @if(Auth::id() === $bid->bideable->user_id)
        @if($bid->estado === 'pendiente')
            <form action="{{ route('bids.accept', $bid) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">Aceptar</button>
            </form>
            <form action="{{ route('bids.reject', $bid) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">Rechazar</button>
            </form>
        @endif
        <form action="{{ route('chats.store', $bid) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-comments"></i> Chat
            </button>
        </form>
    @elseif(Auth::id() === $bid->user_id)
        <form action="{{ route('chats.store', $bid) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-comments"></i> Chat
            </button>
        </form>
    @endif
</div>
