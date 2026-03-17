<table>
    <thead>
        <tr>
            <th>CLIENTE</th>
            <th>VALOR</th>
            <th>NUM. DOC</th>
            <th>CHAVE</th>
            <th>ESTADO</th>
            <th>DATA</th>
            @if(__countLocalAtivo() > 1)
            <th>LOCAL</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item->cliente ? $item->cliente->info : 'consumidor final' }}</td>
            <td>{{ __moeda($item->total) }}</td>
            <td>{{ $item->numero }}</td>
            <td>{{ $item->chave }}</td>
            <td>{{ strtoupper($item->estado) }}</td>
            <td>{{ __data_pt($item->data_emissao) }}</td>
            @if(__countLocalAtivo() > 1)
            <td>{{ $item->localizacao->descricao ?? '--' }}</td>
            @endif
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>TOTAL</td>
            <td>{{ __moeda($data->sum('total')) }}</td>
        </tr>
    </tfoot>
</table>
