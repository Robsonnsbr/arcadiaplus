@php
    $isModal = $isModal ?? false;
    $snapshot = $snapshot ?? [];
    $checklistTemplate = $checklistTemplate ?? [];

    $cabecalho = $snapshot['cabecalho'] ?? [];
    $pecas = $snapshot['pecas'] ?? [];
    $declaracoes = $snapshot['declaracoes'] ?? [];
    $observacaoGeral = $snapshot['observacao_geral'] ?? ($tradein->observacao_tecnico ?? '');

    for ($i = count($pecas); $i < 5; $i++) {
        $pecas[] = ['descricao' => '', 'valor' => null, 'produto_id' => null];
    }

    $valorAparelhoBase = $cabecalho['valor_aparelho'] ?? null;
    $valorAparelhoFormatado = $valorAparelhoBase !== null && $valorAparelhoBase !== '' ? __moeda($valorAparelhoBase) : '';
    $valorAvaliadoBase = $tradein->valor_avaliado !== null ? __moeda($tradein->valor_avaliado) : $valorAparelhoFormatado;
    $resultadoOptions = ['SIM' => 'Sim', 'NAO' => 'Não'];
    $hasSnapshot = !empty($tradein->avaliacao_snapshot);
@endphp

@if($isModal)
    <div id="tradein-modal-errors" class="alert alert-danger d-none"></div>
@elseif($errors->any())
    <div class="alert alert-danger">
        <strong>Confira os dados da avaliação:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if($isModal)
<div
    id="tradein-modal-form"
    data-tradein-evaluation-form="1"
    data-evaluation-saved="{{ $hasSnapshot ? 1 : 0 }}"
    data-action="{{ route('tradein.update', $tradein->id) }}"
>
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="_method" value="PUT">
@else
<form
    action="{{ route('tradein.update', $tradein->id) }}"
    method="post"
    data-tradein-evaluation-form="1"
    data-evaluation-saved="{{ $hasSnapshot ? 1 : 0 }}"
>
    @csrf
    @method('PUT')
@endif
    <input type="hidden" name="empresa_id" value="{{ request()->empresa_id ?? $tradein->empresa_id }}">
    <input type="hidden" name="tradein_id" value="{{ $tradein->id }}">

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label class="form-label">Cliente</label>
            <input type="text" class="form-control" readonly value="{{ old('cabecalho.cliente', $cabecalho['cliente'] ?? ($cliente->razao_social ?? '-')) }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Número da venda</label>
            <input type="text" class="form-control" name="cabecalho[numero_venda]" value="{{ old('cabecalho.numero_venda', $cabecalho['numero_venda'] ?? '') }}" placeholder="Ex: 12345">
        </div>
        <div class="col-md-3">
            <label class="form-label">Data</label>
            <input type="date" class="form-control" name="cabecalho[data]" value="{{ old('cabecalho.data', $cabecalho['data'] ?? now()->format('Y-m-d')) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Aparelho de entrada</label>
            <input type="text" class="form-control" readonly value="{{ old('cabecalho.aparelho_entrada', $cabecalho['aparelho_entrada'] ?? $tradein->nome_item) }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">IMEI</label>
            <input type="text" class="form-control" readonly value="{{ old('cabecalho.imei', $cabecalho['imei'] ?? $tradein->serial_number) }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Consultor</label>
            <input type="text" class="form-control" name="cabecalho[consultor]" value="{{ old('cabecalho.consultor', $cabecalho['consultor'] ?? '') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Valor do aparelho</label>
            <input type="text" class="form-control moeda" name="cabecalho[valor_aparelho]" value="{{ old('cabecalho.valor_aparelho', $valorAparelhoFormatado) }}">
        </div>
        <div class="col-md-9">
            <label class="form-label" for="valor_avaliado">Valor avaliado (trade-in)</label>
            <input type="text" name="valor_avaliado" id="valor_avaliado" class="form-control moeda" value="{{ old('valor_avaliado', $valorAvaliadoBase) }}">
        </div>
    </div>

    <h6 class="text-uppercase fs-12 mb-2">Peças - Relatório Interno</h6>
    <div class="table-responsive mb-4">
        <table class="table table-sm table-bordered align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 40%">Peça / Descrição</th>
                    <th style="width: 35%">Produto do catálogo (opcional)</th>
                    <th style="width: 25%">Valor</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 0; $i < 5; $i++)
                    @php
                        $pecaDescricao = old("pecas.$i.descricao", $pecas[$i]['descricao'] ?? '');
                        $pecaProdutoId = old("pecas.$i.produto_id", $pecas[$i]['produto_id'] ?? null);
                        $pecaValorRaw = old("pecas.$i.valor");
                        if ($pecaValorRaw === null) {
                            $valorBase = $pecas[$i]['valor'] ?? null;
                            $pecaValorRaw = $valorBase !== null && $valorBase !== '' ? __moeda($valorBase) : '';
                        }
                        $pecaProdutoNome = '';
                        if ($pecaProdutoId) {
                            $pecaProdutoObj = \App\Models\Produto::find($pecaProdutoId);
                            $pecaProdutoNome = $pecaProdutoObj ? $pecaProdutoObj->nome : '';
                        }
                    @endphp
                    <tr>
                        <td>
                            <input type="text" class="form-control form-control-sm" name="pecas[{{ $i }}][descricao]" value="{{ $pecaDescricao }}" placeholder="Peça {{ $i + 1 }}">
                        </td>
                        <td>
                            <select class="form-select form-select-sm select2-peca-produto"
                                    name="pecas[{{ $i }}][produto_id]"
                                    data-placeholder="Buscar produto..."
                                    data-allow-clear="true"
                                    style="width:100%">
                                @if($pecaProdutoId && $pecaProdutoNome)
                                    <option value="{{ $pecaProdutoId }}" selected>{{ $pecaProdutoNome }}</option>
                                @endif
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm moeda" name="pecas[{{ $i }}][valor]" value="{{ $pecaValorRaw }}" placeholder="0,00">
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

    <h6 class="text-uppercase fs-12 mb-2">Checklist Técnico (Sim/Não + Observações)</h6>
    <div data-scope="checklist-tecnico">
        <div class="mb-2">
            <div class="d-flex flex-wrap gap-3">
                <div class="form-check form-check-inline m-0">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="checklist_tecnico_all_sim_{{ $tradein->id }}_{{ $isModal ? 'modal' : 'page' }}"
                        data-role="checklist-tecnico-all-sim"
                    >
                    <label class="form-check-label" for="checklist_tecnico_all_sim_{{ $tradein->id }}_{{ $isModal ? 'modal' : 'page' }}">
                        Marcar todos como SIM
                    </label>
                </div>
                <div class="form-check form-check-inline m-0">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="checklist_tecnico_all_nao_{{ $tradein->id }}_{{ $isModal ? 'modal' : 'page' }}"
                        data-role="checklist-tecnico-all-nao"
                    >
                    <label class="form-check-label" for="checklist_tecnico_all_nao_{{ $tradein->id }}_{{ $isModal ? 'modal' : 'page' }}">
                        Marcar todos como NÃO
                    </label>
                </div>
            </div>
        </div>
        <div class="table-responsive mb-4">
        <table class="table table-sm table-striped table-bordered align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Item</th>
                    <th style="width: 90px" class="text-center">Sim</th>
                    <th style="width: 90px" class="text-center">Não</th>
                    <th style="width: 35%">Observações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($checklistTemplate as $key => $label)
                    @php
                        $resultadoSelecionado = old("checklist.$key.resultado", $snapshot['checklist'][$key]['resultado'] ?? '');
                        $observacaoItem = old("checklist.$key.observacao", $snapshot['checklist'][$key]['observacao'] ?? '');
                    @endphp
                    <tr>
                        <td>{{ $label }}</td>
                        @foreach($resultadoOptions as $value => $labelResultado)
                            <td class="text-center">
                                <div class="form-check d-inline-block m-0">
                                    <input
                                        class="form-check-input tech-result-option @if($value === 'SIM') tech-sim @else tech-nao @endif"
                                        type="radio"
                                        name="checklist[{{ $key }}][resultado]"
                                        id="chk_{{ $key }}_{{ strtolower($value) }}"
                                        value="{{ $value }}"
                                        @checked($resultadoSelecionado === $value)
                                    >
                                    <label class="form-check-label" for="chk_{{ $key }}_{{ strtolower($value) }}">{{ $labelResultado }}</label>
                                </div>
                            </td>
                        @endforeach
                        <td>
                            <input type="text" class="form-control form-control-sm" name="checklist[{{ $key }}][observacao]" value="{{ $observacaoItem }}" placeholder="Observações">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>

    <h6 class="text-uppercase fs-12 mb-2">Declarações do Cliente</h6>
    <div class="row g-3 mb-3" data-required-checklist="declaracoes">
        <div class="col-md-6">
            <label class="form-label d-block mb-1">Declaro que removi todas as minhas informações pessoais do dispositivo antes da entrega</label>
            @foreach($resultadoOptions as $value => $labelResultado)
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="declaracoes[removeu_dados_pessoais]" id="decl_removeu_{{ strtolower($value) }}" value="{{ $value }}" @checked(old('declaracoes.removeu_dados_pessoais', $declaracoes['removeu_dados_pessoais'] ?? '') === $value)>
                    <label class="form-check-label" for="decl_removeu_{{ strtolower($value) }}">{{ $labelResultado }}</label>
                </div>
            @endforeach
        </div>
        <div class="col-md-6">
            <label class="form-label d-block mb-1">Declaro que estou transferindo a propriedade do meu aparelho</label>
            @foreach($resultadoOptions as $value => $labelResultado)
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="declaracoes[transferencia_propriedade]" id="decl_transf_{{ strtolower($value) }}" value="{{ $value }}" @checked(old('declaracoes.transferencia_propriedade', $declaracoes['transferencia_propriedade'] ?? '') === $value)>
                    <label class="form-check-label" for="decl_transf_{{ strtolower($value) }}">{{ $labelResultado }}</label>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label" for="observacao_tecnico">Observações gerais da avaliação</label>
        <textarea name="observacao_tecnico" id="observacao_tecnico" rows="4" class="form-control">{{ old('observacao_tecnico', $observacaoGeral) }}</textarea>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6 d-flex align-items-end">
            <div class="form-check">
                <input
                    class="form-check-input"
                    type="checkbox"
                    name="concluir_avaliacao"
                    id="concluir_avaliacao"
                    data-role="concluir-avaliacao"
                    value="1"
                    required
                    @checked(old('concluir_avaliacao', $tradein->status === \App\Models\Tradein::STATUS_COMPLETED))
                >
                <label class="form-check-label" for="concluir_avaliacao">Concluir avaliação</label>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
        <button
            type="@if($isModal)button @else submit @endif"
            class="btn btn-primary btn-save-tradein-avaliacao"
            data-role="save-avaliacao"
            @if($isModal) id="btn-save-tradein-avaliacao" @endif
        >
            <i class="ri-check-line"></i> Salvar avaliação
        </button>
        @if($isModal)
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
        @else
            <a href="{{ route('tradein.index') }}" class="btn btn-light">Cancelar</a>
        @endif
    </div>
@if($isModal)
</div>
@else
</form>
@endif

@push('scripts')
<script>
(function() {
    function initPecaProdutoSelect2(el) {
        $(el).select2({
            minimumInputLength: 2,
            language: "pt-BR",
            placeholder: "Buscar produto...",
            allowClear: true,
            width: "100%",
            ajax: {
                cache: false,
                url: path_url + "api/produtos",
                dataType: "json",
                data: function (params) {
                    return {
                        pesquisa: params.term,
                        empresa_id: $("#empresa_id").val(),
                        usuario_id: $("#usuario_id").val(),
                    };
                },
                processResults: function (response) {
                    var results = [];
                    $.each(response, function (i, v) {
                        results.push({ id: v.id, text: v.nome || v.text || String(v.id) });
                    });
                    return { results: results };
                },
            },
        });
    }

    $(function () {
        $(".select2-peca-produto").each(function () {
            initPecaProdutoSelect2(this);
        });
    });
})();
</script>
@endpush
