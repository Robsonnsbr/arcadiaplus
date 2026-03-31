@extends('relatorios.default')
@section('content')
<h5>Total de registros: <strong>{{ sizeof($data) }}</strong></h5>
@if($start_date)
<p>Data inicial de filtro: <strong>{{ __data_pt($start_date, 0) }}</strong></p>
@endif
@if($end_date)
<p>Data final de filtro: <strong>{{ __data_pt($end_date, 0) }}</strong></p>
@endif

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px; width: 100%;">
    <thead>
        <tr>
            <th>Status</th>
            <th>Data</th>
            <th>Código</th>
            <th>Empresa</th>
            <th>Vendedor</th>
            <th>Caixa</th>
            <th>Cliente</th>
            <th>Desconto</th>
            <th>Valor Pago</th>
            <th>Valor Total</th>
        </tr>
    </thead>
    <tbody>
        @if(sizeof($data) == 0)
        <tr>
            <td colspan="10">Nenhum registro</td>
        </tr>
        @endif

        @foreach($data as $key => $item)
        <tr class="@if($key%2 == 0) pure-table-odd @endif">
            <td>{{ $item->status }}</td>
            <td>{{ __data_pt($item->data) }}</td>
            <td>{{ $item->codigo }}</td>
            <td class="text-left">{{ $item->empresa ?? '--' }}</td>
            <td class="text-left">{{ $item->vendedor ?? '--' }}</td>
            <td>{{ $item->caixa }}</td>
            <td class="text-left">{{ $item->cliente }}</td>
            <td>{{ __moeda($item->desconto) }}</td>
            <td>{{ __moeda($item->valor_pago) }}</td>
            <td>{{ __moeda($item->valor_total) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7" class="text-right"><strong>Totais</strong></td>
            <td><strong>{{ __moeda($data->sum('desconto')) }}</strong></td>
            <td><strong>{{ __moeda($data->sum('valor_pago')) }}</strong></td>
            <td><strong>{{ __moeda($data->sum('valor_total')) }}</strong></td>
        </tr>
    </tfoot>
</table>
@endsection
