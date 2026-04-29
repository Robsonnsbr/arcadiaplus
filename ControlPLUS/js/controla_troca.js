TOTALOLD = 0
$(function(){
	TOTALOLD = $('#valor_total_old').val()
	setTimeout(() => {
		if (typeof comparaValor === "function") {
			comparaValor()
		}
	}, 700)
})

/**
 * Devolução PDV não exige item novo.
 * 1) window.CP_TROCA_IS_DEVOLUCAO_PDV vem do Blade (confiável).
 * 2) Depois: hidden, data-* na página, query string, input[name=modalidade].
 */
function trocaIsDevolucaoPdv() {
	if (window.CP_TROCA_IS_DEVOLUCAO_PDV === true) {
		return true
	}
	var m = String($("#inp-modalidade").val() || "").trim()
	if (m === "devolucao_pdv") {
		return true
	}
	m = String($("#form-troca [data-troca-modalidade]").attr("data-troca-modalidade") || "").trim()
	if (m === "devolucao_pdv") {
		return true
	}
	m = String($('#form-troca input[name="modalidade"]').val() || "").trim()
	if (m === "devolucao_pdv") {
		return true
	}
	var el = document.getElementById("inp-modalidade")
	if (el && String(el.value || "").trim() === "devolucao_pdv") {
		return true
	}
	try {
		if (new URLSearchParams(window.location.search).get("modalidade") === "devolucao_pdv") {
			return true
		}
	} catch (e) {}
	return false
}

function comparaValor(){
	setTimeout(() => {
		let totalSaida = typeof window.CP_TROCA_TOTAL_SAIDA === "number"
			? window.CP_TROCA_TOTAL_SAIDA
			: convertMoedaToFloat($('#inp-valor_total').val())
		let totalRetorno = typeof window.CP_TROCA_TOTAL_RETORNO === "number"
			? window.CP_TROCA_TOTAL_RETORNO
			: parseFloat(TOTALOLD)
		TOTALOLD = totalRetorno
		let saldo = totalSaida - totalRetorno
		let absSaldo = Math.abs(saldo)
		let isZero = absSaldo < 0.005
		let isPagar = saldo > 0 && !isZero
		let isDevolver = saldo < 0 && !isZero

		$('.total-venda').text('R$ ' + convertFloatToMoeda(totalSaida))
		$('.total-saida').text('R$ ' + convertFloatToMoeda(totalSaida))
		$('.total-retorno').text('R$ ' + convertFloatToMoeda(totalRetorno))
		$('.h-valor_pagar').toggleClass('d-none', !isPagar)
		$('.h-valor_restante').toggleClass('d-none', !isDevolver)
		$('.h-valor_zero').toggleClass('d-none', !isZero)
		$('.bloco-tipo-pagamento').toggleClass('d-none', isZero)

		if(isPagar){
			$('.valor_pagar').text('R$ ' + convertFloatToMoeda(absSaldo))
			$('#inp-valor_pagar').val(absSaldo)
			$('#inp-valor_credito').val('0')
			$('#inp-tipo_pagamento').prop('disabled', false)
		}else if(isDevolver){
			$('.valor_restante').text('R$ ' + convertFloatToMoeda(absSaldo))
			$('#inp-valor_pagar').val('0')
			$('#inp-valor_credito').val(absSaldo)
			$('#inp-tipo_pagamento').prop('disabled', false)
		}else{
			$('#inp-valor_pagar').val('0')
			$('#inp-valor_credito').val('0')
			$('#inp-tipo_pagamento').val('')
			$('#inp-tipo_pagamento').prop('disabled', true)
			$('.valor_pagar').text('R$ ' + convertFloatToMoeda(0))
			$('.valor_restante').text('R$ ' + convertFloatToMoeda(0))
			$('#salvar_venda').removeAttr('disabled')
			$('#editar_venda').removeAttr('disabled')
		}
	}, 500)
}

$("body").on("click", ".cards-categorias, .card-group", function () {
	comparaValor()
})

$("body").on("click", ".btn-add-item", function () {
	comparaValor()
})

$("body").on("click", ".btn-qtd", function () {
	comparaValor()
})

$("body").on('click', '.salvar_troca', function () {
	let cliente = $("#inp-cliente_id").val();
	if(!cliente){
		var isDev = trocaIsDevolucaoPdv()
		swal("Alerta", isDev ? "Informe o cliente para finalizar a devolução!" : "Informe o cliente para finalizar a troca!", "warning")
		.then(() => {
			setTimeout(() => {
				$('#finalizar_troca .btn-close').trigger('click')
			}, 100)
		})
		
	}
})
$(".table-itens").on('click', '.btn-delete-row', function () {
	comparaValor()		
})

$("body").on('change', '#inp-tipo_pagamento', function () {
	let tipo_pagamento = $(this).val()
	let cliente = $("#inp-cliente_id").val();
	$('#salvar_venda').removeAttr("disabled")
	
	if(tipo_pagamento == '00' && !cliente){
		$(this).val('').change()
		swal("Alerta", "Informe o cliente!", "warning")
		$('#cliente').modal('show')
	}
});

$("body").on('click', '#btn-comprovante-troca', function () {
	$("#form-troca").submit()
})

$("#form-troca").on("submit", function (e) {
	e.preventDefault();
	var isDev = trocaIsDevolucaoPdv()
	if (!isDev && typeof validateCodigoUnicoRows === "function" && !validateCodigoUnicoRows()) {
		return;
	}
	if (!isDev) {
		var nSaida = $('.table-itens tbody tr[data-tipo-linha="saida"]').length;
		if (nSaida < 1) {
			swal("Atenção", "Inclua ao menos um produto novo (saída de estoque) na troca antes de finalizar.", "warning");
			return;
		}
	}
	const form = $(e.target);
	var json = $(this).serializeFormJSON();

	json.empresa_id = $('#empresa_id').val()
	json.usuario_id = $('#usuario_id').val()
	json.venda_id = $('#venda_id').val()
	json.tipo = $('#tipo').val()
	json.modalidade = isDev ? "devolucao_pdv" : (String($("#inp-modalidade").val() || "").trim() || "troca")

	// console.log(json)
	// return;

	$.post(path_url + 'api/trocas/store', json)
	.done((success) => {
		// console.log(success)
		
		swal({
			title: "Sucesso",
			text: (json.modalidade === "devolucao_pdv" ? "Devolução registrada" : "Troca finalizada") + " com sucesso, deseja imprimir o comprovante?",
			icon: "success",
			buttons: true,
			buttons: ["Não", "Sim"],
			dangerMode: true,
		}).then((isConfirm) => {
			if (isConfirm) {
				window.open(path_url + 'trocas/imprimir/' + success.id, "_blank")
			} else {
			}

			location.href = '/trocas';
		});

	}).fail((err) => {
		let message = (err.responseJSON && (typeof err.responseJSON === "string" ? err.responseJSON : err.responseJSON.message)) || err.responseText || "Não foi possível concluir a operação."
		if (typeof message !== "string") {
			message = JSON.stringify(message)
		}
		if (typeof swal === "function") {
			swal("Erro", message, "error")
		}
		console.log(err)
	})
})
