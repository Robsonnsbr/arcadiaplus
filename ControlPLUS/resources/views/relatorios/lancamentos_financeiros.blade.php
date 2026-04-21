@extends('relatorios.default')
@section('content')

<style>
    .lf-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
    }

    .lf-table {
        table-layout: fixed;
        width: 100%;
        min-width: 1100px;
    }

    .lf-table th,
    .lf-table td {
        word-break: break-word;
        overflow-wrap: break-word;
        white-space: normal;
    }
</style>

<div class="lf-wrapper">
<table class="table-sm table-borderless lf-table" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;">
    <colgroup>
        <col style="width:5%">   {{-- Código --}}
        <col style="width:6%">   {{-- Tipo --}}
        <col style="width:12%">  {{-- Descrição --}}
        <col style="width:12%">  {{-- Cliente/Fornecedor --}}
        <col style="width:8%">   {{-- Nº Documento --}}
        <col style="width:9%">   {{-- Categoria --}}
        <col style="width:10%">  {{-- Plano de Contas --}}
        <col style="width:7%">   {{-- Vencimento --}}
        <col style="width:7%">   {{-- Pagamento --}}
        <col style="width:9%">   {{-- Forma de Pagamento --}}
        <col style="width:8%">   {{-- Valor --}}
        <col style="width:7%">   {{-- Situação --}}
    </colgroup>
    <thead>
        <tr>
            <th>Código</th>
            <th>Tipo</th>
            <th class="text-left">Descrição</th>
            <th class="text-left">Cliente/Fornecedor</th>
            <th>Nº Documento</th>
            <th class="text-left">Categoria</th>
            <th class="text-left">Plano de Contas</th>
            <th>Vencimento</th>
            <th>Pagamento</th>
            <th class="text-left">Forma de Pagamento</th>
            <th class="text-right">Valor</th>
            <th>Situação</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $item)
        <tr class="@if($key%2 == 0) pure-table-odd @endif">
            <td>{{ $item['codigo'] }}</td>
            <td>
                @if($item['tipo'] === 'receber')
                    <span style="color: green; font-weight: bold;">↑ Receber</span>
                @else
                    <span style="color: red; font-weight: bold;">↓ Pagar</span>
                @endif
            </td>
            <td class="text-left">{{ $item['descricao'] ?: '—' }}</td>
            <td class="text-left">{{ $item['pessoa'] ?: '—' }}</td>
            <td>{{ $item['numero_documento'] }}</td>
            <td class="text-left">{{ $item['categoria'] ?: '—' }}</td>
            <td class="text-left">{{ $item['plano_contas'] }}</td>
            <td>{{ __data_pt($item['data_vencimento'], 0) }}</td>
            <td>{{ $item['data_pagamento'] ? __data_pt($item['data_pagamento'], 0) : '--' }}</td>
            <td class="text-left">{{ $item['forma_pagamento'] }}</td>
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
</div>

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
