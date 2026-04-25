@section('css')

    {{-- <link rel="stylesheet" type="text/css" href="/css/pdv.css"> --}}

<style>
/* --- PDV compacto (viewport / menos scroll) — apenas UI --- */
:root{
  --pdv-card-h: 104px;
  --pdv-space-1: 6px;
  --pdv-space-2: 10px;
  --pdv-space-3: 16px;
  --pdv-sidebar-width: 348px;
  --pdv-border: 1px solid rgba(0, 0, 0, 0.08);
}

.pdv-layout {
  --bs-gutter-x: var(--pdv-space-2);
  --bs-gutter-y: var(--pdv-space-2);
}

.pdv-compact-card .card-body {
  padding: 0.5rem 0.65rem;
}
.pdv-compact-card .widget-icon-box-avatar {
  width: 2.25rem !important;
  height: 2.25rem !important;
  font-size: 1.15rem !important;
}

/* Cliente / Vendedor: mesma altura visual e rodapé alinhado */
.pdv-cliente-row > [class*="col-"] {
  display: flex;
}
.pdv-meta-card {
  width: 100%;
}
.pdv-meta-card .card-body {
  min-height: 100%;
}
.pdv-meta-card-head {
  flex: 1 1 auto;
  min-height: 0;
}
.pdv-meta-card .cliente_selecionado,
.pdv-meta-card .funcionario_selecionado {
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}
.pdv-meta-card-footer {
  flex-shrink: 0;
}
/* Reserva a mesma faixa do botão TRADE-IN (btn-sm) no card Vendedor */
.pdv-meta-card-footer-spacer {
  min-height: 31px;
}

@media (min-width: 992px) {
  .pdv-summary-column {
    flex: 0 0 var(--pdv-sidebar-width);
    max-width: var(--pdv-sidebar-width);
  }
  .pdv-main-column {
    flex: 1 1 0;
    min-width: 0;
  }
  .pdv-summary-sidebar {
    position: sticky;
    top: var(--pdv-space-1);
    max-height: calc(100vh - 12px);
    display: flex;
    flex-direction: column;
  }
  .pdv-summary-body {
    max-height: calc(100vh - 16px);
    min-height: 0;
    overflow: hidden;
  }
  /* Scroll só na faixa de botões auxiliares — total, pagamento e rodapé fixos na coluna */
  .pdv-payment-stack {
    flex: 1 1 0 !important;
    min-height: 0 !important;
    overflow: hidden;
  }
  .pdv-payment-stack > .pdv-card-tipo-pag {
    flex-shrink: 0;
  }
  .pdv-payment-stack > .div-troco {
    flex-shrink: 0;
  }
  .pdv-payment-stack > .div-btns {
    flex: 1 1 0;
    min-height: 0;
    overflow-y: auto;
    margin-top: var(--pdv-space-1) !important;
    padding-right: 2px;
  }
}

.pdv-main-card,
.pdv-catalog-card {
  border-radius: 10px;
}

.pdv-table-card .table-scroll {
  max-height: min(38vh, 440px);
}

.pdv-catalog-card .lista_produtos {
  max-height: min(26vh, 300px) !important;
}

.pdv-catalog-card .card-body {
  padding-top: 0.5rem;
  padding-bottom: 0.4rem;
}

.pdv-summary-body {
  padding: 0;
}

.pdv-sidebar-section {
  padding-left: var(--pdv-space-2);
  padding-right: var(--pdv-space-2);
}

.pdv-meta-row {
  font-size: 0.8rem;
  line-height: 1.2;
}

.pdv-line-muted .pdv-line-label {
  font-size: 0.65rem;
  letter-spacing: 0.03em;
}

.pdv-line-muted .pdv-line-value {
  font-size: 0.95rem;
  font-weight: 600;
  color: #5c6c7d;
}

.pdv-line-muted {
  padding: 0.35rem 0.45rem !important;
}

.pdv-total-hero {
  border: var(--pdv-border);
  border-radius: 10px;
  background: linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%);
  padding: var(--pdv-space-2) var(--pdv-space-2);
}

.pdv-total-hero .pdv-total-label {
  font-size: 0.68rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #6c757d;
  margin-bottom: 2px;
}

.pdv-total-hero .total-venda {
  display: block;
  font-size: 1.65rem;
  font-weight: 800;
  line-height: 1.05;
  color: #0b5ed7;
  letter-spacing: -0.02em;
}

.pdv-total-hero .pdv-total-ico {
  width: 1.85rem !important;
  height: 1.85rem !important;
  font-size: 1rem !important;
}

.pdv-btns-secondary .btn {
  min-height: 34px;
  padding-top: 0.35rem;
  padding-bottom: 0.35rem;
  font-size: 0.82rem;
  font-weight: 600;
}

.pdv-btns-tertiary .btn {
  min-height: 32px;
  padding-top: 0.3rem;
  padding-bottom: 0.3rem;
  font-size: 0.78rem;
  font-weight: 500;
}

.pdv-btn-finalizar {
  font-size: 1rem;
  font-weight: 700;
  padding-top: 0.55rem !important;
  padding-bottom: 0.55rem !important;
  box-shadow: 0 3px 12px rgba(25, 135, 84, 0.32);
}

.pdv-sangria-grid .avatar-sm {
  margin-left: 0 !important;
}
.pdv-sangria-grid .widget-icon-box-avatar {
  font-size: 1.25rem !important;
}

.pdv-payment-stack > * + * {
  margin-top: var(--pdv-space-1);
}

.pdv-actions-footer {
  margin-top: auto;
  flex-shrink: 0;
  padding-top: var(--pdv-space-2);
  border-top: var(--pdv-border);
}

.pdv-card-tipo-pag .card-body {
  padding: 0.45rem 0.55rem;
}
.pdv-card-tipo-pag .fs-13 {
  font-size: 0.7rem !important;
}
.pdv-card-tipo-pag .widget-icon-box-avatar {
  width: 2rem !important;
  height: 2rem !important;
  font-size: 1rem !important;
}
.pdv-card-tipo-pag .form-select {
  padding-top: 0.25rem;
  padding-bottom: 0.25rem;
  font-size: 0.85rem;
}

/* Altura fixa só onde precisa (tipo pagamento / troco); lista de ações usa altura fluida + scroll */
.pdv-card-h{
  height: var(--pdv-card-h);
}

.pdv-aux-btns-card {
  min-height: 0;
}
.pdv-aux-btns-card .card-body {
  padding: 0.45rem 0.5rem;
}

/* Table container: scroll when needed */
.table-scroll{
  max-height: 38vh;
  overflow-y: auto;
  min-height: 0;
}

.table-itens.table-sm td,
.table-itens.table-sm th {
  padding: 0.35rem 0.45rem;
  font-size: 0.875rem;
}

.table-itens td, .table-itens th{
  vertical-align: middle;
}
@media (max-width: 991.98px){
  :root{ --pdv-card-h: 118px; }
  .table-scroll{ max-height: 36vh; }
  .pdv-table-card .table-scroll { max-height: 36vh; }
}
@media (max-width: 575.98px){
  :root{ --pdv-card-h: 128px; }
  .table-scroll{ max-height: 32vh; }
  .card-body{ padding: .65rem; }
}
</style>

@endsection

<input type="hidden" id="abertura" value="{{ $abertura }}" name="">
<input type="hidden" id="tef_hash" value="" name="tef_hash">
<input type="hidden" id="config_tef" value="{{ isset($configTef) && $configTef != null ? 1 : 0 }}">
<input type="hidden" id="agrupar_itens" value="{{ $config ? $config->agrupar_itens : 0 }}" name="">
<input type="hidden" id="definir_vendedor_pdv" value="{{ $config ? $config->definir_vendedor_pdv : 0 }}"
    name="">
<input type="hidden" id="venda_id" value="{{ isset($item) ? $item->id : '' }}">
<input type="hidden" id="lista_id"
    value="@isset($item){{ $item->lista_id ?? '' }}@else@isset($cliente){{ $cliente->lista_preco->id ?? '' }}@endisset@endisset"
    name="lista_id">
<input type="hidden" id="inp-produto_tipo_unico" value="0">
<input type="hidden" id="alerta_sonoro" value="{{ $config ? $config->alerta_sonoro : 0 }}">
<input type="hidden" id="local_id" value="{{ $caixa->localizacao->id }}">
<input type="hidden" id="impressao_sem_janela_cupom" value="{{ $config ? $config->impressao_sem_janela_cupom : 0 }}">
<input type="hidden" id="documento_pdv" value="{{ $config ? $config->documento_pdv : 'nfce' }}">
<input type="hidden" id="NFECNPJ" value="{{ env('NFECNPJ') }}">

@php
    $tiposPagamentoTradein = $tiposPagamento ?? [];
    $tradeinCode = \App\Models\TradeinCreditMovement::PAYMENT_CODE;
    $tiposPagamentoTradein[$tradeinCode] = 'Crédito Trade-in (98)';
@endphp

@if ($isVendaSuspensa)
    <input type="hidden" value="{{ $item->id }}" name="venda_suspensa_id">
@endif

@if (isset($isOrcamento) && $isOrcamento)
    <input type="hidden" value="{{ $item->id }}" name="orcamento_id">
@endif

@isset($acrescimo)
    <input type="hidden" value="{{ $acrescimo }}" id="acrescimo_pedido">
    @endif

    @isset($pedido)
        @isset($isDelivery)
            <input name="pedido_delivery_id" id="pedido_delivery_id" value="{{ $pedido->id }}" class="d-none">
            <input id="pedido_desconto" value="{{ $pedido->desconto ? $pedido->desconto : 0 }}" class="d-none">
            <input name="valor_entrega" id="pedido_valor_entrega" value="{{ $pedido->valor_entrega }}" class="d-none">
        @else
            <input name="pedido_id" id="pedido_id" value="{{ $pedido->id }}" class="d-none">
            @isset($pushItensPedido)
                <input name="itens_cliente" id="pedido_id" value="{{ json_encode($pushItensPedido) }}" class="d-none">
                @endif
                @endif
                @endif

                @isset($agendamento)
                    <input name="agendamento_id" value="{{ $agendamento->id }}" class="d-none">
                    @endif

                    <input type="hidden" id="inp-finalizacao_pdv" value="{{ __finalizacaoPdv() }}">

                    <input type="hidden" id="estoque_view" value="@can('estoque_view') 1 @else 0 @endif">

<div class="row pdv-layout g-2 align-items-stretch">
    <div class="col-12 col-lg pdv-main-column produtos">
        <div class="row g-2 mb-1 pdv-cliente-row align-items-stretch">
            <div class="col-lg-6 col-md-6">
                <div class="card widget-icon-box pdv-compact-card pdv-meta-card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between pdv-meta-card-head">
                            <div class="flex-grow-1 overflow-hidden pe-2">
                                <h5 class="text-muted text-uppercase fs-13 mt-0">Cliente</h5>
                                @isset($cliente)
                                <h6 class="cliente_selecionado mb-0">{{ $cliente->razao_social }}</h6>
                                @else
                                <h6 class="cliente_selecionado mb-0">--</h6>
                                @endif
                            </div>
                            <div class="avatar-sm flex-shrink-0 align-self-start">
                                <button type="button" class="avatar-title text-bg-success rounded rounded-3 fs-3 widget-icon-box-avatar shadow btn-selecionar_cliente" data-bs-toggle="modal" data-bs-target="#cliente" aria-label="Selecionar cliente">
                                    <i class="ri-group-line"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mt-2 pdv-meta-card-footer">
                            <button type="button" class="btn btn-outline-primary btn-sm w-100" id="btn-open-tradein">
                                TRADE-IN
                            </button>
                            <div class="mt-2 small text-muted d-none" id="tradein_credit_wrap">
                                Saldo Trade-in: <span id="tradein_credit_balance">R$ 0,00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="card widget-icon-box pdv-compact-card pdv-meta-card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between pdv-meta-card-head">
                            <div class="flex-grow-1 overflow-hidden pe-2">
                                <h5 class="text-muted text-uppercase fs-13 mt-0">Consultor</h5>
                                @isset($funcionario)
                                <h6 class="funcionario_selecionado mb-0">{{ $funcionario->nome }}</h6>
                                @else
                                <h6 class="funcionario_selecionado mb-0">--</h6>
                                @endif
                            </div>
                            <div class="avatar-sm flex-shrink-0 align-self-start">
                                <button type="button" class="avatar-title text-bg-warning rounded rounded-3 fs-3 widget-icon-box-avatar" data-bs-toggle="modal" data-bs-target="#funcionario" aria-label="Selecionar vendedor">
                                    <i class="ri-user-2-line"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mt-2 pdv-meta-card-footer">
                            <div class="pdv-meta-card-footer-spacer" aria-hidden="true"></div>
                            {{-- Reserva altura quando o saldo Trade-in aparece no card Cliente (texto invisível) --}}
                            <div class="mt-2 small text-muted d-none invisible user-select-none" id="tradein_credit_wrap_mirror" aria-hidden="true">
                                Saldo Trade-in: <span>R$ 0,00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card pdv-main-card border-0 shadow-sm mb-2">
            <div class="card-body py-2 px-2">
            <div class="row g-2 mx-0 align-items-end">
                <div class="col-md-6">
                    <div class="form-group mb-0">
                        <label for="inp-produto_id" class="">Produto</label>
                        <div class="input-group">
                            <select class="form-control produto_id" name="produto_id" id="inp-produto_id"></select>
                        </div>
                        <input name="variacao_id" id="inp-variacao_id" type="hidden" value="">

                    </div>
                </div>
                <div class="col-md-2">
                    {!! Form::tel('quantidade', 'Quantidade')->attrs(['class' => 'qtd']) !!}
                </div>
                <div class="col-md-2">
                    {!! Form::tel('valor_unitario', 'Valor Unitário')->attrs(['class' => 'moeda value_unit']) !!}
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary btn-sm btn-add-item w-100" type="button" style="margin-left: 0px">Adicionar</button>
                </div>
                <div class="col-md-1">
                    {!! Form::hidden('subtotal', 'SubTotal')->attrs(['class' => 'moeda']) !!}
                    {!! Form::hidden('valor_total', 'valor Total')->attrs(['class' => 'moeda']) !!}
                </div>
            </div>
            <div class="card pdv-table-card m-0 mt-2 border">
                <div data-bs-target="#navbar-example2" class="scrollspy-example table-scroll">
                    <table class="table table-sm table-striped dt-responsive nowrap table-itens">
                        <thead class="table-dark">
                            <tr>
                                <th></th>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Valor</th>
                                <th>Subtotal</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($item))
                            @foreach ($item->itens as $key => $product)
                            @php
                                $isTipoUnico = $product->produto->tipo_unico ?? 0;
                                $codigoUnicoJson = $product->codigo_unico_json ?? '';
                                $codigoUnicoLabels = [];
                                if($codigoUnicoJson){
                                    $decoded = json_decode($codigoUnicoJson, true);
                                    if(is_array($decoded)){
                                        foreach($decoded as $cu){
                                            if(isset($cu['codigo'])){
                                                $codigoUnicoLabels[] = $cu['codigo'];
                                            }
                                        }
                                    }
                                }
                            @endphp
                            <tr class="line-product" data-tipo-unico="{{ $isTipoUnico ? 1 : 0 }}" data-produto="{{ $product->produto->nome }}">
                                <input readonly type="hidden" name="key" class="form-control" value="{{ $product->key }}">
                                <input readonly type="hidden" name="produto_id[]" class="produto_row" value="{{ $product->produto->id }}">
                                <input name="variacao_id[]" type="hidden" value="{{ $product->variacao_id }}">
                                <input type="hidden" class="codigo_unico_ids" name="codigo_unico_ids[]" value="{{ $codigoUnicoJson }}">

                                <td>
                                    <img src="{{ $product->produto->img }}" style="width: 30px; height: 40px; border-radius: 10px;">
                                </td>
                                <td>
                                    <input style="width: 350px" readonly type="text" name="produto_nome[]" class="form-control" value="{{ $product->produto->nome }} @if ($product->produtoVariacao != null) - {{ $product->produtoVariacao->descricao }} @endif">
                                    <div class="codigo-unico-wrapper @if (!$isTipoUnico) d-none @endif mt-2">
                                        @if ($isTipoUnico)
                                        <span class="badge bg-warning text-dark">Código único obrigatório</span>
                                        <div class="codigo-unico-selected small text-primary mt-1">
                                            @if (sizeof($codigoUnicoLabels) > 0)
                                                {{ implode(', ', $codigoUnicoLabels) }}
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm mt-1 btn-open-codigo-unico">Selecionar códigos</button>
                                        @endif
                                    </div>
                                </td>

                                <td class="datatable-cell">
                                    <div class="form-group mb-2" style="width: 200px">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <button id="btn-subtrai" class="btn btn-danger" type="button">-</button>
                                            </div>
                                            <input type="tel" readonly class="form-control qtd qtd_row" name="quantidade[]" value="{{ number_format($product->quantidade, 2, ',', '') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-success" id="btn-incrementa" type="button">+</button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input style="width: 100px" readonly type="tel" name="valor_unitario[]" class="form-control value-unit" value="{{ __moeda($product->valor_unitario) }}">
                                </td>
                                <td>
                                    <input style="width: 100px" readonly type="tel" name="subtotal_item[]" class="form-control subtotal-item" value="{{ __moeda($product->valor_unitario * $product->quantidade) }}">
                                </td>
                                <td>

                                    @php
                                    $adds = '';

                                    if($product && $product->adicionais){
                                        foreach($product->adicionais as $a){
                                            $adds .= "$a->id,";
                                        }
                                    }
                                    @endphp
                                    <div class="inputs-adicional">
                                        @if ($product->adicionais)
                                        @foreach ($product->adicionais as $a)
                                        <input class='add' type='hidden' value='{{ $a->adicional_id }}' />
                                        @endforeach
                                        @endif
                                    </div>
                                    <input type="hidden" value="{{ $adds }}" class="adicionais" name="adicionais[]">
                                    <button type="button" class="btn btn-danger btn-sm btn-delete-row"><i class="ri-delete-bin-line"></i></button>
                                </td>

                            </tr>
                            @endforeach
                            @endif

                            @if (isset($servicos))
                            @foreach ($servicos as $key => $servico)
                            <tr>
                                <input readonly type="hidden" name="servico_id[]" class="form-control" value="{{ $servico->servico->id }}">

                                <td>
                                    <img src="{{ $servico->servico->img }}" style="width: 30px; height: 40px; border-radius: 10px;">
                                </td>
                                <td style="width: 350px">
                                    <input readonly type="text" name="servico_nome[]" class="form-control" value="{{ $servico->servico->nome }} [serviço]" style="color: darkred;">
                                </td>
                                <td>
                                    <div class="input-group" style="width: 200px">
                                        <div class="input-group-prepend">
                                            <button disabled id="btn-subtrai" class="btn btn-danger" type="button">-</button>
                                        </div>
                                        <input readonly type="tel" name="quantidade_servico[]" class="form-control qtd-item" value="{{ number_format($servico->quantidade, 0) }}">
                                        <div class="input-group-append">
                                            <button disabled class="btn btn-success" id="btn-incrementa" type="button">+</button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input readonly type="tel" style="width: 100px" name="valor_unitario_servico[]" class="form-control" value="{{ __moeda($servico->valor) }}">
                                </td>
                                <td>
                                    <input readonly type="tel" style="width: 100px" name="subtotal_servico[]" class="form-control subtotal-item" value="{{ __moeda($servico->valor * $servico->quantidade) }}">
                                </td>
                                <td>
                                    <button disabled type="button" class="btn btn-danger btn-sm btn-delete-row"><i class="ri-delete-bin-line"></i></button>
                                </td>
                            </tr>
                            @endforeach
                            @endif

                            @if (isset($pedido) && isset($itens))
                            @foreach ($itens as $key => $product)
                            @php
                                $isTipoUnico = $product->produto->tipo_unico ?? 0;
                                $codigoUnicoJson = $product->codigo_unico_json ?? '';
                                $codigoUnicoLabels = [];
                                if($codigoUnicoJson){
                                    $decoded = json_decode($codigoUnicoJson, true);
                                    if(is_array($decoded)){
                                        foreach($decoded as $cu){
                                            if(isset($cu['codigo'])){
                                                $codigoUnicoLabels[] = $cu['codigo'];
                                            }
                                        }
                                    }
                                }
                            @endphp
                            <tr class="line-product" data-tipo-unico="{{ $isTipoUnico ? 1 : 0 }}" data-produto="{{ $product->produto->nome }}">
                                <input readonly type="hidden" name="key" class="form-control" value="{{ $product->key }}">
                                <input readonly type="hidden" name="produto_id[]" class="produto_row" value="{{ $product->produto->id }}">
                                <input name="variacao_id[]" type="hidden" value="{{ $product->variacao_id }}">
                                <input type="hidden" class="codigo_unico_ids" name="codigo_unico_ids[]" value="{{ $codigoUnicoJson }}">

                                <td>
                                    <img src="{{ $product->produto->img }}" style="width: 30px; height: 40px; border-radius: 10px;">
                                </td>
                                <td>
                                    <input style="width: 350px" readonly type="text" name="produto_nome[]" class="form-control" value="{{ $product->produto->nome }} @if ($product->produtoVariacao != null) - {{ $product->produtoVariacao->descricao }} @endif">
                                    <div class="codigo-unico-wrapper @if (!$isTipoUnico) d-none @endif mt-2">
                                        @if ($isTipoUnico)
                                        <span class="badge bg-warning text-dark">Código único obrigatório</span>
                                        <div class="codigo-unico-selected small text-primary mt-1">
                                            @if (sizeof($codigoUnicoLabels) > 0)
                                                {{ implode(', ', $codigoUnicoLabels) }}
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm mt-1 btn-open-codigo-unico">Selecionar códigos</button>
                                        @endif
                                    </div>
                                </td>

                                <td class="datatable-cell">
                                    <div class="form-group mb-2" style="width: 200px">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <button id="btn-subtrai" class="btn btn-danger" type="button">-</button>
                                            </div>
                                            <input type="tel" readonly class="form-control qtd qtd_row" name="quantidade[]" value="{{ number_format($product->quantidade, 2, ',', '') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-success" id="btn-incrementa" type="button">+</button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input style="width: 100px" readonly type="tel" name="valor_unitario[]" class="form-control value-unit" value="{{ __moeda($product->valor_unitario) }}">
                                </td>
                                <td>
                                    <input style="width: 100px" readonly type="tel" name="subtotal_item[]" class="form-control subtotal-item" value="{{ __moeda($product->valor_unitario * $product->quantidade) }}">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm btn-delete-row"><i class="ri-delete-bin-line"></i></button>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                    </tbody>
                </table>
            </div>
        </div>
            </div>
        </div>

        <div class="card pdv-catalog-card border-0 shadow-sm">
            <div class="card-body pt-2 pb-2">
            <h6 class="text-center text-muted text-uppercase mb-1" style="font-size:0.72rem;">Categorias</h6>
            <div class="card categorias m-0" data-simplebar data-simplebar-lg style="height: 52px;">
                <div class="d-flex g m-1">

                    <button type="button" id="cat_todos" onclick="todos()" class="btn btn-cat">Todos</button>
                    @foreach ($categorias as $cat)
                    <button type="button" class="btn btn_cat_{{ $cat->id }} btn-cat" onclick="selectCat('{{ $cat->id }}')">{{ $cat->nome }}</button>
                    @endforeach
                </div>
            </div>
            <h6 class="text-center mt-2 mb-1 fw-semibold">Produtos</h6>
            <div class="card-body lista_produtos m-0" data-simplebar data-simplebar-lg>
                <div class="row cards-categorias">
                </div>
            </div>
            <div class="row mt-2 g-2 align-items-center">
                <div class="col-auto text-center">
                    <input class="mousetrap" type="" autofocus style="border: none; width: 10px; height: 10px; background-color:black" id="codBarras">
                </div>
                <div class="col leitor_ativado text-info">
                    Leitor Ativado
                </div>
                <div class="col leitor_desativado d-none">
                    Leitor Desativado
                </div>
                @if (__countLocalAtivo() > 1 && $caixa->localizacao)
                <div class="col-auto text-end ms-auto">
                    <strong class="text-danger">{{ $caixa->localizacao->descricao }}</strong>
                </div>
                @endif

            </div>
            </div>
        </div>

    </div>

    <div class="col-12 col-lg-auto pdv-summary-column">
        <div class="card pdv-summary-sidebar border-0 shadow-sm h-100 mb-3 mb-lg-0">
            <div class="card-body pdv-summary-body d-flex flex-column p-0">

                <div class="pdv-sidebar-section flex-shrink-0 pt-2 pb-1">
                    <div class="row g-1 pdv-meta-row">
                        <div class="col-6">
                            <span class="text-muted">Itens:</span> <strong class="total-itens text-danger">0</strong>
                        </div>
                        <div class="col-6 text-end">
                            <span class="text-muted">Linhas:</span> <strong class="total-linhas text-danger">0</strong>
                        </div>
                    </div>
                </div>

                <div class="pdv-sidebar-section flex-shrink-0 pb-1">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="card pdv-line-muted border py-2 px-2 h-100">
                                <div class="d-flex justify-content-between align-items-start gap-1">
                                    <span class="pdv-line-label text-muted text-uppercase">Desconto</span>
                                    <button type="button" onclick="setaDesconto()" class="avatar-title text-bg-primary rounded rounded-3 fs-5 widget-icon-box-avatar shadow btn p-0 d-flex align-items-center justify-content-center" style="width:2rem;height:2rem;">
                                        <i class="ri-checkbox-indeterminate-line"></i>
                                    </button>
                                </div>
                                <div class="pdv-line-value mt-1" id="valor_desconto">R$ {{ isset($item) ? __moeda($item->desconto) : '0,00' }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card pdv-line-muted border py-2 px-2 h-100">
                                <div class="d-flex justify-content-between align-items-start gap-1">
                                    <span class="pdv-line-label text-muted text-uppercase">Acréscimo</span>
                                    <button type="button" onclick="setaAcrescimo()" class="avatar-title text-bg-warning rounded rounded-3 fs-5 widget-icon-box-avatar shadow btn p-0 d-flex align-items-center justify-content-center" style="width:2rem;height:2rem;">
                                        <i class="ri-add-box-line"></i>
                                    </button>
                                </div>
                                <div class="pdv-line-value mt-1" id="valor_acrescimo">R$ {{ isset($item) ? __moeda($item->acrescimo) : '0,00' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $pdvSangriaHabilitada = !isset($config) || $config == null || $config->pdvSangriaHabilitada();
                    $pdvSuprimentoHabilitado = !isset($config) || $config == null || $config->pdvSuprimentoHabilitado();
                @endphp

                @if($pdvSangriaHabilitada || $pdvSuprimentoHabilitado)
                <div class="pdv-sidebar-section flex-shrink-0 pb-1">
                    <div class="card border py-1 px-2 pdv-sangria-grid">
                        <div class="row g-2 justify-content-center text-center">
                                @if($pdvSuprimentoHabilitado)
                                <div class="col-6">
                                        <div class="small text-uppercase text-muted mb-0" style="font-size:0.65rem;">Suprimento</div>
                                    <div class="d-flex justify-content-center">
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#suprimento_caixa" class="avatar-title text-bg-info rounded rounded-3 fs-5 widget-icon-box-avatar">
                                            <i class="ri-add-box-line"></i>
                                        </button>
                                    </div>
                                </div>
                                @endif
                                @if($pdvSangriaHabilitada)
                                <div class="col-6">
                                        <div class="small text-uppercase text-muted mb-0" style="font-size:0.65rem;">Sangria</div>
                                    <div class="d-flex justify-content-center">
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#sangria_caixa" class="avatar-title text-bg-danger rounded rounded-3 fs-5 widget-icon-box-avatar">
                                            <i class="ri-checkbox-indeterminate-line"></i>
                                        </button>
                                    </div>
                                </div>
                                @endif
                        </div>
                    </div>
                </div>
                @endif

                <div class="pdv-sidebar-section flex-shrink-0 pb-2">
                    <div class="pdv-total-hero">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="pdv-total-label">Total da venda</div>
                            <span class="avatar-title text-bg-dark rounded rounded-3 widget-icon-box-avatar shadow d-inline-flex align-items-center justify-content-center pdv-total-ico">
                                <i class="ri-shopping-cart-fill"></i>
                            </span>
                        </div>
                        @isset($item)
                        <strong class="total-venda">{{ __moeda($item->valor_total) }}</strong>
                        @else
                        <strong class="total-venda">0,00</strong>
                        @endisset
                    </div>
                </div>

                <div class="pdv-sidebar-section px-2 pb-1 pdv-payment-stack flex-grow-1 d-flex flex-column min-vh-0">

                    <div class="card widget-icon-box pdv-card-tipo-pag pdv-card-h mb-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <h5 class="text-muted text-uppercase fs-13 mt-0 mb-1" title="Number of Orders">Tipo de Pagamento</h5>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title text-bg-success rounded rounded-3 widget-icon-box-avatar shadow">
                                        <i class="ri-money-dollar-circle-line"></i>
                                    </span>
                                </div>
                            </div>

                            {!! Form::select('tipo_pagamento', '', ['' => 'Selecione'] + $tiposPagamentoTradein)->attrs(['class' => 'form-select tp-pag'])->value(isset($item) ? $item->tipo_pagamento : '') !!}
                        </div>
                    </div>

                    <div class="div-troco d-none">
                        <div class="card h-100 pdv-card-h">
                            <div class="row mx-1 my-1 align-items-center">
                                <div class="col-5">
                                    <span class="small fw-semibold">Valor Recebido</span>
                                </div>
                                <div class="col-7">
                                    {!! Form::tel('valor_recebido', '')->attrs(['class' => 'moeda']) !!}
                                </div>
                            </div>
                            <div class="row mx-1 mb-1">
                                <div class="card text-bg-danger py-1 px-2">
                                    <div class="small mb-0 fw-semibold">TROCO = <strong id="valor-troco"></strong></div>
                                    <input type="hidden" name="troco" id="inp-troco">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="div-btns">
                        <div class="card widget-icon-box h-100 pdv-aux-btns-card">
                            <div class="card-body">
                                <div class="row g-1 pdv-btns-secondary">
                                    <div class="col-6">
                                        <button type="button" class="btn btn-warning w-100 btn-pagamento-multi" data-bs-toggle="modal" data-bs-target="#pagamento_multiplo"><i class="ri-list-check-3"></i> Pagamento múltiplo</button>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" class="btn btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#lista_precos"><i class="ri-cash-line"></i> Lista de preços</button>
                                    </div>
                                </div>
                                <div class="row g-1 mt-1 pdv-btns-tertiary">
                                    <div class="col-6">
                                        <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#observacao_pdv"><i class="ri-file-edit-fill"></i> Observação</button>
                                    </div>

                                    @if (!isset($item))
                                    <div class="col-6">
                                        <button type="button" class="btn btn-light border w-100 btn-vendas-suspensas" data-bs-toggle="modal" data-bs-target="#vendas_suspensas"><i class="ri-time-fill"></i> Vendas suspensas</button>
                                    </div>
                                    @endif

                                    <div class="col-6">
                                        <button type="button" class="btn btn-light border w-100" onclick="modalFrete()"><i class="ri-truck-line"></i> Frete <strong class="valor-frete">R$ {{ isset($item) ? __moeda($item->valor_frete) : '0,00' }}</strong></button>
                                    </div>

                                    @if (!isset($item))
                                    <div class="col-6">
                                        <button type="button" class="btn btn-light border w-100 btn-orcamentos" data-bs-toggle="modal" data-bs-target="#orcamentos"><i class="ri-list-settings-fill"></i> Orçamentos</button>
                                    </div>
                                    @endif

                                    <div class="col-12">
                                        <button type="button" class="btn btn-outline-dark w-100 btn-fatura-padrao d-none">
                                            <i class="ri-booklet-line"></i>
                                            Fatura Padrão do Cliente
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="pdv-actions-footer pdv-sidebar-section pb-2 mt-auto d-flex flex-column gap-1">
                    <div class="row g-1">
                        <div class="col-6">
                            <a class="btn btn-outline-danger btn-sm w-100" href="{{ route('frontbox.index') }}">
                                <i class="ri-arrow-left-s-line"></i>
                                Sair do PDV
                            </a>
                        </div>
                        <div class="col-6">
                            @if ($isVendaSuspensa == 0 && $isOrcamento == 0)
                            <button type="button" id="btn-suspender" class="btn btn-light border btn-sm w-100">
                                <i class="ri-timer-line"></i>
                                Suspender Venda
                            </button>
                            @else
                            <a href="{{ route('frontbox.create') }}" class="btn btn-light border btn-sm w-100">
                                <i class="ri-refresh-line"></i>
                                Nova Venda
                            </a>
                            @endif
                        </div>
                    </div>

                    @if (isset($item) && $isVendaSuspensa == 0 && $isOrcamento == 0)
                    <button type="button" class="btn btn-success w-100 pdv-btn-finalizar" disabled id="editar_venda">
                        <i class="ri-checkbox-circle-line"></i>
                        Editar venda
                    </button>
                    @else
                    <button type="button" class="btn btn-success w-100 pdv-btn-finalizar" disabled id="salvar_venda">
                        <i class="ri-checkbox-circle-line"></i>
                        Finalizar venda
                    </button>
                    @endif
                </div>

            </div>
        </div>
    </div>

</div>

@include('modals._pagamento_multiplo', ['not_submit' => true, 'tiposPagamento' => $tiposPagamentoTradein])
@include('modals._finalizar_venda', ['not_submit' => true])
@include('modals._funcionario', ['not_submit' => true])
@include('modals._cartao_credito', ['not_submit' => true])
@include('modals._variacao', ['not_submit' => true])
@include('modals._lista_precos')
@include('modals._vendas_suspensas')
@include('front_box.partials._modal_orcamentos')
@include('modals._tef_consulta')
@include('modals._valor_credito')
@include('modals._modal_pix')
@include('modals._fatura_venda')
@include('modals._frete')
@include('modals._tradein_status')
@include('modals._tradein_create')
@include('modals._tradein_form')

@include('modals._observacao_pdv')
@include('modals._adicionais_pdv')
@include('modals._cliente', ['cashback' => 1])
@include('front_box.partials.modal_codigo_unico')

@section('js')
<script>
    var senhaAcao = "";

    @if (isset($config) && strlen(trim($config->senha_manipula_valor)) > 1)
    senhaAcao = "{{ $config->senha_manipula_valor }}";
    @endif
</script>
<script src="/js/frente_caixa.js" type=""></script>
<script src="/js/tradein_checklist_tecnico.js" type=""></script>
<script src="/js/comanda_pdv.js"></script>

<script type="text/javascript" src="/js/mousetrap.js"></script>
<script type="text/javascript" src="/js/controla_conta_empresa.js"></script>
<script src="/js/novo_cliente.js"></script>

<script type="text/javascript">

    @if (Session::has('sangria_id'))
    window.open(path_url + 'sangria-print/' + {{ Session::get('sangria_id') }}, "_blank")
    @endif
    @if (Session::has('suprimento_id'))
    window.open(path_url + 'suprimento-print/' + {{ Session::get('suprimento_id') }}, "_blank")
    @endif

                                            $('.btn-novo-cliente').click(() => {
                                                $('.modal-select-cliente .btn-close').trigger('click')
                                                $('#modal_novo_cliente').modal('show')

                                            })
                                        </script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Impede o browser de restaurar scroll ao dar F5/back
  if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
  }

  // Força topo da página
  window.scrollTo(0, 0);

  // Zera scroll de containers internos (tabela/listas)
  const containers = [
    document.querySelector('.scrollspy-example'),
    document.querySelector('.table-scroll'),
    document.querySelector('.lista_produtos'),
  ].filter(Boolean);

  containers.forEach(el => (el.scrollTop = 0));

  // Garante foco no leitor/código de barras, sem pular a tela
  const cod = document.getElementById('codBarras');
  if (cod) cod.focus({ preventScroll: true });
});
</script>

@endsection
