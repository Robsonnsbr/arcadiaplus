<table>
    <thead>
        <tr>
            <th>RAZAO SOCIAL</th>
            <th>NOME FANTASIA</th>
            <th>CPF/CNPJ</th>
            <th>IE</th>
            <th>ENDERECO</th>
            <th>CIDADE</th>
            <th>DATA DE CADASTRO</th>
            @if($tipo != '')
            <th>TOTAL COMPRADO</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item->razao_social }}</td>
            <td>{{ $item->nome_fantasia }}</td>
            <td>{{ $item->cpf_cnpj }}</td>
            <td>{{ $item->ie }}</td>
            <td>{{ $item->endereco }}</td>
            <td>{{ $item->cidade ? $item->cidade->info : '--' }}</td>
            <td>{{ __data_pt($item->created_at) }}</td>
            @if($tipo != '')
            <td>{{ __moeda($item->total) }}</td>
            @endif
        </tr>
        @endforeach
    </tbody>
    @if($tipo != '')
    <tfoot>
        <tr>
            <td>TOTAL</td>
            <td>{{ __moeda($data->sum('total')) }}</td>
        </tr>
    </tfoot>
    @endif
</table>
