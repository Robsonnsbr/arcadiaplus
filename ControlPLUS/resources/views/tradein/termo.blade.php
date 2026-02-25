<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Termo Trade-in</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { font-size: 16px; margin-bottom: 8px; }
        p { margin: 6px 0; }
        .box { border: 1px solid #333; padding: 10px; margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Termo de Trade-in</h1>
    <p><strong>Trade-in:</strong> #{{ $tradein->id }}</p>
    <p><strong>Item:</strong> {{ $tradein->nome_item }}</p>
    <p><strong>Cliente ID:</strong> {{ $tradein->cliente_id }}</p>
    <p><strong>Valor avaliado:</strong> {{ $tradein->valor_avaliado ? 'R$ ' . __moeda($tradein->valor_avaliado) : '--' }}</p>

    <div class="box">
        <p>Declaro estar ciente das condições do trade-in acima.</p>
    </div>

    <p style="margin-top: 30px;">__________________________________________</p>
    <p>Assinatura do cliente</p>
</body>
</html>
