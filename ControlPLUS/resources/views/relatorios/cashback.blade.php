@extends('relatorios.default')
@section('content')

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px; width: 100%;">
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Tipo</th>
            <th>Valor da Venda</th>
            <th>Crédito Gerado</th>
            <th>% Cashback</th>
            <th>Vencimento</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $item)
        <tr class="@if($key%2 == 0) pure-table-odd @endif">
            <td class="text-left">{{ $item->cliente ? $item->cliente->razao_social : '—' }}</td>
            <td>{{ strtoupper($item->tipo) }}</td>
            <td class="text-right">{{ __moeda($item->valor_venda) }}</td>
            <td class="text-right">{{ __moeda($item->valor_credito) }}</td>
            <td>{{ number_format($item->valor_percentual, 2, ',', '.') }}%</td>
            <td>{{ __data_pt($item->data_expiracao, 0) }}</td>
            <td>
                @if($item->status == 1)
                    <span style="color: green;">Ativo</span>
                @elseif($item->status == 2)
                    <span style="color: #888;">Utilizado</span>
                @else
                    <span style="color: red;">Expirado</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<table style="width:100%; margin-top: 8px;">
    <tr>
        <td><strong>Total de registros:</strong> {{ $data->count() }}</td>
        <td class="text-right"><strong>Total créditos gerados: R$ {{ __moeda($data->sum('valor_credito')) }}</strong></td>
        <td class="text-right"><strong>Total vendas com cashback: R$ {{ __moeda($data->sum('valor_venda')) }}</strong></td>
    </tr>
</table>

@endsection
