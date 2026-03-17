<table>
    <thead>
        <tr>
            <th>PERIODO</th>
            <th>{{ __data_pt($start_date, 0) }} - {{ __data_pt($end_date, 0) }}</th>
        </tr>
    </thead>
</table>

@if($vagos == 1)
<table>
    <thead>
        <tr>
            <th>NOME</th>
            <th>NUMERO</th>
            <th>CATEGORIA</th>
            <th>CAPACIDADE</th>
            <th>VALOR</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item->nome }}</td>
            <td>{{ $item->numero }}</td>
            <td>{{ $item->categoria ? $item->categoria->nome : '' }}</td>
            <td>{{ $item->capacidade }}</td>
            <td>{{ __moeda($item->valor_diaria) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<table>
    <thead>
        <tr>
            <th>CLIENTE</th>
            <th>VALOR TOTAL</th>
            <th>VALOR DE ESTADIA</th>
            <th>VALOR OUTROS</th>
            <th>DATA DE CRIACAO</th>
            <th>QTD. HOSPEDES</th>
            <th>ESTADO</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item->cliente->razao_social }}</td>
            <td>{{ __moeda($item->valor_total) }}</td>
            <td>{{ __moeda($item->valor_estadia) }}</td>
            <td>{{ __moeda($item->valor_total - $item->valor_estadia) }}</td>
            <td>{{ __data_pt($item->created_at) }}</td>
            <td>{{ $item->total_hospedes }}</td>
            <td>{{ $item->estado }}</td>
        </tr>
        <tr>
            <td>DATA CHECKIN</td>
            <td>{{ __data_pt($item->data_checkin, 0) }}</td>
            <td>DATA CHECKOUT</td>
            <td>{{ __data_pt($item->data_checkout, 0) }}</td>
            <td>ACOMODACAO</td>
            <td colspan="2">{{ $item->acomodacao->nome }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>TOTAL</td>
            <td>R$ {{ __moeda($data->sum('valor_total')) }}</td>
        </tr>
    </tfoot>
</table>
@endif
