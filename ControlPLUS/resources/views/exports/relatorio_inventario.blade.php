@php $soma = 0; @endphp

<table>
    <thead>
        @if($deposito)
        <tr>
            <th>DEPÓSITO</th>
            <th>{{ $deposito->nome }}@if($deposito->localizacao) ({{ $deposito->localizacao->descricao }})@endif</th>
        </tr>
        @else
        <tr>
            <th>EMPRESA</th>
            <th>{{ $empresa->info }}</th>
        </tr>
        @endif
        <tr>
            <th>TOTAL DE REGISTROS</th>
            <th>{{ sizeof($data) }}</th>
        </tr>
        <tr>
            <th>LIVRO</th>
            <th>{{ $livro }}</th>
        </tr>
    </thead>
</table>

<table>
    <thead>
        <tr>
            <th>NCM</th>
            <th>PRODUTO</th>
            <th>UNIDADE</th>
            <th>QUANTIDADE</th>
            <th>CUSTO UNITARIO</th>
            <th>CUSTO TOTAL</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        @php $soma += $item['sub_total']; @endphp
        <tr>
            <td>{{ $item['ncm'] }}</td>
            <td>{{ $item['nome'] }}</td>
            <td>{{ $item['unidade'] }}</td>
            <td>{{ $item['quantidade'] ? $item['quantidade'] : '--' }}</td>
            <td>{{ __moeda($item['custo_unuitario']) }}</td>
            <td>{{ __moeda($item['sub_total']) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>SOMA</td>
            <td>{{ __moeda($soma) }}</td>
        </tr>
    </tfoot>
</table>
