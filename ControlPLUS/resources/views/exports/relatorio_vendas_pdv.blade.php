<table>
    <thead>
        <tr>
            <th>STATUS</th>
            <th>DATA</th>
            <th>CÓDIGO</th>
            <th>EMPRESA</th>
            <th>VENDEDOR</th>
            <th>CAIXA</th>
            <th>CLIENTE</th>
            <th>DESCONTO</th>
            <th>VALOR PAGO</th>
            <th>VALOR TOTAL</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item->status }}</td>
            <td>{{ __data_pt($item->data) }}</td>
            <td>{{ $item->codigo }}</td>
            <td>{{ $item->empresa ?? '--' }}</td>
            <td>{{ $item->vendedor ?? '--' }}</td>
            <td>{{ $item->caixa }}</td>
            <td>{{ $item->cliente }}</td>
            <td>{{ __moeda($item->desconto) }}</td>
            <td>{{ __moeda($item->valor_pago) }}</td>
            <td>{{ __moeda($item->valor_total) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7">TOTAIS</td>
            <td>{{ __moeda($data->sum('desconto')) }}</td>
            <td>{{ __moeda($data->sum('valor_pago')) }}</td>
            <td>{{ __moeda($data->sum('valor_total')) }}</td>
        </tr>
    </tfoot>
</table>
