<table>
    <thead>
        <tr>
            <th>CÓDIGO</th>
            <th>CLIENTE</th>
            <th>CIDADE</th>
            <th>ESTADO</th>
            <th>N NFE</th>
            <th>DATA DA VENDA</th>
            <th>VALOR PREVISTO</th>
            <th>VALOR RECEBIDO</th>
            <th>VALOR A RECEBER</th>
            <th>DATA VENCIMENTO</th>
            <th>DATA PAGAMENTO</th>
            <th>QUITADO</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item->codigo }}</td>
            <td>{{ $item->cliente }}</td>
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
            <td colspan="6">TOTAIS</td>
            <td>{{ __moeda($data->sum('valor_previsto')) }}</td>
            <td>{{ __moeda($data->sum('valor_recebido')) }}</td>
            <td>{{ __moeda($data->sum('valor_a_receber')) }}</td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
</table>
