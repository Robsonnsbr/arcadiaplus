@php $soma = 0; @endphp

<table>
    <thead>
        <tr>
            <th>TOTAL DE REGISTROS</th>
            <th>{{ sizeof($data) }}</th>
        </tr>
    </thead>
</table>

<table>
    <thead>
        <tr>
            <th>TIPO DE PAGAMENTO</th>
            <th>VALOR</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $item)
        @php $soma += (float)$item; @endphp
        <tr>
            <td>{{ App\Models\Nfce::getTipoPagamento($key) }}</td>
            <td>{{ __moeda($item) }}</td>
        </tr>
        @endforeach
    </tbody>
    @if(sizeof($data) > 1)
    <tfoot>
        <tr>
            <td>TOTAL</td>
            <td>{{ __moeda($soma) }}</td>
        </tr>
    </tfoot>
    @endif
</table>
