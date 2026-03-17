@php $soma = 0; @endphp

<table>
    <thead>
        <tr>
            <th>CLIENTE</th>
            <th>VALOR DA VENDA</th>
            <th>VALOR DE CUSTO</th>
            <th>DATA</th>
            <th>LUCRO</th>
            @if(__countLocalAtivo() > 1)
            <th>LOCAL</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        @php $soma += $item['valor_venda'] - $item['valor_custo']; @endphp
        <tr>
            <td>{{ $item['cliente'] }}</td>
            <td>{{ __moeda($item['valor_venda']) }}</td>
            <td>{{ __moeda($item['valor_custo']) }}</td>
            <td>{{ $item['data'] }}</td>
            <td>{{ __moeda($item['valor_venda'] - $item['valor_custo']) }}</td>
            @if(__countLocalAtivo() > 1)
            <td>{{ $item['localizacao']->descricao ?? '--' }}</td>
            @endif
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>TOTAL LUCRO</td>
            <td>R$ {{ __moeda($soma) }}</td>
        </tr>
    </tfoot>
</table>
