@extends('relatorios.default')
@section('content')
<h5>Total de registros: <strong>{{ sizeof($data) }}</strong></h5>
@if($start_date)
<p>Data inicial de filtro: <strong>{{ __data_pt($start_date , 0) }}</strong></p>
@endif
@if($end_date)
<p>Data final de filtro: <strong>{{ __data_pt($end_date , 0) }}</strong></p>
@endif

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px; width: 100%;">
        <thead>
                <tr>
                        <th>Tipo</th>
                        <th>Quantidade</th>
                        <th>Data</th>
                        <th>Movimentação</th>
                        <th style="width: 200px;">Produto</th>
                        <th>SKU</th>
                        <th>Categoria</th>
                        <th>Cód. Transação</th>
                        <th>Serial</th>
                        <th>Valor Unit.</th>
                        <th>Estoque Atual</th>
                        <th>Cliente/Fornecedor</th>
                        <th>Usuário</th>
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
                        <td>{{ $item['tipo'] }}</td>
                        <td>{{ $item['quantidade'] }}</td>
                        <td>{{ __data_pt($item['data']) }}</td>
                        <td>{{ $item['movimentacao'] }}</td>
                        <td class="text-left">{{ $item['produto'] }}</td>
                        <td><code>{{ $item['sku'] ?? '--' }}</code></td>
                        <td>{{ $item['categoria'] }}</td>
                        <td>{{ $item['codigo'] }}</td>
                        <td>{{ $item['serial'] }}</td>
                        <td>{{ $item['valor'] }}</td>
                        <td>{{ $item['estoque_atual'] }}</td>
                        <td>{{ $item['cliente'] }}</td>
                        <td>{{ $item['usuario'] }}</td>
                </tr>
                @endforeach
        </tbody>
</table>

@endsection
