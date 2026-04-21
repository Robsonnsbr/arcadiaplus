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
            <th>TOTAL DE NOTAS</th>
            <th>{{ sizeof($data) }}</th>
        </tr>
    </thead>
</table>

<table>
    <thead>
        <tr>
            <th>CÓDIGO</th>
            <th>Nº NOTA</th>
            <th>SÉRIE</th>
            <th>CHAVE DE ACESSO</th>
            <th>DATA</th>
            <th>CFOP</th>
            <th>EMPRESA</th>
            <th>FORNECEDOR</th>
            <th>TOTAL ITENS</th>
            <th>DESCONTOS</th>
            <th>OUTRAS DESPESAS</th>
            <th>VALOR TOTAL</th>
            <th>ICMS</th>
            <th>ICMS ST</th>
            <th>IPI</th>
        </tr>
    </thead>
    <tbody>
        @if(sizeof($data) == 0)
        <tr>
            <td colspan="15">Nenhum registro</td>
        </tr>
        @endif

        @foreach($data as $item)
        <tr>
            <td>{{ $item['codigo'] }}</td>
            <td>{{ $item['numero'] }}</td>
            <td>{{ $item['serie'] }}</td>
            <td>{{ $item['chave'] }}</td>
            <td>{{ __data_pt($item['data'], 0) }}</td>
            <td>{{ $item['cfop'] }}</td>
            <td>{{ $item['empresa'] }}</td>
            <td>{{ $item['fornecedor'] }}</td>
            <td>{{ __moeda($item['valor_produtos']) }}</td>
            <td>{{ __moeda($item['desconto']) }}</td>
            <td>{{ __moeda($item['outras_despesas']) }}</td>
            <td>{{ __moeda($item['valor_total']) }}</td>
            <td>{{ __moeda($item['icms']) }}</td>
            <td>{{ __moeda($item['icms_st']) }}</td>
            <td>{{ __moeda($item['ipi']) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
