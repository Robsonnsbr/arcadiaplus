<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nfce;
use App\Models\Nfe;
use App\Models\Troca;
use App\Models\Produto;
use App\Models\Caixa;
use App\Models\Funcionario;
use App\Models\ItemTroca;
use App\Models\Cliente;
use App\Models\CreditoCliente;
use App\Models\ProdutoUnico;
use Illuminate\Support\Str;
use App\Utils\EstoqueUtil;
use App\Utils\QuantidadeUtil;
use App\Utils\StatusKeyUtil;
use Illuminate\Support\Facades\DB;
use App\Services\TrocaSerialService;
use Carbon\Carbon;

class TrocaController extends Controller
{
    private const HORAS_PRAZO_DEVOLUCAO_PDV = 24;

    protected $util;

    /** @var TrocaSerialService */
    protected $seriais;

    public function __construct(EstoqueUtil $util, TrocaSerialService $seriais)
    {
        $this->util = $util;
        $this->seriais = $seriais;
    }

    private function getLastNumero($empresa_id)
    {
        $last = Troca::where('empresa_id', $empresa_id)
            ->orderBy('numero_sequencial', 'desc')
            ->where('numero_sequencial', '>', 0)->first();
        $numero = $last != null ? $last->numero_sequencial : 0;
        $numero++;
        return $numero;
    }

    public function store(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                if ($request->tipo == 'nfce') {
                    $venda = Nfce::lockForUpdate()->findOrFail($request->venda_id);
                } else {
                    $venda = Nfe::lockForUpdate()->findOrFail($request->venda_id);
                }

                if ((int) $venda->empresa_id !== (int) $request->empresa_id) {
                    return response()->json('Venda não pertence à empresa.', 422);
                }

                $temTroca = Troca::where($request->tipo == 'nfce' ? 'nfce_id' : 'nfe_id', $venda->id)->exists();
                if ($temTroca) {
                    return response()->json('Esta venda já possui troca ou devolução registrada.', 422);
                }

                $modalidade = $request->input('modalidade', Troca::MODALIDADE_TROCA);
                if (!in_array($modalidade, [Troca::MODALIDADE_TROCA, Troca::MODALIDADE_DEVOLUCAO_PDV], true)) {
                    return response()->json('Modalidade inválida. Use troca ou devolucao_pdv.', 422);
                }

                $venda->load(['itens.produto']);
                $localId = (int) $venda->local_id;
                $isNfce = $request->tipo === 'nfce';

                $linhasSaidaFinanceiras = $modalidade === Troca::MODALIDADE_DEVOLUCAO_PDV
                    ? []
                    : $this->calcularLinhasSaidaFinanceiras($request);
                $totalRetorno = $this->calcularTotalRetornoVenda($venda);
                $totalSaida = $this->calcularTotalSaida($linhasSaidaFinanceiras);
                $saldoTroca = round($totalSaida - $totalRetorno, 2);
                $valorCreditoCalculado = $saldoTroca < 0 ? abs($saldoTroca) : 0;
                $valorPagoCalculado = $saldoTroca > 0 ? $saldoTroca : 0;

                if ($modalidade === Troca::MODALIDADE_DEVOLUCAO_PDV) {
                    $fimPrazo = Carbon::parse($venda->created_at)->addHours(self::HORAS_PRAZO_DEVOLUCAO_PDV);
                    if (Carbon::now()->gt($fimPrazo)) {
                        return response()->json(
                            'Devolução permitida somente em até ' . self::HORAS_PRAZO_DEVOLUCAO_PDV . ' horas após a data da venda.',
                            422
                        );
                    }
                } else {
                    $novoItemCriado = count($linhasSaidaFinanceiras);
                    if ($novoItemCriado < 1) {
                        return response()->json('Informe ao menos um produto novo na troca (itens que saem do estoque).', 422);
                    }
                }

                if ($request->cliente_id) {
                    $venda->cliente_id = $request->cliente_id;
                }

                $usuarioId = $request->usuario_id ?: (function_exists('get_id_user') ? get_id_user() : null);

                $caixa = Caixa::where('usuario_id', $usuarioId)
                    ->where('status', 1)
                    ->first();

                $funcionario = null;
                if ($usuarioId) {
                    $funcionario = Funcionario::where('empresa_id', $request->empresa_id)
                        ->where('usuario_id', $usuarioId)
                        ->first();
                }

                $valorOriginalVenda = $totalRetorno;
                $seriaisDevolvidos = [];

                $valorTrocaDocumento = $totalSaida;

                $troca = Troca::create([
                    'empresa_id' => $request->empresa_id,
                    'modalidade' => $modalidade,
                    'nfce_id' => $isNfce ? $venda->id : null,
                    'nfe_id' => !$isNfce ? $venda->id : null,
                    'caixa_id' => $caixa ? $caixa->id : null,
                    'funcionario_id' => $funcionario ? $funcionario->id : null,
                    'observacao' => $request->observacao ? $request->observacao : '',
                    'numero_sequencial' => $this->getLastNumero($request->empresa_id),
                    'codigo' => Str::random(8),
                    'valor_troca' => $valorTrocaDocumento,
                    'valor_original' => $valorOriginalVenda,
                    'tipo_pagamento' => $request->tipo_pagamento ? $request->tipo_pagamento : $venda->tipo_pagamento,
                    'seriais_devolvidos' => [],
                ]);

                $venda->total = $modalidade === Troca::MODALIDADE_DEVOLUCAO_PDV
                    ? 0
                    : $totalSaida;
                $venda->save();

                foreach ($venda->itens as $linhaVenda) {
                    $prod = $linhaVenda->produto;
                    if (!$prod || !$prod->gerenciar_estoque) {
                        continue;
                    }
                    if ($prod->tipo_unico) {
                        $seriaisDevolvidos = array_merge(
                            $seriaisDevolvidos,
                            $this->devolverSeriaisLinhaVenda($venda, $isNfce, $localId, $linhaVenda)
                        );
                    }
                    $this->util->incrementaEstoque($linhaVenda->produto_id, $linhaVenda->quantidade, null, $localId);
                }

                $troca->seriais_devolvidos = $seriaisDevolvidos;
                $troca->save();

                if ($modalidade === Troca::MODALIDADE_TROCA && count($linhasSaidaFinanceiras) > 0) {
                    foreach ($linhasSaidaFinanceiras as $i => $linhaSaida) {
                        $produto_id = $linhaSaida['produto_id'];
                        $quantidade = $linhaSaida['quantidade'];

                        $product = Produto::findOrFail($produto_id);
                        $serialCodigo = null;
                        if ($product->tipo_unico && $product->gerenciar_estoque) {
                            if (QuantidadeUtil::toUnits($quantidade) !== QuantidadeUtil::FACTOR) {
                                throw new \Exception(
                                    "Produto serializado {$product->nome} deve ser trocado com quantidade 1 por código."
                                );
                            }
                            $raw = $this->getCodigoUnicoRawFromRequest($request, $i);
                            $serialCodigo = $this->consumirSerialTroca(
                                $raw,
                                $product,
                                $localId,
                                $isNfce ? $venda->id : null,
                                $isNfce ? null : $venda->id
                            );
                        }
                        ItemTroca::create([
                            'produto_id' => $produto_id,
                            'quantidade' => $quantidade,
                            'troca_id' => $troca->id,
                            'valor_unitario' => $linhaSaida['valor_unitario'],
                            'sub_total' => $linhaSaida['sub_total'],
                            'serial_codigo' => $serialCodigo,
                        ]);
                        if ($product->gerenciar_estoque) {
                            $this->util->reduzEstoque($product->id, $quantidade, null, $localId);
                        }
                    }
                }

                if ($valorCreditoCalculado > 0) {
                    if (!$request->cliente_id) {
                        throw new \Exception('Informe o cliente para gerar o crédito da troca/devolução.');
                    }
                    $valorCredito = $valorCreditoCalculado;
                    $cliente = Cliente::lockForUpdate()->findOrFail($request->cliente_id);
                    CreditoCliente::create([
                        'valor' => $valorCredito,
                        'cliente_id' => $cliente->id,
                        'troca_id' => $troca->id,
                        'status' => 1
                    ]);

                    $cliente->valor_credito += $valorCredito;
                    $cliente->save();
                }
                $logCategoria = $modalidade === Troca::MODALIDADE_DEVOLUCAO_PDV ? 'Devolução PDV' : 'Troca';
                __createLog($request->empresa_id, $logCategoria, 'cadastrar', "#$troca->numero_sequencial - R$ " . __moeda($valorPagoCalculado));

                return response()->json($troca->fresh(), 200);
            });
        } catch (\Exception $e) {
            __createLog($request->empresa_id ?? 0, 'Troca', 'erro', $e->getMessage());
            return response()->json($e->getMessage() . ", line: " . $e->getLine() . ", file: " . $e->getFile(), 500);
        }
    }

    private function calcularTotalRetornoVenda($venda): float
    {
        $total = 0;
        foreach ($venda->itens as $linhaVenda) {
            $subTotal = $linhaVenda->sub_total ?? null;
            if ($subTotal === null) {
                $subTotal = (float) $linhaVenda->valor_unitario * (float) $linhaVenda->quantidade;
            }
            $total += (float) $subTotal;
        }
        return round($total, 2);
    }

    /**
     * @return array<int, array{produto_id:int, quantidade:float, valor_unitario:float, sub_total:float}>
     */
    private function calcularLinhasSaidaFinanceiras(Request $request): array
    {
        $produtoIds = $this->getRequestArray($request, 'produto_id');
        $quantidades = $this->getRequestArray($request, 'quantidade');
        $valoresUnitarios = $this->getRequestArray($request, 'valor_unitario');

        $linhas = [];
        foreach ($produtoIds as $i => $produtoId) {
            if ($this->getTipoLinhaFromRequest($request, (int) $i) !== 'saida') {
                continue;
            }
            if ($produtoId === null || $produtoId === '') {
                throw new \Exception('Produto de saída inválido na troca.');
            }
            if (!array_key_exists($i, $quantidades) || !array_key_exists($i, $valoresUnitarios)) {
                throw new \Exception('Dados financeiros incompletos para produto de saída na troca.');
            }

            $quantidade = __convert_value_bd((string) $quantidades[$i]);
            $valorUnitario = __convert_value_bd((string) $valoresUnitarios[$i]);
            if ($quantidade <= 0) {
                throw new \Exception('Quantidade inválida para produto de saída na troca.');
            }
            if ($valorUnitario < 0) {
                throw new \Exception('Valor unitário inválido para produto de saída na troca.');
            }

            $linhas[(int) $i] = [
                'produto_id' => (int) $produtoId,
                'quantidade' => $quantidade,
                'valor_unitario' => $valorUnitario,
                'sub_total' => round($quantidade * $valorUnitario, 2),
            ];
        }

        return $linhas;
    }

    /**
     * @param array<int, array{sub_total:float}> $linhasSaida
     */
    private function calcularTotalSaida(array $linhasSaida): float
    {
        $total = 0;
        foreach ($linhasSaida as $linha) {
            $total += (float) $linha['sub_total'];
        }
        return round($total, 2);
    }

    private function getRequestArray(Request $request, string $key): array
    {
        $value = $request->input($key, []);
        if ($value === null || $value === '') {
            return [];
        }
        return is_array($value) ? $value : [$value];
    }

    private function getTipoLinhaFromRequest(Request $request, int $index): string
    {
        $arr = $request->input('tipo_linha');
        $tipoLinha = is_array($arr) && array_key_exists($index, $arr)
            ? (string) $arr[$index]
            : 'saida';

        return $tipoLinha === 'retorno' ? 'retorno' : 'saida';
    }

    /**
     * @return array<int, array{produto_id:int, codigo:string}>
     */
    private function devolverSeriaisLinhaVenda($venda, bool $isNfce, int $localId, $linhaVenda): array
    {
        $prod = $linhaVenda->produto;
        if (!$prod->tipo_unico) {
            return [];
        }
        $need = (int) (QuantidadeUtil::toUnits($linhaVenda->quantidade) / QuantidadeUtil::FACTOR);
        if ($need < 1) {
            $need = 1;
        }
        $q = ProdutoUnico::query()
            ->where('tipo', 'saida')
            ->where('produto_id', $linhaVenda->produto_id);
        if ($isNfce) {
            $q->where('nfce_id', $venda->id);
        } else {
            $q->where('nfe_id', $venda->id);
        }
        $saidas = $q->orderBy('id')->limit($need)->lockForUpdate()->get();
        if ($saidas->count() < $need) {
            throw new \Exception(
                "Registros de serial insuficientes para devolução do produto {$prod->nome} (esperado {$need}, encontrado {$saidas->count()})."
            );
        }
        $out = [];
        foreach ($saidas as $saida) {
            $this->seriais->restaurarUmaSaidaSerial($saida, $localId);
            $out[] = ['produto_id' => (int) $linhaVenda->produto_id, 'codigo' => (string) $saida->codigo];
        }
        return $out;
    }

    private function getCodigoUnicoRawFromRequest(Request $request, int $index): ?string
    {
        $arr = $request->input('codigo_unico_ids');
        if (is_array($arr) && array_key_exists($index, $arr)) {
            return $arr[$index] !== '' ? (string) $arr[$index] : null;
        }
        return null;
    }

    private function consumirSerialTroca(
        ?string $rawJson,
        Produto $product,
        int $localId,
        ?int $nfceId,
        ?int $nfeId
    ): string {
        if ($rawJson === null || $rawJson === '') {
            throw new \Exception("Produto {$product->nome} exige serial/código único na troca.");
        }
        $parsed = json_decode($rawJson, true);
        if (!is_array($parsed) || count($parsed) < 1) {
            throw new \Exception("Serial inválido para o produto {$product->nome}.");
        }
        $first = $parsed[0];
        $produtoUnicoId = $first['id'] ?? null;
        $codigoUnico = $first['codigo'] ?? null;

        if (!$produtoUnicoId && !$codigoUnico) {
            throw new \Exception("Produto {$product->nome} exige código único/serial para a troca.");
        }

        $query = ProdutoUnico::where('produto_id', $product->id)
            ->where('tipo', 'entrada')
            ->where('em_estoque', 1);

        if ($produtoUnicoId) {
            $query->where('id', (int) $produtoUnicoId);
        } else {
            $query->where('codigo', (string) $codigoUnico);
        }

        $serial = $query->lockForUpdate()->first();
        if (!$serial) {
            throw new \Exception("Código serial informado para {$product->nome} não está disponível em estoque.");
        }

        $statusAtual = StatusKeyUtil::normalizeOrDefault($serial->status_key);
        if ($statusAtual !== StatusKeyUtil::DEFAULT_STATUS) {
            throw new \Exception("Produto serializado {$product->nome} não está disponível para troca (status {$statusAtual}).");
        }

        if ($serial->local_id && (int) $serial->local_id !== (int) $localId) {
            throw new \Exception("Código serial informado para {$product->nome} pertence a outro local de estoque.");
        }

        if (!$serial->local_id) {
            $serial->local_id = $localId;
        }
        $serial->em_estoque = 0;
        $serial->status_key = $statusAtual;
        $serial->save();

        ProdutoUnico::create([
            'nfe_id' => $nfeId,
            'nfce_id' => $nfceId,
            'produto_id' => (int) $product->id,
            'local_id' => (int) $serial->local_id,
            'codigo' => $serial->codigo,
            'observacao' => '',
            'tipo' => 'saida',
            'em_estoque' => 0,
            'status_key' => $statusAtual,
        ]);

        return (string) $serial->codigo;
    }
}
