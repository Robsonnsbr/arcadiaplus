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
            <th>CÓDIGO</th>
            <th>Nº NOTA</th>
            <th>PRODUTO</th>
            <th>FORNECEDOR</th>
            <th>DEPÓSITO</th>
            <th>DATA ENTRADA</th>
            <th>QUANTIDADE</th>
            <th>VALOR UNIT.</th>
            <th>VALOR TOTAL</th>
        </tr>
    </thead>
    <tbody>
        @if(sizeof($data) == 0)
        <tr>
            <td colspan="9">Nenhum registro</td>
        </tr>
        @endif

        @foreach($data as $item)
        <tr>
            <td>{{ $item['codigo'] }}</td>
            <td>{{ $item['numero_nota'] }}</td>
            <td>{{ $item['produto'] }}</td>
            <td>{{ $item['fornecedor'] }}</td>
            <td>{{ $item['deposito'] }}</td>
            <td>{{ __data_pt($item['data_entrada'], 0) }}</td>
            <td>{{ number_format((float)$item['quantidade'], 2, ',', '.') }}</td>
            <td>{{ __moeda($item['valor_unitario']) }}</td>
            <td>{{ __moeda($item['valor_total']) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
