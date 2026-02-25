@extends('layouts.app', ['title' => 'Trade-in'])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Trade-in</h4>
                </div>
                <div class="card-body border-top">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Item</th>
                                    <th>Cliente</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tradeins as $tradein)
                                    <tr>
                                        <td>#{{ $tradein->id }}</td>
                                        <td>{{ $tradein->nome_item }}</td>
                                        <td>{{ $tradein->cliente_id }}</td>
                                        <td>{{ $tradein->status }}</td>
                                        <td>{{ $tradein->created_at ? $tradein->created_at->format('d/m/Y H:i') : '--' }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('tradein.edit', $tradein->id) }}" class="btn btn-primary btn-sm">
                                                Avaliar
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">Nenhum trade-in pendente.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(isset($tradeins))
                    <div class="card-footer">
                        {{ $tradeins->appends(request()->all())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
