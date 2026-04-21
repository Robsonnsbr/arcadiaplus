@extends('relatorios.default')
@section('content')

<style>
    .ci-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
    }

    .ci-table {
        table-layout: fixed;
        width: 100%;
        min-width: 980px;
    }

    .ci-table th,
    .ci-table td {
        word-break: break-word;
        overflow-wrap: break-word;
        white-space: normal;
    }
</style>

<h5>Total de registros: <strong>{{ sizeof($data) }}</strong></h5>
@if($start_date)
<p>Data inicial de filtro: <strong>{{ __data_pt($start_date, 0) }}</strong></p>
@endif
@if($end_date)
<p>Data final de filtro: <strong>{{ __data_pt($end_date, 0) }}</strong></p>
@endif

<div class="ci-wrapper">
<table class="table-sm table-borderless ci-table" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;">
    <colgroup>
        <col style="width:6%">   {{-- Código --}}
        <col style="width:7%">   {{-- Nº Nota --}}
        <col style="width:20%">  {{-- Produto --}}
        <col style="width:15%">  {{-- Fornecedor --}}
        <col style="width:10%">  {{-- Depósito --}}
        <col style="width:9%">   {{-- Data Entrada --}}
        <col style="width:7%">   {{-- Quantidade --}}
        <col style="width:12%">  {{-- Valor Unit. --}}
        <col style="width:14%">  {{-- Valor Total --}}
    </colgroup>
    <thead>
        <tr>
            <th>Código</th>
            <th>Nº Nota</th>
            <th class="text-left">Produto</th>
            <th class="text-left">Fornecedor</th>
            <th class="text-left">Depósito</th>
            <th>Data Entrada</th>
            <th>Quantidade</th>
            <th class="text-right">Valor Unit.</th>
            <th class="text-right">Valor Total</th>
        </tr>
    </thead>
    <tbody>
        @if(sizeof($data) == 0)
        <tr>
            <td colspan="9">Nenhum registro</td>
        </tr>
        @endif

        @foreach($data as $key => $item)
        <tr class="@if($key%2 == 0) pure-table-odd @endif">
            <td>{{ $item['codigo'] }}</td>
            <td>{{ $item['numero_nota'] }}</td>
            <td class="text-left">{{ $item['produto'] }}</td>
            <td class="text-left">{{ $item['fornecedor'] }}</td>
            <td class="text-left">{{ $item['deposito'] }}</td>
            <td>{{ __data_pt($item['data_entrada'], 0) }}</td>
            <td>{{ number_format((float)$item['quantidade'], 2, ',', '.') }}</td>
            <td class="text-right">R$ {{ __moeda($item['valor_unitario']) }}</td>
            <td class="text-right">R$ {{ __moeda($item['valor_total']) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>

@php
    $totalQtd = collect($data)->sum(fn($i) => (float)$i['quantidade']);
    $totalValor = collect($data)->sum(fn($i) => (float)$i['valor_total']);
@endphp

<table style="width:100%; margin-top: 8px; border-top: 2px solid #444;">
    <tr>
        <td style="padding: 6px 4px;"><strong>Total de Itens:</strong> {{ sizeof($data) }}</td>
        <td style="padding: 6px 4px;"><strong>Qtd. Total:</strong> {{ number_format($totalQtd, 2, ',', '.') }}</td>
        <td class="text-right" style="padding: 6px 4px;">
            <strong>Valor Total:</strong> R$ {{ __moeda($totalValor) }}
        </td>
    </tr>
</table>

@endsection
