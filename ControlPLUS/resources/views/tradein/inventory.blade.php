@extends('layouts.app', ['title' => 'Estoque Trade-in'])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Estoque Trade-in</h4>
                </div>
                <div class="card-body border-top">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Trade-in</th>
                                    <th>Cliente</th>
                                    <th>Descrição</th>
                                    <th>Serial</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Criado em</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($items as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->tradein_id }}</td>
                                        <td>{{ $clientes[$item->cliente_id] ?? '—' }}</td>
                                        <td>{{ $item->descricao_item }}</td>
                                        <td>{{ $item->serial ?: '—' }}</td>
                                        <td>R$ {{ __moeda($item->valor ?? 0) }}</td>
                                        <td>{{ $item->status }}</td>
                                        <td>{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '—' }}</td>
                                        <td class="text-end">
                                            @if ($item->status === \App\Models\TradeinInventoryItem::STATUS_PENDING_TRANSFER)
                                                <a href="{{ route('tradein.inventory.transfer', ['id' => $item->id, 'empresa_id' => request()->empresa_id]) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    Transferir para estoque real
                                                </a>
                                            @else
                                                <span class="text-muted">Transferido</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">Nenhum item encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $items->appends(['status' => $status])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
