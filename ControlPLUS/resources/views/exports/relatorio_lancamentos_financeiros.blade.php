<table>
    <thead>
        <tr>
            <th>CÓDIGO</th>
            <th>TIPO</th>
            <th>DESCRIÇÃO</th>
            <th>CLIENTE/FORNECEDOR</th>
            <th>Nº DOCUMENTO</th>
            <th>CATEGORIA</th>
            <th>PLANO DE CONTAS</th>
            <th>VENCIMENTO</th>
            <th>PAGAMENTO</th>
            <th>FORMA DE PAGAMENTO</th>
            <th>VALOR</th>
            <th>SITUAÇÃO</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item['codigo'] }}</td>
            <td>{{ $item['tipo'] === 'receber' ? 'Receber' : 'Pagar' }}</td>
            <td>{{ $item['descricao'] ?: '' }}</td>
            <td>{{ $item['pessoa'] ?: '' }}</td>
            <td>{{ $item['numero_documento'] }}</td>
            <td>{{ $item['categoria'] ?: '' }}</td>
            <td>{{ $item['plano_contas'] }}</td>
            <td>{{ __data_pt($item['data_vencimento'], 0) }}</td>
            <td>{{ $item['data_pagamento'] ? __data_pt($item['data_pagamento'], 0) : '' }}</td>
            <td>{{ $item['forma_pagamento'] }}</td>
            <td>R$ {{ __moeda($item['valor']) }}</td>
            <td>
                @if($item['status'] == 1) Quitado
                @elseif(strtotime($item['data_vencimento']) < strtotime(date('Y-m-d'))) Em atraso
                @else Pendente
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="10">TOTAL A RECEBER</td>
            <td>R$ {{ __moeda($total_receber) }}</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="10">TOTAL A PAGAR</td>
            <td>R$ {{ __moeda($total_pagar) }}</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="10">SALDO</td>
            <td>R$ {{ __moeda(abs($saldo)) }} {{ $saldo >= 0 ? '(positivo)' : '(negativo)' }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
