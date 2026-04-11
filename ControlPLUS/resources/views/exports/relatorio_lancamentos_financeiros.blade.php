<table>
    <thead>
        <tr>
            <th>TIPO</th>
            <th>DESCRIÇÃO</th>
            <th>CLIENTE / FORNECEDOR</th>
            <th>CATEGORIA</th>
            <th>VENCIMENTO</th>
            <th>VALOR</th>
            <th>STATUS</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item['tipo'] === 'receber' ? 'Receber' : 'Pagar' }}</td>
            <td>{{ $item['descricao'] ?: '' }}</td>
            <td>{{ $item['pessoa'] ?: '' }}</td>
            <td>{{ $item['categoria'] ?: '' }}</td>
            <td>{{ __data_pt($item['data_vencimento'], 0) }}</td>
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
            <td colspan="5">TOTAL A RECEBER</td>
            <td>R$ {{ __moeda($total_receber) }}</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="5">TOTAL A PAGAR</td>
            <td>R$ {{ __moeda($total_pagar) }}</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="5">SALDO</td>
            <td>R$ {{ __moeda(abs($saldo)) }} {{ $saldo >= 0 ? '(positivo)' : '(negativo)' }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
