@extends('relatorios.default')
@section('content')
<h5>Total de registros: <strong>{{ sizeof($data) }}</strong></h5>
@if($start_date)
<p>Data inicial de filtro: <strong>{{ __data_pt($start_date, 0) }}</strong></p>
@endif
@if($end_date)
<p>Data final de filtro: <strong>{{ __data_pt($end_date, 0) }}</strong></p>
@endif
@if($status == 1)
<p>Status: <strong>Quitado</strong></p>
@elseif($status == -1)
<p>Status: <strong>Aberto</strong></p>
@endif

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px; width: 100%;">
    <thead>
        <tr>
            <th>Código</th>
            <th>Cliente</th>
            <th>Cidade</th>
            <th>Estado</th>
            <th>Nº NFe</th>
            <th>Data da venda</th>
            <th>Valor previsto</th>
            <th>Valor recebido</th>
            <th>Valor a receber</th>
            <th>Data vencimento</th>
            <th>Data pagamento</th>
            <th>Quitado</th>
        </tr>
    </thead>
    <tbody>
        @if(sizeof($data) == 0)
        <tr>
            <td colspan="12">Nenhum registro</td>
        </tr>
        @endif

        @foreach($data as $key => $item)
        <tr class="@if($key%2 == 0) pure-table-odd @endif">
            <td>{{ $item->codigo }}</td>
            <td class="text-left">{{ $item->cliente }}</td>
            <td>{{ $item->cidade ?: '--' }}</td>
            <td>{{ $item->estado ?: '--' }}</td>
            <td>{{ $item->numero_nfe ?: '--' }}</td>
            <td>{{ $item->data_venda ? __data_pt($item->data_venda) : '--' }}</td>
            <td>{{ __moeda($item->valor_previsto) }}</td>
            <td>{{ __moeda($item->valor_recebido) }}</td>
            <td>{{ __moeda($item->valor_a_receber) }}</td>
            <td>{{ $item->data_vencimento ? __data_pt($item->data_vencimento, 0) : '--' }}</td>
            <td>{{ $item->data_pagamento ? __data_pt($item->data_pagamento, 0) : '--' }}</td>
            <td>{{ $item->quitado }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6" class="text-right"><strong>Totais</strong></td>
            <td><strong>{{ __moeda($data->sum('valor_previsto')) }}</strong></td>
            <td><strong>{{ __moeda($data->sum('valor_recebido')) }}</strong></td>
            <td><strong>{{ __moeda($data->sum('valor_a_receber')) }}</strong></td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
</table>
@endsection
