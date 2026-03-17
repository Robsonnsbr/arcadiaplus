@php $total = 0; @endphp

<table>
    <thead>
        <tr>
            <th>VENDEDOR</th>
            <th>{{ $funcionario->nome }}</th>
        </tr>
    </thead>
</table>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>CLIENTE</th>
            <th>DATA</th>
            <th>VALOR</th>
            @if(__countLocalAtivo() > 1)
            <th>LOCAL</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        @php $total += $item['total']; @endphp
        <tr>
            <td>{{ $item['id'] }}</td>
            <td>{{ $item['cliente'] }}</td>
            <td>{{ __data_pt($item['data']) }}</td>
            <td>{{ __moeda($item['total']) }}</td>
            @if(__countLocalAtivo() > 1)
            <td>{{ $item['localizacao']->descricao ?? '--' }}</td>
            @endif
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>TOTAL DE VENDAS</td>
            <td>R$ {{ __moeda($total) }}</td>
        </tr>
    </tfoot>
</table>
