<div class="mb-4">
    <h3>Ofertas Recibidas</h3>
    @if($bids->isEmpty())
        <p class="text-muted">No hay ofertas aún</p>
    @else
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bids as $bid)
                        <tr>
                            <td>{{ $bid->user->name }}</td>
                            <td>${{ number_format($bid->monto, 2) }}</td>
                            <td>{{ $bid->fecha_hora->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $bid->estado === 'aceptado' ? 'success' : ($bid->estado === 'rechazado' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($bid->estado) }}
                                </span>
                            </td>
                            <td class="d-flex gap-2">
                                @include('bids._bid_actions', ['bid' => $bid])
                                @if($bid->estado === 'aceptado' || Auth::id() === $bid->user_id || Auth::id() === $bid->bideable->user_id)
                                    <form action="{{ route('chats.store', $bid) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-comments"></i> Chat
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
