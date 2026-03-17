@php $qtd = 0; @endphp

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>PRODUTO</th>
            <th>QUANTIDADE</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        @php $qtd += $item['quantidade']; @endphp
        <tr>
            <td>{{ $item['numero_sequencial'] }}</td>
            <td>{{ $item['produto_nome'] }}</td>
            <td>{{ $item['quantidade'] }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>TOTAL DE ITENS</td>
            <td>{{ sizeof($data) }}</td>
            <td>{{ $qtd }}</td>
        </tr>
    </tfoot>
</table>
