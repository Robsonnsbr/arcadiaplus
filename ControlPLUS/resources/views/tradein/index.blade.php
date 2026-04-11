@extends('layouts.app', ['title' => 'Trade-in'])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h4 class="card-title mb-0">Trade-in</h4>
                    <div class="mt-2 mt-md-0">
                        <a href="{{ route('tradein.inventory.index', ['empresa_id' => request()->empresa_id]) }}"
                           class="btn btn-sm btn-outline-primary">
                            Estoque Trade-in
                        </a>
                    </div>
                </div>
                <div class="card-body border-top">
                    @if($tradeins->count())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Item</th>
                                    <th>Status</th>
                                    <th>Criado em</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($tradeins as $tradein)
                                    @php
                                        $clienteNome = $clientes[$tradein->cliente_id] ?? '-';
                                        $statusLabel = match ($tradein->status) {
                                            \App\Models\Tradein::STATUS_SUBMITTED => 'Submetido',
                                            \App\Models\Tradein::STATUS_IN_REVIEW => 'Em analise',
                                            \App\Models\Tradein::STATUS_COMPLETED => 'Concluido',
                                            \App\Models\Tradein::STATUS_CANCELLED => 'Cancelado',
                                            default => $tradein->status,
                                        };
                                        $statusClass = match ($tradein->status) {
                                            \App\Models\Tradein::STATUS_SUBMITTED => 'bg-info',
                                            \App\Models\Tradein::STATUS_IN_REVIEW => 'bg-warning',
                                            \App\Models\Tradein::STATUS_COMPLETED => 'bg-success',
                                            \App\Models\Tradein::STATUS_CANCELLED => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <tr>
                                        <td>{{ $tradein->id }}</td>
                                        <td>{{ $clienteNome }}</td>
                                        <td>{{ $tradein->nome_item }}</td>
                                        <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                        <td>{{ __data_pt($tradein->created_at, 1) }}</td>
                                        <td class="text-end">
                                            @can('tradein_edit')
                                            <a href="{{ route('tradein.edit', ['id' => $tradein->id, 'empresa_id' => request()->empresa_id]) }}"
                                               class="btn btn-sm btn-primary">
                                                {{ $tradein->status === \App\Models\Tradein::STATUS_COMPLETED ? 'Alterar' : 'Avaliar' }}
                                            </a>
                                            @endcan
                                            @can('tradein_delete')
                                            <form method="POST" action="{{ route('tradein.destroy', ['id' => $tradein->id, 'empresa_id' => request()->empresa_id]) }}"
                                                  style="display:inline;"
                                                  onsubmit="return confirm('Tem certeza que deseja excluir este trade-in?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $tradeins->links() }}
                        </div>
                    @else
                        <p class="mb-0">Nenhum trade-in encontrado.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
