@php
$somaQtdCompra = 0;
$somaQtdSaida = 0;
$somaValorCompra = 0;
$somaValorVenda = 0;
@endphp

<table>
    <thead>
        @if($start_date)
        <tr>
            <th>DATA INICIAL DE FILTRO</th>
            <th>{{ __data_pt($start_date, 0) }}</th>
        </tr>
        @endif
        @if($end_date)
        <tr>
            <th>DATA FINAL DE FILTRO</th>
            <th>{{ __data_pt($end_date, 0) }}</th>
        </tr>
        @endif
        <tr>
            <th>TOTAL DE REGISTROS</th>
            <th>{{ sizeof($data) }}</th>
        </tr>
    </thead>
</table>

<table>
    <thead>
        <tr>
            <th>PRODUTO</th>
            <th>QTD. VENDIDA</th>
            <th>QTD. COMPRADA</th>
            <th>VL. VENDA</th>
            <th>VL. COMPRA</th>
            <th>SUB. VENDA</th>
            <th>SUB. COMPRA</th>
            @if(__countLocalAtivo() > 1)
            <th>LOCAL</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        @php
        $somaQtdCompra += $item['qtd_compra'];
        $somaQtdSaida += $item['qtd_saida'];
        $somaValorCompra += $item['subtotal_compra'];
        $somaValorVenda += $item['subtotal_venda'];
        @endphp
        <tr>
            <td>{{ $item['nome_produto'] }}</td>
            <td>{{ $item['qtd_saida'] }}</td>
            <td>{{ $item['qtd_compra'] }}</td>
            <td>{{ __moeda($item['vl_venda']) }}</td>
            <td>{{ __moeda($item['vl_compra']) }}</td>
            <td>{{ __moeda($item['subtotal_venda']) }}</td>
            <td>{{ __moeda($item['subtotal_compra']) }}</td>
            @if(__countLocalAtivo() > 1)
            <td></td>
            @endif
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>TOTAIS</td>
            <td>{{ $somaQtdSaida }}</td>
            <td>{{ $somaQtdCompra }}</td>
            <td></td>
            <td></td>
            <td>{{ __moeda($somaValorVenda) }}</td>
            <td>{{ __moeda($somaValorCompra) }}</td>
            @if(__countLocalAtivo() > 1)
            <td></td>
            @endif
        </tr>
    </tfoot>
</table>
