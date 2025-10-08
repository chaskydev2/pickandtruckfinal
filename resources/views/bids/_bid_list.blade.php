<div class="card shadow-sm mt-4">
    <div class="card-header bg-white">
        <h3 class="h5 mb-0">Pujas Recibidas ({{ $bids->count() }})</h3>
    </div>
    <div class="card-body">
        @if($bids->isEmpty())
            <p class="text-muted text-center mb-0">No hay pujas recibidas aún.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Transportista</th>
                            <th>Estado</th>
                            <th>Puja</th>
                            <th>Fecha Propuesta</th>
                            <th>Comentario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bids as $bid)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        {{ $bid->user->name }}
                                        @if($bid->user->verified)
                                            <i class="fas fa-check-circle text-success ms-1" title="Usuario Verificado"></i>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $bid->estado === 'aceptado' ? 'success' : ($bid->estado === 'rechazado' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($bid->estado) }}
                                    </span>
                                </td>
                                <td>${{ number_format($bid->monto, 2) }}</td>
                                <td>{{ $bid->fecha_hora->format('d/m/Y') }}</td>
                                <td>{{ Str::limit($bid->comentario, 30) }}</td>
                                <td>
                                    <div class="btn-group">
                                        @if(Auth::id() === $bid->bideable->user_id)
                                            @if($bid->estado === 'pendiente')
                                                <form action="{{ route('bids.accept', $bid) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('bids.reject', $bid) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <form action="{{ route('chats.store', $bid) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-comments"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
