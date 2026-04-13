@extends('relatorios.default')
@section('content')

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px; width: 100%;">
    <thead>
        <tr>
            <th>#</th>
            <th>Nome do Cliente</th>
            <th>CPF/CNPJ</th>
            <th>Vendedor</th>
            <th>Data</th>
            <th>Tipo</th>
            <th>Produto</th>
            <th>Categoria</th>
            <th>Qtd.</th>
            <th>Vl. Unit.</th>
            <th>Subtotal</th>
            <th>Total da Venda</th>
            @if(__countLocalAtivo() > 1)
            <th>Local</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @php $total = 0; $totalContado = []; @endphp
        @foreach($data as $key => $item)
        <tr class="@if($key%2 == 0) pure-table-odd @endif">
            <td>{{ $item['id'] }}</td>
            <td class="text-left">{{ $item['cliente_nome'] }}</td>
            <td>{{ $item['cliente_cpf'] }}</td>
            <td>{{ $item['vendedor'] ?? '--' }}</td>
            <td>{{ __data_pt($item['data']) }}</td>
            <td>{{ $item['tipo'] }}</td>
            <td class="text-left">{{ $item['produto'] }}</td>
            <td>{{ $item['categoria'] }}</td>
            <td>{{ $item['quantidade'] }}</td>
            <td>{{ $item['valor_unitario'] !== '--' ? __moeda($item['valor_unitario']) : '--' }}</td>
            <td>{{ $item['sub_total'] !== '--' ? __moeda($item['sub_total']) : '--' }}</td>
            <td>{{ __moeda($item['total']) }}</td>
            @if(__countLocalAtivo() > 1)
            <td class="text-danger">{{ $item['localizacao']->descricao ?? '--' }}</td>
            @endif
        </tr>
        @php
            if (!in_array($item['id'] . '_' . $item['tipo'], $totalContado)) {
                $total += $item['total'];
                $totalContado[] = $item['id'] . '_' . $item['tipo'];
            }
        @endphp
        @endforeach
    </tbody>
</table>
<h6>Total de Registros (vendas): {{ count($totalContado) }}</h6>
<h6>Total de Linhas (itens): {{ sizeof($data) }}</h6>
<h4>Total de Vendas: R$ {{ __moeda($total) }}</h4>
@endsection
