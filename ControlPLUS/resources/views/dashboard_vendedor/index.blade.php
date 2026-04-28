@extends('layouts.app', ['title' => 'Dashboard Comercial'])

@section('css')
<style>
    .metric-card {
        border: 0;
        border-radius: 18px;
        overflow: hidden;
        color: #fff;
        min-height: 128px;
    }

    .metric-card .card-body {
        position: relative;
        z-index: 1;
    }

    .metric-card::before {
        content: '';
        position: absolute;
        inset: 0;
        opacity: .18;
        background: radial-gradient(circle at top right, rgba(255,255,255,.8), transparent 55%);
        pointer-events: none;
    }

    .metric-card h2,
    .metric-card h4,
    .metric-card p {
        color: #fff;
        margin: 0;
    }

    .metric-blue { background: linear-gradient(135deg, #0f4c81, #1a87d6); }
    .metric-green { background: linear-gradient(135deg, #13795b, #2ca36b); }
    .metric-orange { background: linear-gradient(135deg, #9b4d0f, #f28b30); }
    .metric-dark { background: linear-gradient(135deg, #1f2937, #4b5563); }

    .soft-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 8px 30px rgba(15, 23, 42, .06);
    }

    .soft-card .card-header {
        background: #fff;
        border-bottom: 1px solid rgba(15, 23, 42, .06);
        border-radius: 18px 18px 0 0 !important;
    }

    .section-title {
        font-weight: 700;
        letter-spacing: -.02em;
    }

    .mini-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .35rem .7rem;
        border-radius: 999px;
        font-size: .78rem;
        background: #eef6ff;
        color: #155e9a;
    }

    .table thead th {
        white-space: nowrap;
    }
</style>
@endsection

@section('content')
@php
    $metaPercent = min(100, (float) $metaAtingida);
    $taxaPreVenda = min(100, (float) $funil['taxa_pre_venda']);
    $taxaVendaCrm = min(100, (float) $funil['taxa_venda_crm']);
@endphp

<div class="mt-1">
    <div class="card soft-card mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h4 class="section-title mb-1">Dashboard Comercial</h4>
                    <div class="text-muted">Visão consolidada por vendedor, consultor e operação comercial.</div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="mini-badge">Período: {{ __data_pt($startDate, 0) }} - {{ __data_pt($endDate, 0) }}</span>
                    <span class="mini-badge">Vendas: {{ $qtdVendas }}</span>
                    <span class="mini-badge">Pré-vendas: {{ $preVendasCriadas }}</span>
                </div>
            </div>

            <form method="get" action="{{ route('dashboard.vendedor') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Período inicial</label>
                        <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Período final</label>
                        <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Funcionário</label>
                        <select name="funcionario_id" class="form-select select2">
                            <option value="">Todos os comerciais</option>
                            @foreach($funcionariosComerciais as $funcionario)
                                <option value="{{ $funcionario->id }}" @selected((string) $funcionarioId === (string) $funcionario->id)>{{ $funcionario->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Local</label>
                        <select name="local_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach(__getLocaisAtivoUsuarioParaSelect() as $id => $descricao)
                                <option value="{{ $id }}" @selected((string) $localId === (string) $id)>{{ $descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1 d-grid">
                        <button class="btn btn-primary" type="submit">Filtrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card metric-card metric-blue shadow-sm position-relative">
                <div class="card-body">
                    <p>Vendas</p>
                    <h2 class="mt-2">R$ {{ __moeda($totalVendas) }}</h2>
                    <div class="mt-2">{{ $qtdVendas }} venda(s) no período</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card metric-card metric-green shadow-sm position-relative">
                <div class="card-body">
                    <p>Ticket médio</p>
                    <h2 class="mt-2">R$ {{ __moeda($ticketMedio) }}</h2>
                    <div class="mt-2">Média por venda faturada</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card metric-card metric-orange shadow-sm position-relative">
                <div class="card-body">
                    <p>Meta comercial</p>
                    <h2 class="mt-2">R$ {{ __moeda($totalMeta) }}</h2>
                    <div class="mt-2">{{ number_format($metaPercent, 1, ',', '.') }}% de atingimento</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card metric-card metric-dark shadow-sm position-relative">
                <div class="card-body">
                    <p>CRM concluído</p>
                    <h2 class="mt-2">{{ $crmConcluido }}</h2>
                    <div class="mt-2">{{ $crmTotal }} registro(s) no período</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card soft-card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Funil comercial</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Pré-vendas</span>
                            <strong>{{ $funil['pre_vendas']['valor'] }}</strong>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-info" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Conversão em venda</span>
                            <strong>{{ $funil['pre_vendas_convertidas'] }}</strong>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-success" style="width: {{ $taxaPreVenda }}%"></div>
                        </div>
                        <small class="text-muted">{{ number_format($funil['taxa_pre_venda'], 1, ',', '.') }}% de conversão</small>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Vendas faturadas</span>
                            <strong>{{ $funil['vendas']['valor'] }}</strong>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-warning" style="width: 100%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between">
                            <span>CRM com venda concluida</span>
                            <strong>{{ $funil['crm']['valor'] }}</strong>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-primary" style="width: {{ $taxaVendaCrm }}%"></div>
                        </div>
                        <small class="text-muted">{{ number_format($funil['taxa_venda_crm'], 1, ',', '.') }}% sobre vendas</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-3">
            <div class="card soft-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gráfico diário</h5>
                    <span class="text-muted small">Vendas totais por dia</span>
                </div>
                <div class="card-body">
                    <div id="grafico-diario" data-labels='@json($graficoDiario["labels"])' data-values='@json($graficoDiario["values"])'></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card soft-card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Ranking de vendedores</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Vendedor</th>
                                    <th class="text-center">Qtd.</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ranking as $item)
                                    <tr>
                                        <td>{{ optional($item['funcionario'])->nome ?? '--' }}</td>
                                        <td class="text-center">{{ $item['qtd_vendas'] }}</td>
                                        <td class="text-end">R$ {{ __moeda($item['total_vendas']) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4">Nenhuma venda encontrada</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-3">
            <div class="card soft-card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Últimas vendas</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Tipo</th>
                                    <th>Número</th>
                                    <th>Cliente</th>
                                    <th>Vendedor</th>
                                    <th>Local</th>
                                    <th>Data</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ultimasVendas as $item)
                                    <tr>
                                        <td>{{ $item['tipo'] }}</td>
                                        <td>{{ $item['numero'] ?? '--' }}</td>
                                        <td>{{ $item['cliente'] }}</td>
                                        <td>{{ $item['funcionario'] }}</td>
                                        <td>{{ $item['localizacao'] }}</td>
                                        <td>{{ __data_pt($item['data']) }}</td>
                                        <td class="text-end">R$ {{ __moeda($item['total']) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">Nenhuma venda encontrada</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card soft-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Próximos retornos</h5>
                    <span class="text-muted small">CRM com retorno previsto para os próximos 7 dias</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Consultor</th>
                                    <th>Assunto</th>
                                    <th>Status</th>
                                    <th>Data retorno</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($proximosRetornos as $item)
                                    <tr>
                                        <td>{{ $item->cliente ? $item->cliente->info : '--' }}</td>
                                        <td>{{ $item->funcionario ? $item->funcionario->nome : '--' }}</td>
                                        <td>{{ $item->assunto }}</td>
                                        <td>
                                            @if($item->status == 'positivo')
                                                <span class="badge bg-success">Positivo</span>
                                            @elseif($item->status == 'bom')
                                                <span class="badge bg-warning text-dark">Bom</span>
                                            @elseif($item->status == 'negativo')
                                                <span class="badge bg-danger">Negativo</span>
                                            @else
                                                <span class="badge bg-secondary">--</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->data_retorno ? __data_pt($item->data_retorno, 0) : '--' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">Nenhum retorno programado</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    (function () {
        const el = document.querySelector('#grafico-diario');
        if (!el) {
            return;
        }

        const labels = JSON.parse(el.dataset.labels || '[]');
        const values = JSON.parse(el.dataset.values || '[]');

        const chart = new ApexCharts(el, {
            chart: {
                type: 'area',
                height: 360,
                toolbar: { show: false }
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            dataLabels: {
                enabled: false
            },
            series: [{
                name: 'Vendas',
                data: values
            }],
            xaxis: {
                categories: labels
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    }
                }
            },
            colors: ['#1a87d6'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.35,
                    opacityTo: 0.05
                }
            }
        });

        chart.render();
    })();
</script>
@endsection
