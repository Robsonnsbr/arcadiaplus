<table>
    <thead>
        <tr>
            <th>DATA</th>
            <th>TIPO DE OPERAÇÃO</th>
            <th>CAIXA</th>
            <th>REALIZADOR</th>
            <th>MOTIVO</th>
            <th>VALOR</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ __data_pt($item->data) }}</td>
            <td>{{ $item->tipo_operacao }}</td>
            <td>{{ $item->caixa }}</td>
            <td>{{ $item->realizador }}</td>
            <td>{{ $item->motivo }}</td>
            <td>{{ __moeda($item->valor) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5">TOTAL</td>
            <td>{{ __moeda($data->sum('valor')) }}</td>
        </tr>
    </tfoot>
</table>
