<table>
    <thead>
        <tr>
            <th>TIPO DE DESPESA</th>
            <th>FRETE</th>
            <th>FORNECEDOR</th>
            <th>VALOR</th>
            <th>OBSERVACAO</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item->tipoDespesaFrete->nome ?? '--' }}</td>
            <td>#{{ $item->frete->numero_sequencial ?? '--' }}</td>
            <td>{{ $item->fornecedor ? $item->fornecedor->info : '' }}</td>
            <td>{{ __moeda($item->valor) }}</td>
            <td>{{ $item->observacao }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>SOMA</td>
            <td>{{ __moeda($data->sum('valor')) }}</td>
        </tr>
    </tfoot>
</table>
