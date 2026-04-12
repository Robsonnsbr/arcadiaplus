<html>

<head>

    <style type="text/css">
        @page {
            margin: 0cm 0cm;
        }

        body {
            margin-top: 2cm;
            margin-left: 1cm;
            margin-right: 1cm;
            margin-bottom: 2cm;
        }

        header {
            position: relative;
            margin-top: 0px;
            margin-left: 40px;
            margin-right: 40px;
            margin-bottom: 25px;
            height: 20px;
        }

        .banner {
            text-align: center;
            display: flex;
            align-items: flex-start;
        }

        td {
            text-align: center;
        }

        p {
            font-size: 12px;
            margin-top: 0px;
            margin-bottom: 2px;
        }

        .pure-table-odd {
            background: #EBEBEB;
        }

        .logoBanner img {
            float: left;
            max-width: 70px;
        }

        .banner h1 {
            position: absolute;
            margin-top: 0;
        }

        .banner hr {
            margin-top: 29px;
            margin-left: 120px;
        }

        .date {
            float: right;
        }

        .provider {
            text-align: left;
            margin-top: 5px;
            margin-bottom: 10px;
        }

        .client {
            margin-bottom: 0.6rem;
        }

        .status-box {
            border: 1px solid #999;
            padding: 12px;
            margin-bottom: 18px;
            text-align: center;
        }

        .status-box .title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .status-box .subtitle {
            font-size: 11px;
            line-height: 1.4;
        }

        footer {
            position: fixed;
            bottom: 1.9cm;
            left: 0.4cm;
            right: 0cm;
            height: 0cm;
        }

        img {
            max-width: 100px;
            height: auto;
        }

        table {
            font-size: 0.8rem;
            margin: 0;
        }

        table thead {
            border-bottom: 1px solid rgb(206, 206, 206);
            border-top: 1px solid rgb(206, 206, 206);
        }

        .caption {
            display: block;
        }

        .row {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }

        .col-1, .col-2, .col-3, .col-4, .col-5, .col-6,
        .col-7, .col-8, .col-9, .col-10, .col-11, .col-12,
        .col, .col-auto {
            position: relative;
            width: 100%;
            min-height: 1px;
            padding-right: 15px;
            padding-left: 15px;
        }

        .col-6 { -ms-flex: 0 0 50%; flex: 0 0 50%; max-width: 50%; }
        .col-12 { -ms-flex: 0 0 100%; flex: 0 0 100%; max-width: 100%; }

        .text-left   { text-align: left !important; }
        .text-right  { text-align: right !important; }
        .text-center { text-align: center !important; }
        .w-100 { width: 100% !important; }

        .mt-0 { margin-top: 0 !important; }
        .mb-0 { margin-bottom: 0 !important; }
        .mb-1 { margin-bottom: 0.25rem !important; }
        .mb-2 { margin-bottom: 0.5rem !important; }
        .ml-3 { margin-left: 1rem !important; }
        .mr-3 { margin-right: 1rem !important; }

        .float-right { float: right !important; }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #333;
            padding-bottom: 3px;
            margin-top: 14px;
            margin-bottom: 6px;
        }

        * {
            font-family: "Lucida Console", "Courier New", monospace;
        }
    </style>

</head>

<header>
    <div class="headReport" style="display:flex; justify-content: space-between; padding-top:1rem">

        @if($config->logo != null)
            <img style="margin-top: -65px; height: 80px;"
                src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(@public_path('/uploads/logos/' . $config->logo))) }}"
                alt="Logo" class="mb-2">
        @else
            <img style="margin-top: -75px;"
                src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(@public_path('superstore_logo.png'))) }}"
                alt="Logo" class="mb-2">
        @endif

        <div class="row text-right">
            <div class="col-12" style="margin-top: -50px;">
                <small class="float-right" style="color:grey; font-size: 11px;">Emissão:
                    {{ date('d/m/Y - H:i') }}</small><br>
            </div>
        </div>

        <div class="row">
            <h4 style="text-align:center; margin-top: -50px;">COMPROVANTE DE TROCA</h4>
        </div>

    </div>
</header>

<body>

    <div class="status-box">
        <div class="title">COMPROVANTE DE TROCA</div>
        <div class="subtitle">COMPROVANTE NÃO FISCAL | DOCUMENTO SEM VALIDADE FISCAL</div>
    </div>

    {{-- Dados da empresa --}}
    <table>
        <tr>
            <td class="text-left" style="width: 700px;">
                <strong>Dados da empresa</strong>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td class="b-top text-left" style="width: 450px;">
                Razão social: <strong>{{ $config->nome }}</strong>
            </td>
            <td class="b-top" style="width: 247px;">
                CNPJ: <strong>{{ __setMask($config->cpf_cnpj, "###.###.###/####-##") }}</strong>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td class="b-top text-left" style="width: 450px;">
                Insc. Estadual: <strong>{{ $config->ie }}</strong>
            </td>
            <td class="b-top" style="width: 247px;">
                Telefone: <strong>{{ $config->celular }}</strong>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td class="b-top text-left" style="width: 700px;">
                Endereço: <strong>{{ $config->rua }}, {{ $config->numero }} - {{ $config->bairro }} -
                    {{ $config->cidade->nome }} ({{ $config->cidade->uf }})</strong>
            </td>
        </tr>
    </table>
    <br>

    {{-- Dados do cliente --}}
    @php
        $clienteDoc = null;
        if (isset($item->nfe) && isset($item->nfe->cliente)) {
            $clienteDoc = $item->nfe->cliente;
        } elseif (isset($item->nfce) && isset($item->nfce->cliente)) {
            $clienteDoc = $item->nfce->cliente;
        }
    @endphp
    @if($clienteDoc)
        <table>
            <tr>
                <td class="text-left" style="width: 700px;">
                    <strong>Dados do cliente</strong>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td class="b-top text-left" style="width: 450px;">
                    Nome: <strong>{{ $clienteDoc->razao_social }}</strong>
                </td>
                <td class="b-top" style="width: 247px;">
                    CPF/CNPJ: <strong>{{ $clienteDoc->cpf_cnpj }}</strong>
                </td>
            </tr>
        </table>
        <br>
    @endif

    {{-- Identificação do documento --}}
    <table>
        <tr>
            <td class="b-top text-left" style="width: 350px;">
                Código da venda: <strong>{{ $item->nfce ? $item->nfce->numero_sequencial : $item->nfe->numero_sequencial }}</strong>
            </td>
            <td class="b-top text-left" style="width: 350px;">
                Código da troca: <strong>{{ $item->numero_sequencial }}</strong>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td class="b-top text-left" style="width: 350px;">
                Data da venda: <strong>{{ __data_pt($item->nfce ? $item->nfce->created_at : $item->nfe->created_at) }}</strong>
            </td>
            <td class="b-top text-left" style="width: 350px;">
                Data da troca: <strong>{{ __data_pt($item->created_at) }}</strong>
            </td>
        </tr>
    </table>
    <br>

    {{-- ITENS DA VENDA ORIGINAL --}}
    <table>
        <tr>
            <td class="b-top b-bottom" style="width: 700px; height: 40px;">
                <strong>ITENS DA VENDA ORIGINAL</strong>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <td style="width: 80px; text-align: left;">Código</td>
                <td style="width: 350px; text-align: left;">Descrição</td>
                <td style="width: 80px; text-align: left;">Qtd.</td>
                <td style="width: 95px; text-align: left;">Vl Unit.</td>
                <td style="width: 95px; text-align: left;">Vl Total</td>
            </tr>
        </thead>
        <tbody>
            @foreach(($item->nfe ? $item->nfe->itens : $item->nfce->itens) as $i)
            <tr>
                <td class="b-top text-left">{{ $i->produto->numero_sequencial }}</td>
                <td class="b-top text-left">{{ $i->descricao() }}</td>
                <td class="b-top text-left">
                    @if(!$i->produto->unidadeDecimal())
                        {{ number_format($i->quantidade, 0, ',', '.') }}
                    @else
                        {{ number_format($i->quantidade, 3, ',', '.') }}
                    @endif
                </td>
                <td class="b-top text-left">{{ __moeda($i->valor_unitario) }}</td>
                <td class="b-top text-left">{{ __moeda($i->sub_total) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br>

    {{-- ITENS ALTERADOS (trocados) --}}
    <table>
        <tr>
            <td class="b-top b-bottom" style="width: 700px; height: 40px;">
                <strong>ITENS ALTERADOS (TROCA)</strong>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <td style="width: 80px; text-align: left;">Código</td>
                <td style="width: 350px; text-align: left;">Descrição</td>
                <td style="width: 80px; text-align: left;">Qtd.</td>
                <td style="width: 95px; text-align: left;">Vl Unit.</td>
                <td style="width: 95px; text-align: left;">Vl Total</td>
            </tr>
        </thead>
        <tbody>
            @php $somaLinhas = 0; $somaQtd = 0; @endphp
            @foreach($item->itens as $i)
            @php $somaLinhas++; $somaQtd += $i->quantidade; @endphp
            <tr>
                <td class="b-top text-left">{{ $i->produto->numero_sequencial }}</td>
                <td class="b-top text-left">{{ $i->descricao() }}</td>
                <td class="b-top text-left">
                    @if(!$i->produto->unidadeDecimal())
                        {{ number_format($i->quantidade, 0, ',', '.') }}
                    @else
                        {{ number_format($i->quantidade, 3, ',', '.') }}
                    @endif
                </td>
                <td class="b-top text-left">{{ __moeda($i->valor_unitario) }}</td>
                <td class="b-top text-left">{{ __moeda($i->sub_total) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br>

    {{-- Totais --}}
    <table>
        <tr>
            <td class="b-top b-bottom" style="width: 350px;">
                <center><strong>Qtde de linhas: {{ $somaLinhas }}</strong></center>
            </td>
            <td class="b-top b-bottom" style="width: 350px;">
                <center><strong>Qtde total de itens: {{ $somaQtd }}</strong></center>
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="text-left" style="width: 240px;">
                Desconto (-): <strong>R$ {{ __moeda($item->desconto) }}</strong>
            </td>
            <td class="text-left" style="width: 240px;">
                Acréscimo (+): <strong>R$ {{ __moeda($item->acrescimo) }}</strong>
            </td>
            <td class="text-left" style="width: 220px;">
                Valor Total:
                <strong>
                    @isset($preVenda)
                        R$ {{ __moeda($item->valor_total) }}
                    @else
                        R$ {{ __moeda($item->total) }}
                    @endisset
                </strong>
            </td>
        </tr>
    </table>

    @if($item->valor_entrega > 0)
    <table>
        <tr>
            <td class="text-left" style="width: 240px;">
                Frete (+): <strong>R$ {{ __moeda($item->valor_entrega) }}</strong>
            </td>
        </tr>
    </table>
    @endif

    @if($item->valor_frete > 0)
    <table>
        <tr>
            <td class="text-left" style="width: 240px;">
                Valor do Frete (+): <strong>R$ {{ __moeda($item->valor_frete) }}</strong>
            </td>
        </tr>
    </table>
    @endif

    <br>

    {{-- Pagamento --}}
    <table>
        <tr>
            <td class="b-bottom" style="width: 700px; height: 40px;">
                <strong>PAGAMENTO:</strong>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td class="b-bottom text-left" style="width: 350px;">
                Tipo de pagamento
            </td>
            <td class="b-bottom text-left" style="width: 350px;">
                Valor pago
            </td>
        </tr>
        <tr>
            <td class="text-left">
                <strong>{{ \App\Models\Nfce::getTipoPagamento($item->tipo_pagamento) }}</strong>
            </td>
            <td class="text-left">
                <strong>R$ {{ __moeda($item->valor_troca) }}</strong>
            </td>
        </tr>
    </table>
    <br>

    {{-- Observação --}}
    @if($item->observacao)
    <table>
        <tr>
            <td class="text-left" style="width: 700px;">
                Observação: <strong>{{ $item->observacao }}</strong>
            </td>
        </tr>
    </table>
    @endif

    {{-- Mensagem padrão --}}
    @if($configGeral && $configGeral->mensagem_padrao_impressao_venda != "")
        <br>
        {!! $configGeral->mensagem_padrao_impressao_venda !!}
    @endif

</body>

<footer id="footer_imagem">
    <table style="width: 100%; border-top: 1px solid #999;">
        <tbody>
            <tr>
                <td class="text-left ml-3 mb-3">
                    {{ env('SITE_SUPORTE') }}
                </td>
                <td class="text-right">
                    <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(@public_path('superstore_logo.png'))) }}"
                        alt="Logo" class="mr-3">
                </td>
            </tr>
        </tbody>
    </table>
</footer>

@if(($printMode ?? 'pdf') === 'html')
<script type="text/javascript">
    window.onload = function() {
        window.print();
        setTimeout(() => { window.close(); }, 10);
    }
</script>
@endif

</html>
