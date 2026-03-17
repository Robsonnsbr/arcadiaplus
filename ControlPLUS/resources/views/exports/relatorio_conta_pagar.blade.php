<table>
    <thead>
        <tr>
            <th>FORNECEDOR</th>
            <th>VALOR</th>
            <th>VALOR PAGO</th>
            <th>DATA VENCIMENTO</th>
            <th>DATA PAGAMENTO</th>
            <th>ESTADO</th>
            @if(__countLocalAtivo() > 1)
            <th>LOCAL</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item->fornecedor ? $item->fornecedor->razao_social : '--' }}</td>
            <td>{{ __moeda($item->valor_integral) }}</td>
            <td>{{ $item->status ? __moeda($item->valor_pago) : '--' }}</td>
            <td>{{ __data_pt($item->data_vencimento, 0) }}</td>
            <td>{{ $item->status ? __data_pt($item->data_pagamento, 0) : '--' }}</td>
            <td>
                @if($item->status == 0)
                    @if(strtotime($item->data_vencimento) < strtotime(date('Y-m-d')))
                    Em atraso
                    @else
                    Pendente
                    @endif
                @else
                Quitado
                @endif
            </td>
            @if(__countLocalAtivo() > 1)
            <td>{{ $item->localizacao->descricao ?? '--' }}</td>
            @endif
        </tr>
        @if($item->status == 1)
        <tr>
            <td>Desconto R$ {{ __moeda($item->desconto) }}</td>
            <td>Acrescimo {{ __moeda($item->acrescimo) }}</td>
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
            <td>TOTAL</td>
            <td>{{ __moeda($data->sum('valor_integral')) }}</td>
        </tr>
    </tfoot>
</table>
