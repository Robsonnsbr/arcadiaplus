<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Termo de Transferência de Propriedade do Aparelho</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        h1 { font-size: 16px; margin: 0 0 8px; text-align: center; }
        h2 { font-size: 13px; margin: 14px 0 6px; }
        p { margin: 4px 0; line-height: 1.35; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 6px; vertical-align: top; }
        th { background: #f1f1f1; }
        .no-border td { border: none; padding: 2px 0; }
        .assinaturas { margin-top: 28px; }
        .assinatura-linha { margin-top: 35px; border-top: 1px solid #333; padding-top: 4px; }
        .small { font-size: 10px; }
        .center { text-align: center; }
    </style>
</head>
<body>
    @php
        $snapshot = $snapshot ?? [];
        $cabecalho = $snapshot['cabecalho'] ?? [];
        $pecas = $snapshot['pecas'] ?? [];
        $checklist = $snapshot['checklist'] ?? [];
        $declaracoes = $snapshot['declaracoes'] ?? [];

        for ($i = count($pecas); $i < 5; $i++) {
            $pecas[] = ['descricao' => '', 'valor' => null];
        }

        $clienteNome = $cabecalho['cliente'] ?? ($cliente->razao_social ?? '-');
        $numeroVenda = $cabecalho['numero_venda'] ?? '-';
        $aparelhoEntrada = $cabecalho['aparelho_entrada'] ?? ($tradein->nome_item ?? '-');
        $dataAvaliacao = $cabecalho['data'] ?? ($tradein->avaliado_em ? $tradein->avaliado_em->format('Y-m-d') : now()->format('Y-m-d'));
        $dataAvaliacaoFormatada = now()->format('d/m/Y');
        try {
            $dataAvaliacaoFormatada = \Carbon\Carbon::parse($dataAvaliacao)->format('d/m/Y');
        } catch (\Throwable $e) {
            $dataAvaliacaoFormatada = now()->format('d/m/Y');
        }
        $imei = $cabecalho['imei'] ?? ($tradein->serial_number ?? '-');
        $consultor = $cabecalho['consultor'] ?? '-';
        $valorAparelho = $cabecalho['valor_aparelho'] ?? $tradein->valor_avaliado ?? $tradein->valor_pretendido;
        $cpfCnpj = $cliente->cpf_cnpj ?? '-';
    @endphp

    <h1>TERMO DE TRANSFERÊNCIA DE PROPRIEDADE DO APARELHO (CONTINGÊNCIA)</h1>

    <table class="no-border">
        <tr>
            <td><strong>CLIENTE:</strong> {{ $clienteNome }}</td>
            <td><strong>NÚMERO DA VENDA:</strong> {{ $numeroVenda ?: '-' }}</td>
        </tr>
        <tr>
            <td><strong>APARELHO DE ENTRADA:</strong> {{ $aparelhoEntrada }}</td>
            <td><strong>DATA:</strong> {{ $dataAvaliacaoFormatada }}</td>
        </tr>
        <tr>
            <td><strong>IMEI:</strong> {{ $imei ?: '-' }}</td>
            <td><strong>CONSULTOR:</strong> {{ $consultor ?: '-' }}</td>
        </tr>
        <tr>
            <td><strong>TRADE-IN:</strong> #{{ $tradein->id }}</td>
            <td><strong>VALOR DO APARELHO:</strong> {{ $valorAparelho !== null && $valorAparelho !== '' ? 'R$ ' . __moeda($valorAparelho) : '--' }}</td>
        </tr>
    </table>

    <h2>PEÇAS - RELATÓRIO INTERNO</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 70%">Peça</th>
                <th style="width: 30%">Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pecas as $peca)
                <tr>
                    <td>{{ $peca['descricao'] ?: '-' }}</td>
                    <td>{{ isset($peca['valor']) && $peca['valor'] !== null && $peca['valor'] !== '' ? 'R$ ' . __moeda($peca['valor']) : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Condições e Declarações</h2>
    <p>Declaro para os devidos fins que estou transferindo a propriedade do aparelho descrito neste documento para a empresa receptora no contexto da operação de trade-in. Declaro ainda que as informações prestadas são verdadeiras, estando ciente de que o equipamento poderá passar por testes técnicos e que divergências poderão gerar revisão do valor.</p>
    <p>
        <strong>Remoção de dados pessoais:</strong>
        {{ ($declaracoes['removeu_dados_pessoais'] ?? '') ?: '--' }}
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>Transferência de propriedade:</strong>
        {{ ($declaracoes['transferencia_propriedade'] ?? '') ?: '--' }}
    </p>

    <h2>Checklist Técnico</h2>
    <table>
        <thead>
            <tr>
                <th>Descrição</th>
                <th style="width: 55px" class="center">Sim</th>
                <th style="width: 55px" class="center">Não</th>
                <th style="width: 38%">Observações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($checklistTemplate as $key => $label)
                @php
                    $resultado = strtoupper(trim((string) ($checklist[$key]['resultado'] ?? '')));
                    $obs = $checklist[$key]['observacao'] ?? '';
                @endphp
                <tr>
                    <td>{{ $label }}</td>
                    <td class="center">{{ $resultado === 'SIM' ? 'X' : '' }}</td>
                    <td class="center">{{ $resultado === 'NAO' ? 'X' : '' }}</td>
                    <td>{{ $obs ?: '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(!empty($snapshot['observacao_geral']))
        <h2>Observações Gerais</h2>
        <p>{{ $snapshot['observacao_geral'] }}</p>
    @endif

    <div class="assinaturas">
        <table class="no-border">
            <tr>
                <td style="width: 50%; padding-right: 18px;">
                    <div class="assinatura-linha">Assinatura e nome do vendedor responsável pela análise</div>
                </td>
                <td style="width: 50%; padding-left: 18px;">
                    <div class="assinatura-linha">Assinatura do cliente / Nome / CPF ({{ $cpfCnpj }})</div>
                </td>
            </tr>
        </table>
    </div>

    <p class="small" style="margin-top: 14px;">Documento gerado em {{ now()->format('d/m/Y H:i') }}.</p>
</body>
</html>
