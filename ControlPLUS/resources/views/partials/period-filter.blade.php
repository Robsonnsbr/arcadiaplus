{{--
    Partial: partials/period-filter

    Parâmetros (todos opcionais):
        $startName  string  nome do input start_date  (padrão: 'start_date')
        $endName    string  nome do input end_date    (padrão: 'end_date')
        $startId    string  id do input start_date    (padrão: gerado automaticamente)
        $endId      string  id do input end_date      (padrão: gerado automaticamente)

    Uso padrão:
        @include('partials.period-filter')

    Com parâmetros:
        @include('partials.period-filter', [
            'startName' => 'data_inicial',
            'endName'   => 'data_final',
            'startId'   => 'meu-start-date',
            'endId'     => 'meu-end-date',
        ])
--}}
@php
    $pfUid      = 'pf_' . uniqid();
    $pfSelectId = $pfUid . '_sel';
    $pfStartId  = $startId  ?? ($pfUid . '_start');
    $pfEndId    = $endId    ?? ($pfUid . '_end');
    $pfStartName = $startName ?? 'start_date';
    $pfEndName   = $endName   ?? 'end_date';
@endphp

<div class="col-12 rp-period-filter">
    <div class="row g-1">
        <div class="col-12">
            <label class="form-label mb-1 fw-semibold">Período</label>
            <select id="{{ $pfSelectId }}"
                    class="form-select form-select-sm rp-periodo-select"
                    data-start="#{{ $pfStartId }}"
                    data-end="#{{ $pfEndId }}">
                <option value="hoje">Hoje</option>
                <option value="ontem">Ontem</option>
                <option value="ultimos_7">Últimos 7 dias</option>
                <option value="ultimos_30">Últimos 30 dias</option>
                <option value="mes_atual" selected>Mês atual (até hoje)</option>
                <option value="mes_anterior">Mês anterior (fechado)</option>
                <option value="todo_periodo">Todo o período</option>
                <option value="personalizado">Personalizado</option>
            </select>
        </div>
        <div class="col-6">
            <label class="form-label mb-1 small text-muted">Data inicial</label>
            <input type="date"
                   name="{{ $pfStartName }}"
                   id="{{ $pfStartId }}"
                   class="form-control form-control-sm rp-date-input">
        </div>
        <div class="col-6">
            <label class="form-label mb-1 small text-muted">Data final</label>
            <input type="date"
                   name="{{ $pfEndName }}"
                   id="{{ $pfEndId }}"
                   class="form-control form-control-sm rp-date-input">
        </div>
        <div class="col-12 rp-warning-todo d-none">
            <small class="text-warning">
                <i class="ri-alert-line"></i>
                "Todo o período" pode ser lento em bases com muitos dados.
            </small>
        </div>
    </div>
</div>
