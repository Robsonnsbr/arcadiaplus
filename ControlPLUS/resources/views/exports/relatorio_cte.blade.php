<table>
    <thead>
        <tr>
            <th>REMETENTE</th>
            <th>DESTINATARIO</th>
            <th>VALOR A RECEBER</th>
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
            <td>{{ $item->remetente->razao_social ?? '--' }}</td>
            <td>{{ $item->destinatario->razao_social ?? '--' }}</td>
            <td>{{ __moeda($item->valor_receber) }}</td>
            <td>{{ $item->numero }}</td>
            <td>{{ $item->chave }}</td>
            <td>{{ strtoupper($item->estado) }}</td>
            <td>{{ __data_pt($item->created_at) }}</td>
            @if(__countLocalAtivo() > 1)
            <td>{{ $item->localizacao->descricao ?? '--' }}</td>
            @endif
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>TOTAL A RECEBER</td>
            <td>{{ __moeda($data->sum('valor_receber')) }}</td>
        </tr>
    </tfoot>
</table>
