<table>
    <thead>
        @if($local)
        <tr>
            <th>LOCAL</th>
            <th>{{ $local->nome }}</th>
        </tr>
        @endif
        <tr>
            <th>TOTAL DE REGISTROS</th>
            <th>{{ sizeof($data) }}</th>
        </tr>
    </thead>
</table>

<table>
    <thead>
        <tr>
            <th>PRODUTO</th>
            <th>CATEGORIA</th>
            <th>CUSTO MEDIO</th>
            <th>VALOR DE VENDA</th>
            <th>QUANTIDADE</th>
            <th>DATA DE CADASTRO</th>
            <th>NCM</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item['nome'] }}</td>
            <td>{{ $item['categoria_nome'] }}</td>
            <td>{{ __moeda($item['custo_medio']) }}</td>
            <td>{{ __moeda($item['valor_unitario']) }}</td>
            <td>{{ $item['quantidade'] ? $item['quantidade'] : '--' }}</td>
            <td>{{ __data_pt($item['created_at']) }}</td>
            <td>{{ $item['ncm'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
