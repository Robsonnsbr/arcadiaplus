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

        // Interação Estoque Crítico ↔ Filtro de Período
        // Os inputs de data agora são gerenciados pelo period-filter.js.
        // Quando o usuário ativa "Estoque Crítico", forçamos o período para
        // "todo_periodo" (datas vazias), pois a lógica de crítico ignora datas.
        // Quando "Estoque Crítico" é removido, restauramos "mes_atual".
        const $estoqueCritico = $("#relatorio-estoque-critico");
        const $estoqueSelectPeriodo = $("#relatorio-estoque-start-date")
            .closest('.rp-period-filter')
            .find('.rp-periodo-select');

        if ($estoqueCritico.length) {
            $estoqueCritico.on("change", function () {
                if ($(this).val()) {
                    // Ativa estoque crítico: limpa datas (todo período)
                    if (window.rpSetPeriodo && $estoqueSelectPeriodo.length) {
                        window.rpSetPeriodo($estoqueSelectPeriodo[0], 'todo_periodo');
                    }
                } else {
                    // Remove estoque crítico: volta ao padrão
                    if (window.rpSetPeriodo && $estoqueSelectPeriodo.length) {
                        window.rpSetPeriodo($estoqueSelectPeriodo[0], 'mes_atual');
                    }
                }
            });
        }
    }, 100);
});
