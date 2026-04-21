@extends('relatorios.default')
@section('content')

<style>
    .cn-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
    }

    .cn-table {
        table-layout: fixed;
        width: 100%;
        /* Alinhado com A3 landscape (~1580px uteis); em tela menor, o wrapper rola */
        min-width: 1400px;
    }

    .cn-table th,
    .cn-table td {
        /* Tipografia compacta para caber 15 colunas sem corte lateral */
        padding: 3px 4px !important;
        font-size: 9.5px;
        word-break: break-word;
        overflow-wrap: break-word;
        white-space: normal;
    }

    /* A chave de acesso tem 44 chars hex sem separador natural; forca quebra */
    .cn-chave {
        font-family: "Courier New", monospace;
        font-size: 8.5px;
        letter-spacing: -0.2px;
        word-break: break-all;
        overflow-wrap: anywhere;
        line-height: 1.2;
    }

    /* Totais de rodape: layout auto para nao truncar em A4 */
    .cn-totais {
        width: 100%;
        margin-top: 8px;
        border-top: 2px solid #444;
        font-size: 10px;
    }

    .cn-totais td {
        padding: 6px 4px;
        border: none !important;
    }
</style>

<h5>Total de notas: <strong>{{ sizeof($data) }}</strong></h5>
@if($start_date)
<p>Data inicial de filtro: <strong>{{ __data_pt($start_date, 0) }}</strong></p>
@endif
@if($end_date)
<p>Data final de filtro: <strong>{{ __data_pt($end_date, 0) }}</strong></p>
@endif

<div class="cn-wrapper">
<table class="table-sm table-borderless cn-table" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;">
    <colgroup>
        <col style="width:4%">   {{-- Código --}}
        <col style="width:5%">   {{-- Nº Nota --}}
        <col style="width:3%">   {{-- Série --}}
        <col style="width:15%">  {{-- Chave --}}
        <col style="width:6%">   {{-- Data --}}
        <col style="width:6%">   {{-- CFOP --}}
        <col style="width:9%">   {{-- Empresa --}}
        <col style="width:11%">  {{-- Fornecedor --}}
        <col style="width:6%">   {{-- Total Itens --}}
        <col style="width:6%">   {{-- Descontos --}}
        <col style="width:6%">   {{-- Outras Despesas --}}
        <col style="width:7%">   {{-- Valor Total --}}
        <col style="width:5%">   {{-- ICMS --}}
        <col style="width:6%">   {{-- ICMS ST --}}
        <col style="width:5%">   {{-- IPI --}}
    </colgroup>
    <thead>
        <tr>
            <th>Código</th>
            <th>Nº Nota</th>
            <th>Série</th>
            <th class="text-left">Chave de Acesso</th>
            <th>Data</th>
            <th class="text-left">CFOP</th>
            <th class="text-left">Empresa</th>
            <th class="text-left">Fornecedor</th>
            <th class="text-right">Total Itens</th>
            <th class="text-right">Descontos</th>
            <th class="text-right">Outras Despesas</th>
            <th class="text-right">Valor Total</th>
            <th class="text-right">ICMS</th>
            <th class="text-right">ICMS ST</th>
            <th class="text-right">IPI</th>
        </tr>
    </thead>
    <tbody>
        @if(sizeof($data) == 0)
        <tr>
            <td colspan="15">Nenhum registro</td>
        </tr>
        @endif

        @foreach($data as $key => $item)
        <tr class="@if($key%2 == 0) pure-table-odd @endif">
            <td>{{ $item['codigo'] }}</td>
            <td>{{ $item['numero'] }}</td>
            <td>{{ $item['serie'] }}</td>
            <td class="text-left cn-chave">{{ $item['chave'] }}</td>
            <td>{{ __data_pt($item['data'], 0) }}</td>
            <td class="text-left">{{ $item['cfop'] }}</td>
            <td class="text-left">{{ $item['empresa'] }}</td>
            <td class="text-left">{{ $item['fornecedor'] }}</td>
            <td class="text-right">{{ __moeda($item['valor_produtos']) }}</td>
            <td class="text-right">{{ __moeda($item['desconto']) }}</td>
            <td class="text-right">{{ __moeda($item['outras_despesas']) }}</td>
            <td class="text-right"><strong>{{ __moeda($item['valor_total']) }}</strong></td>
            <td class="text-right">{{ __moeda($item['icms']) }}</td>
            <td class="text-right">{{ __moeda($item['icms_st']) }}</td>
            <td class="text-right">{{ __moeda($item['ipi']) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>

@php
    $totProd = collect($data)->sum(fn($i) => (float)$i['valor_produtos']);
    $totDesc = collect($data)->sum(fn($i) => (float)$i['desconto']);
    $totOutr = collect($data)->sum(fn($i) => (float)$i['outras_despesas']);
    $totNota = collect($data)->sum(fn($i) => (float)$i['valor_total']);
    $totIcms = collect($data)->sum(fn($i) => (float)$i['icms']);
    $totIcmsSt = collect($data)->sum(fn($i) => (float)$i['icms_st']);
    $totIpi  = collect($data)->sum(fn($i) => (float)$i['ipi']);
@endphp

<table class="cn-totais">
    <tr>
        <td><strong>Total Itens:</strong> R$ {{ __moeda($totProd) }}</td>
        <td><strong>Descontos:</strong> R$ {{ __moeda($totDesc) }}</td>
        <td><strong>Outras Despesas:</strong> R$ {{ __moeda($totOutr) }}</td>
        <td class="text-right"><strong>Total das Notas:</strong> R$ {{ __moeda($totNota) }}</td>
    </tr>
    <tr>
        <td><strong>ICMS:</strong> R$ {{ __moeda($totIcms) }}</td>
        <td><strong>ICMS ST:</strong> R$ {{ __moeda($totIcmsSt) }}</td>
        <td><strong>IPI:</strong> R$ {{ __moeda($totIpi) }}</td>
        <td class="text-right"><strong>Total de Notas:</strong> {{ sizeof($data) }}</td>
    </tr>
</table>

@endsection
