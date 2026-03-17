<table>
    <thead>
        <tr>
            <th>FUNCIONARIO</th>
            <th>TIPO</th>
            <th>STATUS</th>
            <th>VALOR DA VENDA</th>
            <th>VALOR DA COMISSAO</th>
            <th>DATA</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item->funcionario->nome ?? '--' }}</td>
            <td>{{ $item->tabela == 'nfce' ? 'PDV' : 'Pedido' }}</td>
            <td>{{ $item->status ? 'Paga' : 'Pendente' }}</td>
            <td>{{ __moeda($item->valor_venda) }}</td>
            <td>{{ __moeda($item->valor) }}</td>
            <td>{{ __data_pt($item->created_at) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>SOMA</td>
            <td></td>
            <td></td>
            <td>{{ __moeda($data->sum('valor_venda')) }}</td>
            <td>{{ __moeda($data->sum('valor')) }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
