<table>
    <thead>
        <tr>
            <th>PRODUTO</th>
            <th>CÓD. BARRAS</th>
            <th>QTD. VENDAS</th>
            <th>QTD. ITENS</th>
            <th>VALOR TOTAL VENDIDO</th>
            <th>TOTAL CASHBACK GERADO</th>
            <th>% MÉDIO CASHBACK</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item->nome_produto }}</td>
            <td>{{ $item->codigo_barras ?: '' }}</td>
            <td>{{ $item->qtd_vendas }}</td>
            <td>{{ number_format($item->qtd_itens, 0, ',', '.') }}</td>
            <td>R$ {{ __moeda($item->valor_total_vendido) }}</td>
            <td>R$ {{ __moeda($item->total_cashback) }}</td>
            <td>{{ number_format($item->perc_medio, 2, ',', '.') }}%</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4">TOTAIS</td>
            <td>R$ {{ __moeda(collect($data)->sum('valor_total_vendido')) }}</td>
            <td>R$ {{ __moeda(collect($data)->sum('total_cashback')) }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
