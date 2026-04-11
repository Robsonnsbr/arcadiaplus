@extends('relatorios.default')
@section('content')

@foreach($data as $nfe)
<table class="table-sm table-borderless" style="border:1px solid #ccc; margin-bottom:16px; width:100%; border-collapse:collapse;">
    <thead>
        <tr style="background:#f0f0f0;">
            <th style="padding:4px 6px; border-bottom:1px solid #ccc;" colspan="4">
                NF-e #{{ $nfe->numero ?? $nfe->id }}
                &nbsp;|&nbsp; <strong>{{ $nfe->fornecedor ? $nfe->fornecedor->razao_social : '--' }}</strong>
                &nbsp;|&nbsp; {{ __data_pt($nfe->created_at) }}
                &nbsp;|&nbsp; Total: <strong>{{ __moeda($nfe->total) }}</strong>
                @if(__countLocalAtivo() > 1 && isset($nfe->localizacao))
                    &nbsp;|&nbsp; Local: {{ $nfe->localizacao->descricao }}
                @endif
            </th>
        </tr>
        <tr style="background:#e8e8e8; font-size:11px;">
            <th style="padding:3px 6px; border-bottom:1px solid #ccc; width:50%;">Produto</th>
            <th style="padding:3px 6px; border-bottom:1px solid #ccc; width:12%; text-align:center;">Qtd.</th>
            <th style="padding:3px 6px; border-bottom:1px solid #ccc; width:19%; text-align:right;">Valor Unit.</th>
            <th style="padding:3px 6px; border-bottom:1px solid #ccc; width:19%; text-align:right;">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @forelse($nfe->itens as $item)
        <tr style="font-size:11px; border-bottom:1px solid #eee;">
            <td style="padding:3px 6px;">
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
            <td style="padding:3px 6px; text-align:center;">{{ number_format((float)$item->quantidade, 2, ',', '.') }}</td>
            <td style="padding:3px 6px; text-align:right;">{{ __moeda($item->valor_unitario) }}</td>
            <td style="padding:3px 6px; text-align:right;">{{ __moeda($item->sub_total) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" style="padding:4px 6px; color:#999; font-size:11px;">Nenhum produto encontrado nesta nota.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endforeach

<h4 style="margin-top:10px;">Total de Compras: R$ {{ __moeda($data->sum('total')) }}</h4>
@endsection
