<table>
    <thead>
        <tr>
            <th>#</th>
            <th>CLIENTE</th>
            <th>VALOR</th>
            <th>DATA DE INICIO</th>
            <th>DATA DE ENTREGA</th>
            <th>ESTADO</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item->codigo_sequencial }}</td>
            <td>{{ $item->cliente->info ?? '--' }}</td>
            <td>{{ __moeda($item->valor) }}</td>
            <td>{{ __data_pt($item->data_inicio, 0) }}</td>
            <td>{{ $item->data_entrega ? __data_pt($item->data_entrega, 0) : '--' }}</td>
            <td>
                @if($item->estado == 'pd')
                PENDENTE
                @elseif($item->estado == 'ap')
                APROVADO
                @elseif($item->estado == 'rp')
                REPROVADO
                @elseif($item->estado == 'fz')
                FINALIZADO
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>SOMA</td>
            <td>{{ __moeda($data->sum('valor')) }}</td>
        </tr>
    </tfoot>
</table>
