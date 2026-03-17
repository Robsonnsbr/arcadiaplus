@php
$somaEstoque = 0;
$somaVenda = 0;
$somaCompra = 0;
@endphp

<table>
    <thead>
        <tr>
            <th>TOTAL DE REGISTROS</th>
            <th>{{ sizeof($data) }}</th>
        </tr>
        @if($local)
        <tr>
            <th>LOCAL</th>
            <th>{{ $local->nome }}</th>
        </tr>
        @endif
    </thead>
</table>

<table>
    <thead>
        <tr>
            <th>PRODUTO</th>
            <th>VL. VENDA</th>
            <th>VL. COMPRA</th>
            <th>DT. CADASTRO</th>
            <th>ESTOQUE TOTAL</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        @php
        $estoque = (float)$item->estoqueTotal($local_id);
        $somaEstoque += $estoque;
        $somaVenda += $estoque * $item->valor_unitario;
        $somaCompra += $estoque * $item->valor_compra;
        @endphp
        <tr>
            <td>{{ $item->nome }}</td>
            <td>{{ __moeda($item->valor_unitario) }}</td>
            <td>{{ __moeda($item->valor_compra) }}</td>
            <td>{{ __data_pt($item->created_at) }}</td>
            <td>{{ $item->estoqueTotal($local_id) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>TOTAL DE ITENS NO ESTOQUE</td>
            <td>{{ $somaEstoque }}</td>
        </tr>
        <tr>
            <td>TOTAL VALOR DE VENDA</td>
            <td>R$ {{ __moeda($somaVenda) }}</td>
        </tr>
        <tr>
            <td>TOTAL VALOR DE COMPRA</td>
            <td>R$ {{ __moeda($somaCompra) }}</td>
        </tr>
    </tfoot>
</table>
