<table>
    <thead>
        <tr>
            <th style="width: 100px">NÚMERO</th>
            <th style="width: 280px">NOME DO CLIENTE</th>
            <th style="width: 160px">CPF/CNPJ</th>
            <th style="width: 200px">VENDEDOR</th>
            <th style="width: 160px">DATA</th>
            <th style="width: 100px">TIPO</th>
            <th style="width: 280px">PRODUTO</th>
            <th style="width: 180px">CATEGORIA</th>
            <th style="width: 80px">QTD.</th>
            <th style="width: 120px">VL. UNITÁRIO</th>
            <th style="width: 120px">SUBTOTAL ITEM</th>
            <th style="width: 140px">TOTAL DA VENDA</th>
            @if(__countLocalAtivo() > 1)
            <th style="width: 200px">LOCAL</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item['id'] }}</td>
            <td>{{ $item['cliente_nome'] }}</td>
            <td>{{ $item['cliente_cpf'] }}</td>
            <td>{{ $item['vendedor'] ?? '--' }}</td>
            <td>{{ __data_pt($item['data']) }}</td>
            <td>{{ $item['tipo'] }}</td>
            <td>{{ $item['produto'] }}</td>
            <td>{{ $item['categoria'] }}</td>
            <td>{{ $item['quantidade'] }}</td>
            <td>{{ $item['valor_unitario'] !== '--' ? __moeda($item['valor_unitario']) : '--' }}</td>
            <td>{{ $item['sub_total'] !== '--' ? __moeda($item['sub_total']) : '--' }}</td>
            <td>{{ __moeda($item['total']) }}</td>
            @if(__countLocalAtivo() > 1)
            <td>{{ $item['localizacao']->descricao ?? '--' }}</td>
            @endif
        </tr>
        @endforeach
    </tbody>
</table>
