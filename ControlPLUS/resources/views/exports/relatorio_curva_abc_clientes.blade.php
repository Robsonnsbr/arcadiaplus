<table>
    <thead>
        <tr>
            <th>CLIENTE</th>
            <th>QTD. VENDAS</th>
            <th>TOTAL VENDAS</th>
            <th>PERCENTUAL</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item['nome'] }}</td>
            <td>{{ $item['count'] }}</td>
            <td>{{ __moeda($item['total']) }}</td>
            <td>{{ $item['percentual'] }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>SOMA</td>
            <td>R$ {{ __moeda($soma) }}</td>
        </tr>
    </tfoot>
</table>
