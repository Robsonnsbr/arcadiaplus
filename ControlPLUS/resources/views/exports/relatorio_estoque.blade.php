@php
    $isEstoqueCritico = !empty($estoque_critico);
@endphp

<table>
    <thead>
        @if(!empty($deposito))
        <tr>
            <th>DEPÓSITO</th>
            <th>{{ $deposito->nome }}@if($deposito->localizacao) ({{ $deposito->localizacao->descricao }})@endif</th>
        </tr>
        @endif
        <tr>
            <th style="width: 300px">PRODUTO</th>
            <th style="width: 120px">SKU</th>
            <th style="width: 200px">CATEGORIA</th>
            <th style="width: 120px">VALOR DE COMPRA</th>
            <th style="width: 120px">VALOR DE VENDA</th>
            <th style="width: 120px">QUANTIDADE</th>
            <th style="width: 120px">ESTOQUE MÍNIMO</th>
            @if($isEstoqueCritico)
            <th style="width: 160px">ÚLTIMA MOVIMENTAÇÃO</th>
            @endif
            <th style="width: 200px">DATA DE CADASTRO</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $item)
        <tr>
            <td>{{ $item['produto'] }}</td>
            <td>{{ $item['sku'] ?? '--' }}</td>
            <td>{{ $item['categoria'] }}</td>
            <td>{{ __moeda($item['valor_compra']) }}</td>
            <td>{{ __moeda($item['valor_venda']) }}</td>
            <td>{{ $item['quantidade'] }}</td>
            <td>{{ $item['estoque_minimo'] }}</td>
            @if($isEstoqueCritico)
            <td>{{ $item['ultima_movimentacao'] }}</td>
            @endif
            <td>{{ $item['data_cadastro'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
