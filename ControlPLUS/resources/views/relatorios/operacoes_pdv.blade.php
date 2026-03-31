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
            <th>Data</th>
            <th>Tipo de Operação</th>
            <th>Caixa</th>
            <th>Realizador</th>
            <th>Motivo</th>
            <th>Valor</th>
        </tr>
    </thead>
    <tbody>
        @if(sizeof($data) == 0)
        <tr>
            <td colspan="6">Nenhum registro</td>
        </tr>
        @endif

        @foreach($data as $key => $item)
        <tr class="@if($key%2 == 0) pure-table-odd @endif">
            <td>{{ __data_pt($item->data) }}</td>
            <td>{{ $item->tipo_operacao }}</td>
            <td class="text-left">{{ $item->caixa }}</td>
            <td class="text-left">{{ $item->realizador }}</td>
            <td class="text-left">{{ $item->motivo }}</td>
            <td>{{ __moeda($item->valor) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="text-right"><strong>Total</strong></td>
            <td><strong>{{ __moeda($data->sum('valor')) }}</strong></td>
        </tr>
    </tfoot>
</table>
@endsection
