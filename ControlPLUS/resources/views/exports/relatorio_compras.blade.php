<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>FORNECEDOR</th>
            <th>DATA</th>
            <th>VALOR</th>
            @if(__countLocalAtivo() > 1)
            <th>LOCAL</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item->id }}</td>
            <td>{{ $item->fornecedor ? $item->fornecedor->razao_social : '--' }}</td>
            <td>{{ __data_pt($item->created_at) }}</td>
            <td>{{ __moeda($item->total) }}</td>
            @if(__countLocalAtivo() > 1)
            <td>{{ $item->localizacao->descricao ?? '--' }}</td>
            @endif
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>TOTAL DE COMPRAS</td>
            <td>{{ __moeda($data->sum('total')) }}</td>
        </tr>
    </tfoot>
</table>
