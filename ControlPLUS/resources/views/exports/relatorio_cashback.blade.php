<table>
    <thead>
        <tr>
            <th>CLIENTE</th>
            <th>TIPO</th>
            <th>VALOR DA VENDA</th>
            <th>CRÉDITO GERADO</th>
            <th>% CASHBACK</th>
            <th>VENCIMENTO</th>
            <th>STATUS</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item->cliente ? $item->cliente->razao_social : '' }}</td>
            <td>{{ strtoupper($item->tipo) }}</td>
            <td>{{ __moeda($item->valor_venda) }}</td>
            <td>{{ __moeda($item->valor_credito) }}</td>
            <td>{{ number_format($item->valor_percentual, 2, ',', '.') }}%</td>
            <td>{{ __data_pt($item->data_expiracao, 0) }}</td>
            <td>
                @if($item->status == 1) Ativo
                @elseif($item->status == 2) Utilizado
                @else Expirado
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">TOTAL</td>
            <td>{{ __moeda($data->sum('valor_venda')) }}</td>
            <td>{{ __moeda($data->sum('valor_credito')) }}</td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
</table>
