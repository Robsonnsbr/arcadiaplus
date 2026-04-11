<table>
    <thead>
        <tr>
            <th>NF-e</th>
            <th>FORNECEDOR</th>
            <th>DATA</th>
            <th>PRODUTO</th>
            <th>QTD.</th>
            <th>VALOR UNIT.</th>
            <th>SUBTOTAL</th>
            <th>TOTAL NF-e</th>
            @if(__countLocalAtivo() > 1)
            <th>LOCAL</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($data as $nfe)
            @forelse($nfe->itens as $item)
            <tr>
                <td>{{ $nfe->numero ?? $nfe->id }}</td>
                <td>{{ $nfe->fornecedor ? $nfe->fornecedor->razao_social : '--' }}</td>
                <td>{{ __data_pt($nfe->created_at) }}</td>
                <td>
                    @if($item->produto)
                        @if($item->variacao_id && $item->produtoVariacao)
                            {{ $item->produto->nome }} - {{ $item->produtoVariacao->descricao }}
                        @else
                            {{ $item->produto->nome }}
                        @endif
                    @elseif($item->descricao)
                        {{ $item->descricao }}
                    @else
                        --
                    @endif
                </td>
                <td>{{ number_format((float)$item->quantidade, 2, ',', '.') }}</td>
                <td>{{ __moeda($item->valor_unitario) }}</td>
                <td>{{ __moeda($item->sub_total) }}</td>
                <td>{{ $loop->first ? __moeda($nfe->total) : '' }}</td>
                @if(__countLocalAtivo() > 1)
                <td>{{ $loop->first ? ($nfe->localizacao->descricao ?? '--') : '' }}</td>
                @endif
            </tr>
            @empty
            <tr>
                <td>{{ $nfe->numero ?? $nfe->id }}</td>
                <td>{{ $nfe->fornecedor ? $nfe->fornecedor->razao_social : '--' }}</td>
                <td>{{ __data_pt($nfe->created_at) }}</td>
                <td>Sem itens</td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ __moeda($nfe->total) }}</td>
                @if(__countLocalAtivo() > 1)
                <td>{{ $nfe->localizacao->descricao ?? '--' }}</td>
                @endif
            </tr>
            @endforelse
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7"><strong>TOTAL DE COMPRAS</strong></td>
            <td><strong>{{ __moeda($data->sum('total')) }}</strong></td>
        </tr>
    </tfoot>
</table>
