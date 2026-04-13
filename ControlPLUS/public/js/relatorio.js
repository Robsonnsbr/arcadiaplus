$(".inp-vendas").select2({
    minimumInputLength: 2,
    language: "pt-BR",
    placeholder: "",
    multiple:true,
    ajax: {
        cache: true,
        url: path_url + "api/vendas/pesquisa",
        dataType: "json",
        data: function (params) {
            console.clear();
            var query = {
                pesquisa: params.term,
                empresa_id: $("#empresa_id").val(),
            };
            return query;
        },
        processResults: function (response) {
            var results = [];
            console.log(response)
            $.each(response, function (i, v) {
                var o = {};
                o.id = (v.tipo == 'pdv' ? 'pdv_' : 'pedido_' ) + v.id;
                o.text = (v.tipo == 'pdv' ? 'PDV ' : 'Pedido ' ) +  "[" +v.numero_sequencial + "] ";
                if(v.cliente){
                    o.text += " " + v.cliente.info;
                }
                o.value = v.id;
                results.push(o);
            });
            return {
                results: results,
            };
            
        },
    },
});
$(function(){
    setTimeout(() => {
        $(".cliente").each(function (i) {
            $(this).select2({
                minimumInputLength: 2,
                language: "pt-BR",
                placeholder: "Digite para buscar o cliente",

                ajax: {
                    cache: true,
                    url: path_url + "api/clientes/pesquisa",
                    dataType: "json",
                    data: function (params) {
                        console.clear();
                        var query = {
                            pesquisa: params.term,
                            empresa_id: $("#empresa_id").val(),
                        };
                        return query;
                    },
                    processResults: function (response) {
                        var results = [];

                        $.each(response, function (i, v) {
                            var o = {};
                            o.id = v.id;

                            o.text = "["+v.numero_sequencial+"] " + v.razao_social + " - " + v.cpf_cnpj;
                            o.value = v.id;
                            results.push(o);
                        });
                        return {
                            results: results,
                        };
                    },
                },
            });
            return;
        });

        $(".funcionario").each(function (i) {
            $(this).select2({
                minimumInputLength: 2,
                language: "pt-BR",
                placeholder: "Digite para buscar o vendedor",

                ajax: {
                    cache: true,
                    url: path_url + "api/funcionarios/pesquisa",
                    dataType: "json",
                    data: function (params) {
                        console.clear();
                        var query = {
                            pesquisa: params.term,
                            empresa_id: $("#empresa_id").val(),
                        };
                        return query;
                    },
                    processResults: function (response) {
                        var results = [];

                        $.each(response, function (i, v) {
                            var o = {};
                            o.id = v.id;
                            o.text = v.nome;
                            o.value = v.id;
                            results.push(o);
                        });
                        return {
                            results: results,
                        };
                    },
                },
            });
            return;
        });

        // Atalhos de período — Relatório de Compras
        const $periodoCompras = $("#periodo-compras");
        const $comprasStart   = $("#compras-start-date");
        const $comprasEnd     = $("#compras-end-date");

        function aplicarPeriodoCompras(valor) {
            const hoje = new Date();
            const pad  = n => String(n).padStart(2, '0');
            const fmt  = d => d.getFullYear() + '-' + pad(d.getMonth()+1) + '-' + pad(d.getDate());

            if (valor === 'hoje') {
                $comprasStart.val(fmt(hoje));
                $comprasEnd.val(fmt(hoje));
            } else if (valor === 'esta_semana') {
                const dia = hoje.getDay();
                const seg = new Date(hoje); seg.setDate(hoje.getDate() - (dia === 0 ? 6 : dia - 1));
                $comprasStart.val(fmt(seg));
                $comprasEnd.val(fmt(hoje));
            } else if (valor === 'este_mes') {
                const ini = new Date(hoje.getFullYear(), hoje.getMonth(), 1);
                const fim = new Date(hoje.getFullYear(), hoje.getMonth() + 1, 0);
                $comprasStart.val(fmt(ini));
                $comprasEnd.val(fmt(fim));
            } else if (valor === 'mes_passado') {
                const ini = new Date(hoje.getFullYear(), hoje.getMonth() - 1, 1);
                const fim = new Date(hoje.getFullYear(), hoje.getMonth(), 0);
                $comprasStart.val(fmt(ini));
                $comprasEnd.val(fmt(fim));
            } else {
                $comprasStart.val('');
                $comprasEnd.val('');
            }
        }

        if ($periodoCompras.length) {
            $periodoCompras.on('change', function () {
                aplicarPeriodoCompras($(this).val());
            });
        }

        // Ao digitar data manualmente, volta para "Personalizado"
        $comprasStart.add($comprasEnd).on('change', function () {
            if ($periodoCompras.val() !== '') {
                $periodoCompras.val('');
            }
        });

        const $estoqueStartDate = $("#relatorio-estoque-start-date");
        const $estoqueEndDate = $("#relatorio-estoque-end-date");
        const $estoqueCritico = $("#relatorio-estoque-critico");

        if ($estoqueCritico.length) {
            $estoqueCritico.on("change", function () {
                if ($(this).val()) {
                    $estoqueStartDate.val("");
                    $estoqueEndDate.val("");
                }
            });
        }

        if ($estoqueStartDate.length || $estoqueEndDate.length) {
            $estoqueStartDate.add($estoqueEndDate).on("change", function () {
                if ($estoqueStartDate.val() || $estoqueEndDate.val()) {
                    $estoqueCritico.val("");
                }
            });
        }
    }, 100);
});
