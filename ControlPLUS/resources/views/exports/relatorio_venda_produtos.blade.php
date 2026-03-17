<table>
    <thead>
        <tr>
            <th>DATA INICIAL</th>
            <th>{{ $start_date ? __data_pt($start_date, 0) : '--' }}</th>
        </tr>
        <tr>
            <th>DATA FINAL</th>
            <th>{{ $end_date ? __data_pt($end_date, 0) : '--' }}</th>
        </tr>
    </thead>
</table>

<table>
    <thead>
        <tr>
            <th>DATA</th>
            <th>PRODUTO</th>
            <th>QUANTIDADE</th>
            <th>VALOR VENDA</th>
            <th>VALOR VENDA MEDIA</th>
            <th>SUB TOTAL</th>
            <th>LUCRO</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $grupo)
            @foreach($grupo['itens'] as $item)
            @php $lucro = $item['subtotal'] - ($item['quantidade'] * $item['produto']->valor_compra); @endphp
            <tr>
                <td>{{ __data_pt($grupo['data'], 0) }}</td>
                <td>{{ $item['produto']->nome }} {{ $item['produto']->referencia }}</td>
                <td>{{ __moeda($item['quantidade']) }}</td>
                <td>{{ __moeda($item['valor']) }}</td>
                <td>{{ __moeda($item['media']) }}</td>
                <td>{{ __moeda($item['subtotal']) }}</td>
                <td>{{ __moeda($lucro) }}</td>
            </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
