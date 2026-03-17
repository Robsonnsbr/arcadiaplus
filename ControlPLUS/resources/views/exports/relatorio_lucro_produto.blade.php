<table>
    <thead>
        <tr>
            <th>PERIODO</th>
            <th><strong>{{ $start_date ? __data_pt($start_date, 0) : 'nao definido' }}</strong> ate <strong>{{ $end_date ? __data_pt($end_date, 0) : 'nao definido' }}</strong></th>
        </tr>
    </thead>
</table>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>PRODUTO</th>
            <th>TOTAL EM VENDAS</th>
            <th>TOTAL EM COMPRAS</th>
            <th>LUCRO</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item['numero_sequencial'] }}</td>
            <td>{{ $item['produto_nome'] }}</td>
            <td>{{ __moeda($item['total_vendas']) }}</td>
            <td>{{ __moeda($item['total_compras']) }}</td>
            <td>{{ __moeda($item['total_vendas'] - $item['total_compras']) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
