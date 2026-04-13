<table>
    <thead>
        @if($start_date)
        <tr>
            <th>DATA INICIAL DE FILTRO</th>
            <th>{{ __data_pt($start_date, 0) }}</th>
        </tr>
        @endif
        @if($end_date)
        <tr>
            <th>DATA FINAL DE FILTRO</th>
            <th>{{ __data_pt($end_date, 0) }}</th>
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
            <th>TIPO</th>
            <th>QUANTIDADE</th>
            <th>DATA</th>
            <th>MOVIMENTAÇÃO</th>
            <th>PRODUTO</th>
            <th>CATEGORIA</th>
            <th>CÓDIGO</th>
            <th>SERIAL</th>
            <th>VALOR UNITÁRIO</th>
            <th>ESTOQUE ATUAL</th>
            <th>CLIENTE</th>
            <th>USUÁRIO</th>
        </tr>
    </thead>
    <tbody>
        @if(sizeof($data) == 0)
        <tr>
            <td colspan="12">Nenhum registro</td>
        </tr>
        @endif

        @foreach($data as $item)
        <tr>
            <td>{{ $item['tipo'] }}</td>
            <td>{{ $item['quantidade'] }}</td>
            <td>{{ __data_pt($item['data']) }}</td>
            <td>{{ $item['movimentacao'] }}</td>
            <td>{{ $item['produto'] }}</td>
            <td>{{ $item['categoria'] }}</td>
            <td>{{ $item['codigo'] }}</td>
            <td>{{ $item['serial'] }}</td>
            <td>{{ $item['valor'] }}</td>
            <td>{{ $item['estoque_atual'] }}</td>
            <td>{{ $item['cliente'] }}</td>
            <td>{{ $item['usuario'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
