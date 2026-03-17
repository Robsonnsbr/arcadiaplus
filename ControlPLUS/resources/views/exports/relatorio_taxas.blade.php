<table>
    <thead>
        <tr>
            <th>CLIENTE</th>
            <th>TOTAL</th>
            <th>% TAXA</th>
            <th>DATA</th>
            <th>TIPO PAGAMENTO</th>
            <th>TIPO</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item['cliente'] }}</td>
            <td>{{ $item['total'] }}</td>
            <td>{{ __moeda($item['taxa_perc']) }}</td>
            <td>{{ $item['data'] }}</td>
            <td>{{ $item['tipo_pagamento'] }}</td>
            <td>{{ $item['tipo'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
