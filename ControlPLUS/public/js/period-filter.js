/**
 * period-filter.js
 * Seletor de período padronizado para todos os relatórios.
 *
 * Uso: qualquer <select class="rp-periodo-select"> com
 *   data-start="#id-do-input-start"
 *   data-end="#id-do-input-end"
 * será inicializado automaticamente no DOM ready.
 *
 * API pública:
 *   window.rpInitPeriodFilter(selectEl)  → inicializa um select manualmente
 *   window.rpSetPeriodo(selectEl, valor) → força um período via código externo
 */
(function ($) {
    'use strict';

    /* ------------------------------------------------------------------ */
    /* Utilitário de datas                                                  */
    /* ------------------------------------------------------------------ */

    function pad(n) {
        return String(n).padStart(2, '0');
    }

    function fmt(d) {
        return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
    }

    /**
     * Calcula o intervalo de datas para cada opção de período.
     * Retorna { start: 'YYYY-MM-DD', end: 'YYYY-MM-DD' }
     * ou  { start: '', end: '' }   para "todo_periodo"
     * ou  null                     para "personalizado" (não toca nos inputs)
     */
    function calcPeriodo(valor) {
        var hoje = new Date();
        hoje.setHours(0, 0, 0, 0);

        switch (valor) {
            case 'hoje':
                return { start: fmt(hoje), end: fmt(hoje) };

            case 'ontem': {
                var ontem = new Date(hoje);
                ontem.setDate(hoje.getDate() - 1);
                return { start: fmt(ontem), end: fmt(ontem) };
            }

            case 'ultimos_7': {
                var d7 = new Date(hoje);
                d7.setDate(hoje.getDate() - 6);
                return { start: fmt(d7), end: fmt(hoje) };
            }

            case 'ultimos_30': {
                var d30 = new Date(hoje);
                d30.setDate(hoje.getDate() - 29);
                return { start: fmt(d30), end: fmt(hoje) };
            }

            case 'mes_atual': {
                var ini = new Date(hoje.getFullYear(), hoje.getMonth(), 1);
                return { start: fmt(ini), end: fmt(hoje) };
            }

            case 'mes_anterior': {
                var iniA = new Date(hoje.getFullYear(), hoje.getMonth() - 1, 1);
                var fimA = new Date(hoje.getFullYear(), hoje.getMonth(), 0);
                return { start: fmt(iniA), end: fmt(fimA) };
            }

            case 'todo_periodo':
                // Envia strings vazias; o backend ignora o filtro de data quando vazio
                return { start: '', end: '' };

            case 'personalizado':
            default:
                return null; // não toca nos inputs; usuário define livremente
        }
    }

    /* ------------------------------------------------------------------ */
    /* Lógica de inicialização de um filtro                                 */
    /* ------------------------------------------------------------------ */

    function initPeriodFilter($select) {
        if ($select.data('rp-initialized')) return;
        $select.data('rp-initialized', true);

        var $start  = $($select.data('start'));
        var $end    = $($select.data('end'));
        var $filter = $select.closest('.rp-period-filter');
        var $aviso  = $filter.find('.rp-warning-todo');

        function aplicar(valor) {
            var periodo        = calcPeriodo(valor);
            var isPersonalizado = (valor === 'personalizado');
            var isTodoPeriodo  = (valor === 'todo_periodo');

            if (periodo !== null) {
                // Pré-definido: preenche e bloqueia edição (readonly)
                $start.val(periodo.start).prop('readonly', true).removeClass('rp-date-editavel');
                $end.val(periodo.end).prop('readonly', true).removeClass('rp-date-editavel');
            } else {
                // Personalizado: habilita edição
                $start.prop('readonly', false).addClass('rp-date-editavel');
                $end.prop('readonly', false).addClass('rp-date-editavel');
            }

            $aviso.toggleClass('d-none', !isTodoPeriodo);
        }

        // Inicializa com "Mês atual (até hoje)"
        $select.val('mes_atual');
        aplicar('mes_atual');

        $select.on('change.rpPeriodo', function () {
            aplicar($(this).val());
        });

        // Quando o usuário edita a data manualmente (só possível se "personalizado"):
        // garante que o select reflete o estado correto.
        $start.add($end).on('change.rpPeriodo', function () {
            if ($select.val() !== 'personalizado') {
                $select.val('personalizado');
                aplicar('personalizado');
            }
        });
    }

    /* ------------------------------------------------------------------ */
    /* API pública                                                           */
    /* ------------------------------------------------------------------ */

    /**
     * Inicializa manualmente um select.
     * @param {HTMLElement|jQuery} selectEl
     */
    window.rpInitPeriodFilter = function (selectEl) {
        initPeriodFilter($(selectEl));
    };

    /**
     * Força a troca de período programaticamente (e.g., ao mudar "Estoque Crítico").
     * @param {HTMLElement|jQuery} selectEl
     * @param {string} valor  - ex: 'personalizado', 'todo_periodo', 'mes_atual'
     */
    window.rpSetPeriodo = function (selectEl, valor) {
        var $select = $(selectEl);
        $select.val(valor).trigger('change.rpPeriodo');
    };

    /* ------------------------------------------------------------------ */
    /* Auto-inicialização no DOM ready                                       */
    /* ------------------------------------------------------------------ */

    $(function () {
        $('.rp-periodo-select').each(function () {
            initPeriodFilter($(this));
        });
    });

})(jQuery);
