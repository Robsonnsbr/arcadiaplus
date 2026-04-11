@extends('relatorios.default')
@section('content')

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px; width: 100%;">
    <thead>
        <tr>
            <th>Produto</th>
            <th>Cód. Barras</th>
            <th>Qtd. Vendas</th>
            <th>Qtd. Itens</th>
            <th>Valor Total Vendido</th>
            <th>Total Cashback Gerado</th>
            <th>% Médio Cashback</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $item)
        <tr class="@if($key%2 == 0) pure-table-odd @endif">
            <td class="text-left">{{ $item->nome_produto }}</td>
            <td>{{ $item->codigo_barras ?: '—' }}</td>
            <td>{{ $item->qtd_vendas }}</td>
            <td>{{ number_format($item->qtd_itens, 0, ',', '.') }}</td>
            <td class="text-right">R$ {{ __moeda($item->valor_total_vendido) }}</td>
            <td class="text-right">R$ {{ __moeda($item->total_cashback) }}</td>
            <td>{{ number_format($item->perc_medio, 2, ',', '.') }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table style="width:100%; margin-top: 8px;">
    <tr>
        <td><strong>Total de produtos:</strong> {{ count($data) }}</td>
        <td class="text-right"><strong>Total vendido: R$ {{ __moeda(collect($data)->sum('valor_total_vendido')) }}</strong></td>
        <td class="text-right"><strong>Total cashback: R$ {{ __moeda(collect($data)->sum('total_cashback')) }}</strong></td>
    </tr>
</table>

@endsection
