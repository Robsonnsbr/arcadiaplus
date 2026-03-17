<table>
    <thead>
        <tr>
            <th style="width: 120px">NÚMERO</th>
            <th style="width: 300px">CLIENTE</th>
            <th style="width: 240px">VENDEDOR</th>
            <th style="width: 180px">DATA</th>
            <th style="width: 120px">TIPO</th>
            <th style="width: 140px">VALOR</th>
            @if(__countLocalAtivo() > 1)
            <th style="width: 220px">LOCAL</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item['id'] }}</td>
            <td>{{ $item['cliente'] }}</td>
            <td>{{ $item['vendedor'] ?? '--' }}</td>
            <td>{{ __data_pt($item['data']) }}</td>
            <td>{{ $item['tipo'] }}</td>
            <td>{{ __moeda($item['total']) }}</td>
            @if(__countLocalAtivo() > 1)
            <td>{{ $item['localizacao']->descricao ?? '--' }}</td>
            @endif
        </tr>
        @endforeach
    </tbody>
</table>
