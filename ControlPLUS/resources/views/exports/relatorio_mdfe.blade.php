<table>
    <thead>
        <tr>
            <th>REMETENTE</th>
            <th>VALOR DA CARGA</th>
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
            <td>{{ $item->empresa->nome ?? '--' }}</td>
            <td>{{ __moeda($item->valor_carga) }}</td>
            <td>{{ $item->mdfe_numero }}</td>
            <td>{{ $item->chave }}</td>
            <td>{{ strtoupper($item->estado_emissao) }}</td>
            <td>{{ __data_pt($item->created_at) }}</td>
            @if(__countLocalAtivo() > 1)
            <td>{{ $item->localizacao->descricao ?? '--' }}</td>
            @endif
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>TOTAL</td>
            <td>{{ __moeda($data->sum('valor_carga')) }}</td>
        </tr>
    </tfoot>
</table>
