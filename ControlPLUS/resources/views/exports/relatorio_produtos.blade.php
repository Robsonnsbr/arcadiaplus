<table>
    <thead>
        @isset($marca)
            <tr>
                <th>MARCA</th>
                <th>{{ $marca->nome }}</th>
            </tr>
        @endisset
        @isset($categoria)
            <tr>
                <th>CATEGORIA</th>
                <th>{{ $categoria->nome }}</th>
            </tr>
        @endisset
        <tr>
            <th>TOTAL DE REGISTROS</th>
            <th>{{ sizeof($data) }}</th>
        </tr>
    </thead>
</table>

<table>
    <thead>
        <tr>
            <th>PRODUTO</th>
            <th>VL. VENDA</th>
            <th>VL. COMPRA</th>
            <th>DT. CADASTRO</th>
            @if (__countLocalAtivo() > 1)
                <th>DISPONIBILIDADE</th>
            @endif
            <th>ESTOQUE</th>
            @if ($tipo == 1 || $tipo == -1)
                <th>QTD. VENDIDA</th>
            @endif
            @if ($tipo == 2 || $tipo == -2)
                <th>QTD. COMPRADA</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            @if (sizeof($item->variacoes) > 0)
                @foreach ($item->variacoes as $v)
                    <tr>
                        <td>{{ $item->nome }} {{ $v->descricao }}</td>
                        <td>{{ __moeda($item->valor) }}</td>
                        <td>{{ __moeda($item->valor_compra) }}</td>
                        <td>{{ __data_pt($item->created_at) }}</td>
                        @if (__countLocalAtivo() > 1)
                            <td>
                                @php
                                    $estoqueLocais = $item->estoqueLocais
                                        ->filter(function ($estoque) {
                                            return $estoque->local;
                                        })
                                        ->unique('local_id');
                                @endphp
                                @foreach ($estoqueLocais as $e)
                                    {{ $e->local->descricao }}@if (!$loop->last)
                                        |
                                    @endif
                                @endforeach
                            </td>
                        @endif
                        <td>
                            @if (__countLocalAtivo() > 1)
                                @foreach ($item->estoqueLocais as $e)
                                    @if ($e->local && $v->id == $e->produto_variacao_id)
                                        {{ $e->local->descricao }}:
                                        @if ($item->unidade == 'UN' || $item->unidade == 'UNID')
                                            {{ number_format($e->quantidade_vendida, 0) }}
                                        @else
                                            {{ number_format($e->quantidade_vendida, 3) }}
                                        @endif
                                    @endif
                                    @if (!$loop->last)
                                        |
                                    @endif
                                @endforeach
                            @else
                                {{ $item->estoque ? number_format($item->estoque->quantidade, 2) : '0' }} -
                                {{ $item->unidade }}
                            @endif
                        </td>
                        @if ($tipo != '')
                            <td>{{ $item->quantidade_vendida ?? '' }}</td>
                        @endif
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>{{ $item->nome }}</td>
                    <td>{{ __moeda($item->valor_unitario) }}</td>
                    <td>{{ __moeda($item->valor_compra) }}</td>
                    <td>{{ __data_pt($item->created_at) }}</td>
                    @if (__countLocalAtivo() > 1)
                        <td>
                            @php
                                $estoqueLocais = $item->estoqueLocais
                                    ->filter(function ($estoque) {
                                        return $estoque->local;
                                    })
                                    ->unique('local_id');
                            @endphp
                            @foreach ($estoqueLocais as $e)
                                {{ $e->local->descricao }}@if (!$loop->last)
                                    |
                                @endif
                            @endforeach
                        </td>
                    @endif
                    <td>
                        @if (__countLocalAtivo() > 1)
                            @foreach ($item->estoqueLocais as $e)
                                @if ($e->local)
                                    {{ $e->local->descricao }}:
                                    @if ($item->unidade == 'UN' || $item->unidade == 'UNID')
                                        {{ number_format($e->quantidade, 0) }}
                                    @else
                                        {{ number_format($e->quantidade, 3) }}
                                    @endif
                                @endif
                                @if (!$loop->last)
                                    |
                                @endif
                            @endforeach
                        @else
                            {{ $item->estoque ? number_format($item->estoque->quantidade, 2) : '0' }} -
                            {{ $item->unidade }}
                        @endif
                    </td>
                    @if ($tipo != '')
                        <td>{{ $item->quantidade_vendida }}</td>
                    @endif
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
