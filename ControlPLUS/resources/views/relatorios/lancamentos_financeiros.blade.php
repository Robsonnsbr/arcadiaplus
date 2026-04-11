@extends('relatorios.default')
@section('content')

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px; width: 100%;">
    <thead>
        <tr>
            <th>Tipo</th>
            <th class="text-left">Descrição</th>
            <th class="text-left">Cliente / Fornecedor</th>
            <th class="text-left">Categoria</th>
            <th>Vencimento</th>
            <th>Valor</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $item)
        <tr class="@if($key%2 == 0) pure-table-odd @endif">
            <td>
                @if($item['tipo'] === 'receber')
                    <span style="color: green; font-weight: bold;">↑ Receber</span>
                @else
                    <span style="color: red; font-weight: bold;">↓ Pagar</span>
                @endif
            </td>
            <td class="text-left">{{ $item['descricao'] ?: '—' }}</td>
            <td class="text-left">{{ $item['pessoa'] ?: '—' }}</td>
            <td class="text-left">{{ $item['categoria'] ?: '—' }}</td>
            <td>{{ __data_pt($item['data_vencimento'], 0) }}</td>
            <td class="text-right">R$ {{ __moeda($item['valor']) }}</td>
            <td>
                @if($item['status'] == 1)
                    <span style="color: green;">Quitado</span>
                @elseif(strtotime($item['data_vencimento']) < strtotime(date('Y-m-d')))
                    <span style="color: red;">Em atraso</span>
                @else
                    <span style="color: #e67e00;">Pendente</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<table style="width:100%; margin-top: 8px; border-top: 2px solid #444;">
    <tr>
        <td style="padding: 6px 4px;"><strong>Total a Receber:</strong> <span style="color: green;">R$ {{ __moeda($total_receber) }}</span></td>
        <td style="padding: 6px 4px;"><strong>Total a Pagar:</strong> <span style="color: red;">R$ {{ __moeda($total_pagar) }}</span></td>
        <td class="text-right" style="padding: 6px 4px;">
            <strong>Saldo:</strong>
            <span style="color: {{ $saldo >= 0 ? 'green' : 'red' }}; font-weight: bold;">
                R$ {{ __moeda(abs($saldo)) }} {{ $saldo >= 0 ? '(positivo)' : '(negativo)' }}
            </span>
        </td>
    </tr>
</table>

@endsection
