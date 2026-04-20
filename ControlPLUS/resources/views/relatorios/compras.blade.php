@extends('relatorios.default')
@section('content')

<h5>Total de registros: <strong>{{ $data->count() }}</strong></h5>

@foreach($data as $nfe)
<table style="margin-bottom: 16px; width: 100%;">
    <thead>
        <tr>
            <th class="text-left" colspan="4">
                NF-e #{{ $nfe->numero ?? $nfe->id }}
                &nbsp;|&nbsp; <strong>{{ $nfe->fornecedor ? $nfe->fornecedor->razao_social : '--' }}</strong>
                &nbsp;|&nbsp; {{ __data_pt($nfe->created_at) }}
                &nbsp;|&nbsp; Total: <strong>{{ __moeda($nfe->total) }}</strong>
                @if(__countLocalAtivo() > 1 && isset($nfe->localizacao))
                    &nbsp;|&nbsp; Local: {{ $nfe->localizacao->descricao }}
                @endif
            </th>
        </tr>
        <tr>
            <th class="text-left" style="width:50%;">Produto</th>
            <th style="width:12%;">Qtd.</th>
            <th class="text-right" style="width:19%;">Valor Unit.</th>
            <th class="text-right" style="width:19%;">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @forelse($nfe->itens as $idx => $item)
        <tr>
            <td class="text-left">
                @if($item->produto)
                    @if($item->variacao_id && $item->produtoVariacao)
                        {{ $item->produto->nome }} – {{ $item->produtoVariacao->descricao }}
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
            <td class="text-right">{{ __moeda($item->valor_unitario) }}</td>
            <td class="text-right">{{ __moeda($item->sub_total) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-left">Nenhum produto encontrado nesta nota.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endforeach

<h4 style="margin-top: 10px;">Total de Compras: R$ {{ __moeda($data->sum('total')) }}</h4>
@endsection
