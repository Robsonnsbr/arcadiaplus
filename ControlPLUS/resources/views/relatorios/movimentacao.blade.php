@extends('relatorios.default')
@section('content')

<style>
    /* Wrapper: scroll horizontal apenas quando necessário (browser) */
    .mov-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
    }

    /* Forçar distribuição proporcional entre colunas */
    .mov-table {
        table-layout: fixed;
        width: 100%;
        min-width: 860px; /* evita colapso em telas muito estreitas */
    }

    /* Quebra de linha em vez de expandir a coluna */
    .mov-table th,
    .mov-table td {
        word-break: break-word;
        overflow-wrap: break-word;
        white-space: normal;
    }
</style>

<h5>Total de registros: <strong>{{ sizeof($data) }}</strong></h5>
@if($start_date)
<p>Data inicial de filtro: <strong>{{ __data_pt($start_date , 0) }}</strong></p>
@endif
@if($end_date)
<p>Data final de filtro: <strong>{{ __data_pt($end_date , 0) }}</strong></p>
@endif

<div class="mov-wrapper">
<table class="table-sm table-borderless mov-table" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;">
    <colgroup>
        <col style="width:5%">   {{-- Tipo --}}
        <col style="width:4%">   {{-- Qtd --}}
        <col style="width:8%">   {{-- Data --}}
        <col style="width:9%">   {{-- Movimentação --}}
        <col style="width:15%">  {{-- Produto --}}
        <col style="width:6%">   {{-- SKU --}}
        <col style="width:9%">   {{-- Categoria --}}
        <col style="width:8%">   {{-- Cód. Transação --}}
        <col style="width:8%">   {{-- Serial --}}
        <col style="width:6%">   {{-- Valor Unit. --}}
        <col style="width:5%">   {{-- Estoque Atual --}}
        <col style="width:13%">  {{-- Cliente/Fornecedor --}}
        <col style="width:7%">   {{-- Usuário --}}
    </colgroup>
    <thead>
        <tr>
            <th>Tipo</th>
            <th>Qtd.</th>
            <th>Data</th>
            <th>Movimentação</th>
            <th class="text-left">Produto</th>
            <th>SKU</th>
            <th>Categoria</th>
            <th>Cód. Transação</th>
            <th>Serial</th>
            <th>Valor Unit.</th>
            <th>Estoque</th>
            <th class="text-left">Cliente/Fornecedor</th>
            <th>Usuário</th>
        </tr>
    </thead>
    <tbody>
        @if(sizeof($data) == 0)
        <tr>
            <td colspan="13">Nenhum registro</td>
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
            <td class="text-left">{{ $item['cliente'] }}</td>
            <td>{{ $item['usuario'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>

@endsection
