<table>
    <thead>
        <tr>
            <th>{{ $empresa->nome }}</th>
            <th>REGISTRO DE INVENTARIO</th>
            <th>Livro.: {{ $livro }}</th>
        </tr>
        <tr>
            <th>INSC. ESTADUAL.: {{ $empresa->ie }}</th>
            <th>CNPJ(MF): {{ $empresa->cpf_cnpj }}</th>
            <th>existente em: {{ $date }}</th>
        </tr>
    </thead>
</table>

<table>
    <thead>
        <tr>
            <th>NCM</th>
            <th>DESCRICAO DO ARTIGO</th>
            <th>QUANTIDADE</th>
            <th>UNIDADE</th>
            <th>CUSTO UNITARIO</th>
            <th>CUSTO TOTAL</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        @if($item->quantidade > 0)
        <tr>
            <td>{{ preg_replace('/[^0-9]/', '', $item->produto->ncm) }}</td>
            <td>{{ $item->produto->nome }}</td>
            <td>{{ number_format($item->quantidade, 4, ',', '') }}</td>
            <td>{{ $item->produto->unidade }}</td>
            <td>{{ __moeda($item->valor_unitario) }}</td>
            <td>{{ __moeda($item->sub_total) }}</td>
        </tr>
        @endif
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5">Total........:</td>
            <td>{{ __moeda($data->sum('sub_total')) }}</td>
        </tr>
    </tfoot>
</table>
