<table>
    <thead>
        <tr>
            <th>CLIENTE</th>
            <th>VALOR</th>
            <th>DATA VENCIMENTO</th>
            <th>ESTADO</th>
            <th>PARCELA</th>
            <th>N PEDIDO / N NFE</th>
            @if(__countLocalAtivo() > 1)
            <th>LOCAL</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item->cliente ? $item->cliente->razao_social : '' }}</td>
            <td>{{ __moeda($item->valor_integral) }}</td>
            <td>{{ __data_pt($item->data_vencimento, 0) }}</td>
            <td>
                @if($item->status == 0)
                    @if(strtotime($item->data_vencimento) < strtotime(date('Y-m-d')))
                    Em atraso
                    @else
                    Pendente
                    @endif
                @else
                Recebido
                @endif
            </td>
            @if($item->nfe)
            <td>{{ $item->contaFatura() }}</td>
            <td>
                {{ $item->nfe->numero_sequencial }}
                @if($item->nfe->estado == 'aprovado')
                /{{ $item->nfe->numero }}
                @endif
            </td>
            @else
            <td>--</td>
            <td>--</td>
            @endif
            @if(__countLocalAtivo() > 1)
            <td>{{ $item->localizacao->descricao ?? '--' }}</td>
            @endif
        </tr>
        @if($item->status == 1)
        <tr>
            <td>Valor recebido R$ {{ __moeda($item->valor_recebido) }}</td>
            <td>Recebimento {{ __data_pt($item->data_recebimento, 0) }}</td>
            <td colspan="5">
                @if($item->contaEmpresa)
                Conta {{ $item->contaEmpresa->nome }}
                @endif
            </td>
        </tr>
        @endif
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>TOTAL A RECEBER</td>
            <td>{{ __moeda($data->sum('valor_integral')) }}</td>
        </tr>
    </tfoot>
</table>
