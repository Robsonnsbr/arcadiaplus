<?php

namespace App\Utils;

use Illuminate\Support\Str;
use App\Models\Deposito;
use App\Models\Estoque;
use App\Models\Produto;
use App\Models\VendiZapConfig;
use App\Models\ProdutoVariacao;
use App\Models\EstoqueAtualProduto;
use App\Models\MovimentacaoProduto;
use App\Services\EstoqueStatusService;
use Illuminate\Support\Facades\Auth;

class EstoqueUtil
{
    protected $urlVendiZap = "https://app.vendizap.com/api";
    protected $estoqueStatusService;

    public function __construct(EstoqueStatusService $estoqueStatusService)
    {
        $this->estoqueStatusService = $estoqueStatusService;
    }

    private function resolveEstoqueContext($local_id = null, $deposito_id = null): array
    {
        if ($deposito_id) {
            $deposito = Deposito::select('id', 'local_id')->find($deposito_id);
            if (!$deposito) {
                throw new \Exception("Depósito de estoque inválido para a operação.");
            }

            if ($local_id && (int)$deposito->local_id !== (int)$local_id) {
                throw new \Exception("Depósito incompatível com o local legado informado.");
            }

            return [
                'deposito_id' => (int)$deposito->id,
                'local_id' => (int)$deposito->local_id,
            ];
        }

        if ($local_id) {
            $deposito = Deposito::ensureDefaultForLocalId((int)$local_id);
            if (!$deposito) {
                throw new \Exception("Depósito padrão não definido para o local de estoque informado.");
            }

            return [
                'deposito_id' => (int)$deposito->id,
                'local_id' => (int)$deposito->local_id,
            ];
        }

        if (Auth::check() && function_exists('__getLocalAtivo')) {
            $localAtivoUsuario = __getLocalAtivo();
            if ($localAtivoUsuario && isset($localAtivoUsuario->id)) {
                return $this->resolveEstoqueContext((int)$localAtivoUsuario->id, null);
            }
        }

        throw new \Exception("Depósito/local de estoque não definido para a operação.");
    }

    private function applyDepositoFiltro($query, ?int $deposito_id = null, ?int $local_id = null)
    {
        if ($deposito_id) {
            return $query->where(function ($q) use ($deposito_id, $local_id) {
                $q->where('deposito_id', $deposito_id);
                if ($local_id) {
                    $q->orWhere(function ($legacy) use ($local_id) {
                        $legacy->whereNull('deposito_id')
                            ->where('local_id', $local_id);
                    });
                }
            });
        }

        if ($local_id) {
            return $query->where('local_id', $local_id);
        }

        return $query->whereNull('deposito_id')->whereNull('local_id');
    }

    private function validaReducaoNaoSerialDisponivel(
        Produto $produto,
        ?Estoque $item,
        $produto_variacao_id,
        int $local_id,
        int $deposito_id,
        int $quantidadeUnits
    ): void
    {
        if ($produto->tipo_unico) {
            return;
        }

        $saldoFisicoAtual = $item ? QuantidadeUtil::toUnits($item->quantidade) : 0;
        $reservadoNaoAtivo = $this->estoqueStatusService->somaReservasNaoAtivoDepositoUnits(
            (int)$produto->empresa_id,
            (int)$produto->id,
            $produto_variacao_id,
            $deposito_id,
            $local_id
        );
        $saldoFisicoFinal = $saldoFisicoAtual - $quantidadeUnits;

        if ($saldoFisicoFinal < $reservadoNaoAtivo) {
            throw new \Exception('Operação inválida: saldo físico ficaria abaixo do reservado em status não-ATIVO.');
        }
    }

    public function incrementaEstoque($produto_id, $quantidade, $produto_variacao_id, $local_id = null, $deposito_id = null)
    {
        $contexto = $this->resolveEstoqueContext($local_id, $deposito_id);
        $local_id = $contexto['local_id'];
        $deposito_id = $contexto['deposito_id'];

        $itemQuery = Estoque::where('produto_id', $produto_id);
        $itemQuery = $this->applyDepositoFiltro($itemQuery, $deposito_id, $local_id);
        $itemQuery = VariacaoQueryUtil::apply($itemQuery, $produto_variacao_id);
        $item = $itemQuery->first();

        $produto = Produto::findOrFail($produto_id);
        if($produto->combo){

            foreach($produto->itensDoCombo as $c){
                $this->incrementaEstoque($c->item_id, $c->quantidade * $quantidade, $produto_variacao_id, $local_id, $deposito_id);
            }
        }else{

            if ($item != null) {
                $atualUnits = QuantidadeUtil::toUnits($item->quantidade);
                $incUnits = QuantidadeUtil::toUnits($quantidade);
                $item->quantidade = QuantidadeUtil::fromUnits($atualUnits + $incUnits);
                $item->deposito_id = $deposito_id;
                $item->local_id = $local_id;
                $item->save();
            } else {
                $item = Estoque::create([
                    'produto_id' => $produto_id,
                    'quantidade' => QuantidadeUtil::fromUnits(QuantidadeUtil::toUnits($quantidade)),
                    'produto_variacao_id' => $produto_variacao_id,
                    'local_id' => $local_id,
                    'deposito_id' => $deposito_id
                ]);
            }
        }

        if($item){
            $atual = EstoqueAtualProduto::where('produto_id', $produto_id)->first();
            if($atual == null){
                EstoqueAtualProduto::create([
                    'quantidade' => $item->quantidade,
                    'produto_id' => $produto_id,
                ]);
            }else{
                $atual->quantidade = $item->quantidade;
                $atual->save();
            }
        }
    }

    public function reduzEstoque($produto_id, $quantidade, $produto_variacao_id, $local_id = null, $deposito_id = null)
    {
        $contexto = $this->resolveEstoqueContext($local_id, $deposito_id);
        $local_id = $contexto['local_id'];
        $deposito_id = $contexto['deposito_id'];

        $itemQuery = Estoque::where('produto_id', $produto_id);
        $itemQuery = $this->applyDepositoFiltro($itemQuery, $deposito_id, $local_id);
        $itemQuery = VariacaoQueryUtil::apply($itemQuery, $produto_variacao_id);
        $item = $itemQuery->first();

        $produto = $item ? $item->produto : Produto::findOrFail($produto_id);

        if ($item != null) {
            if($produto->combo){
                foreach($produto->itensDoCombo as $c){
                    $this->reduzEstoque($c->item_id, $c->quantidade * $quantidade, $produto_variacao_id, $local_id, $deposito_id);
                }
            }else{
                $qtdUnits = QuantidadeUtil::toUnits($quantidade);
                $this->validaReducaoNaoSerialDisponivel(
                    $produto,
                    $item,
                    $produto_variacao_id,
                    $local_id,
                    $deposito_id,
                    $qtdUnits
                );
                $atualUnits = QuantidadeUtil::toUnits($item->quantidade);
                $item->quantidade = QuantidadeUtil::fromUnits($atualUnits - $qtdUnits);
                $item->deposito_id = $deposito_id;
                $item->local_id = $local_id;
                $item->save();
            }
        }else{
            if($produto->combo){
                foreach($produto->itensDoCombo as $c){
                    $this->reduzEstoque($c->item_id, $c->quantidade * $quantidade, $produto_variacao_id, $local_id, $deposito_id);
                }
            }else{
                $this->validaReducaoNaoSerialDisponivel(
                    $produto,
                    null,
                    $produto_variacao_id,
                    $local_id,
                    $deposito_id,
                    QuantidadeUtil::toUnits($quantidade)
                );
            }
        }

        if($produto->vendizap_id){
            $this->SetaEstoqueVendiZap($produto, $quantidade, $produto_variacao_id);
        }

        if($item){
            $atual = EstoqueAtualProduto::where('produto_id', $produto_id)->first();
            if($atual == null){
                EstoqueAtualProduto::create([
                    'quantidade' => $item->quantidade,
                    'produto_id' => $produto_id,
                ]);
            }else{
                $atual->quantidade = $item->quantidade;
                $atual->save();
            }
        }
    }

    private function SetaEstoqueVendiZap($produto, $quantidade, $produto_variacao_id){
        try{
            $estoqueQuery = Estoque::where('produto_id', $produto->id);
            $estoqueQuery = VariacaoQueryUtil::apply($estoqueQuery, $produto_variacao_id);
            $estoque = $estoqueQuery->first();
            $qtdAtual = 0;

            if($estoque){
                $qtdAtual = $estoque->quantidade;
            }
            
            if($produto_variacao_id == null){
                $this->ajustaEstoqueSimples($produto, $qtdAtual);
            }else{
                $this->ajustaEstoqueVariacao($produto, $qtdAtual, $produto_variacao_id);
            }
        }catch(\Exception $e){
            echo $e->getMessage();
            die;
        }

    }

    private function ajustaEstoqueVariacao($item, $quantidade, $produto_variacao_id){
        $produtoVariacao = ProdutoVariacao::find($produto_variacao_id);

        if($produtoVariacao){
            $config = VendiZapConfig::where('empresa_id', $item->empresa_id)->first();


            $ch = curl_init();
            $headers = [
                "X-Auth-Id: " . $config->auth_id,
                "X-Auth-Secret: " . $config->auth_secret,
            ];

            curl_setopt($ch, CURLOPT_URL, $this->urlVendiZap . '/produtos/'.$item->vendizap_id);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_ENCODING, '');
            $data = json_decode(curl_exec($ch));
            curl_close($ch);

            if(!isset($data->estoque)){
                return;
            }

            if(sizeof($data->variacoes) == 1){
                foreach($data->variacoes[0]->variaveis as $variavel){
                    if($variavel->nome == $produtoVariacao->descricao){
                        $data = [
                            'quantidade' => $quantidade,
                            'combinacao' => [
                                $data->variacoes[0]->id => $variavel->id
                            ]
                        ];
                    }
                }
            }else{
                $combinacao = $this->getCombinacao($data, $produtoVariacao);

                $data = [
                    'quantidade' => $quantidade,
                    'combinacao' => $combinacao
                ];
            }

            $ch = curl_init();
            $headers = [
                "X-Auth-Id: " . $config->auth_id,
                "X-Auth-Secret: " . $config->auth_secret,
                'Content-Type: application/json'
            ];


            curl_setopt($ch, CURLOPT_URL, $this->urlVendiZap . '/estoque/'.$item->vendizap_id);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

            $data = json_decode(curl_exec($ch));
            curl_close($ch);
            return $data;
        }
    }

    private function getCombinacao($data, $produtoVariacao){
        $descricao = $produtoVariacao->descricao;
        $temp = explode(" ", $descricao);
        $ret1 = [];
        $ret2 = [];

        foreach($data->variacoes[0]->variaveis as $va){
            if($va->nome == $temp[0]){
                $ret1 = [
                    'variacao_id' => $data->variacoes[0]->id,
                    'variavel_id' => $va->id
                ];
            }
        }

        foreach($data->variacoes[1]->variaveis as $va){
            if($va->nome == $temp[1]){
                $ret2 = [
                    'variacao_id' => $data->variacoes[1]->id,
                    'variavel_id' => $va->id
                ];
            }
        }
        return [
            $ret1['variacao_id'] => $ret1['variavel_id'],
            $ret2['variacao_id'] => $ret2['variavel_id'],
        ];
        
    }

    private function ajustaEstoqueSimples($item, $quantidade){
        $config = VendiZapConfig::where('empresa_id', $item->empresa_id)->first();

        $ch = curl_init();
        $headers = [
            "X-Auth-Id: " . $config->auth_id,
            "X-Auth-Secret: " . $config->auth_secret,
            'Content-Type: application/json'
        ];

        $data = [
            'quantidade' => $quantidade
        ];

        curl_setopt($ch, CURLOPT_URL, $this->urlVendiZap . '/estoque/'.$item->vendizap_id);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

        $data = json_decode(curl_exec($ch));
        curl_close($ch);
        return $data;
    }

    public function reduzComposicao($produto_id, $quantidade, $produto_variacao_id = null)
    {
        $produto = Produto::findOrFail($produto_id);
        foreach ($produto->composicao as $item) {
            $this->reduzEstoque($item->ingrediente_id, ($item->quantidade * $quantidade), $produto_variacao_id);
        }
        $this->incrementaEstoque($produto_id, $quantidade, $produto_variacao_id);
    }

    public function verificaEstoqueComposicao($produto_id, $quantidade, $produto_variacao_id = null)
    {
        $produto = Produto::findOrFail($produto_id);
        $mensagem = "";
        foreach ($produto->composicao as $item) {
            $qtd = $item->quantidade * $quantidade;

            if($item->ingrediente->estoque){
                if($qtd > $item->ingrediente->estoque->quantidade){
                    $mensagem .= $item->ingrediente->nome . " com estoque insuficiente | ";
                }
            }else{
                $mensagem .= $item->ingrediente->nome . " sem nenhum estoque cadastrado | ";
            }
        }
        $mensagem = substr($mensagem, 0, strlen($mensagem)-2);
        return $mensagem;

    }

    public function verificaEstoqueCombo($produto, $quantidade)
    {

        $mensagem = "";
        foreach ($produto->itensDoCombo as $item) {
            $qtd = $item->quantidade * $quantidade;
            if($item->produtoDoCombo->gerenciar_estoque){
                if($item->produtoDoCombo->estoque){
                    if($qtd > $item->produtoDoCombo->estoque->quantidade){
                        $mensagem .= $item->produtoDoCombo->nome . " com estoque insuficiente | ";
                    }
                }else{
                    $mensagem .= $item->produtoDoCombo->nome . " sem nenhum estoque cadastrado | ";
                }
            }
        }
        $mensagem = substr($mensagem, 0, strlen($mensagem)-2);
        return $mensagem;

    }

    public function movimentacaoProduto(
        $produto_id,
        $quantidade,
        $tipo,
        $codigo_transacao,
        $tipo_transacao,
        $user_id,
        $produto_variacao_id = null,
        $local_id = null,
        $deposito_id = null
    ){

        $estoqueQuery = Estoque::where('produto_id', $produto_id);
        $estoqueQuery = VariacaoQueryUtil::apply($estoqueQuery, $produto_variacao_id);
        if ($deposito_id || $local_id) {
            $contexto = $this->resolveEstoqueContext($local_id, $deposito_id);
            $deposito_id = $contexto['deposito_id'];
            $local_id = $contexto['local_id'];
            $estoqueQuery = $this->applyDepositoFiltro($estoqueQuery, $deposito_id, $local_id);
        }
        $estoque = $estoqueQuery->first();

        MovimentacaoProduto::create([
            'produto_id' => $produto_id,
            'quantidade' => $quantidade,
            'tipo' => $tipo,
            'codigo_transacao' => $codigo_transacao,
            'tipo_transacao' => $tipo_transacao,
            'produto_variacao_id' => $produto_variacao_id,
            'deposito_id' => $deposito_id,
            'deposito_origem_id' => null,
            'deposito_destino_id' => null,
            'user_id' => $user_id,
            'estoque_atual' => $estoque ? $estoque->quantidade : 0
        ]);
    }

    public function movimentacaoTransferenciaProduto(
        $produto_id,
        $quantidade,
        $codigo_transacao,
        $user_id,
        $produto_variacao_id = null,
        $local_origem_id = null,
        $deposito_origem_id = null,
        $local_destino_id = null,
        $deposito_destino_id = null
    ): void {
        $origem = $this->resolveEstoqueContext($local_origem_id, $deposito_origem_id);
        $destino = $this->resolveEstoqueContext($local_destino_id, $deposito_destino_id);

        if ((int)$origem['deposito_id'] === (int)$destino['deposito_id']) {
            throw new \Exception('Transferência inválida: depósitos de saída e entrada devem ser diferentes.');
        }

        $estoqueOrigemQuery = Estoque::where('produto_id', $produto_id);
        $estoqueOrigemQuery = VariacaoQueryUtil::apply($estoqueOrigemQuery, $produto_variacao_id);
        $estoqueOrigemQuery = $this->applyDepositoFiltro($estoqueOrigemQuery, $origem['deposito_id'], $origem['local_id']);
        $estoqueOrigem = $estoqueOrigemQuery->first();

        $estoqueDestinoQuery = Estoque::where('produto_id', $produto_id);
        $estoqueDestinoQuery = VariacaoQueryUtil::apply($estoqueDestinoQuery, $produto_variacao_id);
        $estoqueDestinoQuery = $this->applyDepositoFiltro($estoqueDestinoQuery, $destino['deposito_id'], $destino['local_id']);
        $estoqueDestino = $estoqueDestinoQuery->first();

        MovimentacaoProduto::create([
            'produto_id' => $produto_id,
            'quantidade' => $quantidade,
            'tipo' => 'reducao',
            'codigo_transacao' => $codigo_transacao,
            'tipo_transacao' => 'transferencia_estoque',
            'produto_variacao_id' => $produto_variacao_id,
            'deposito_id' => $origem['deposito_id'],
            'deposito_origem_id' => $origem['deposito_id'],
            'deposito_destino_id' => $destino['deposito_id'],
            'user_id' => $user_id,
            'estoque_atual' => $estoqueOrigem ? $estoqueOrigem->quantidade : 0,
        ]);

        MovimentacaoProduto::create([
            'produto_id' => $produto_id,
            'quantidade' => $quantidade,
            'tipo' => 'incremento',
            'codigo_transacao' => $codigo_transacao,
            'tipo_transacao' => 'transferencia_estoque',
            'produto_variacao_id' => $produto_variacao_id,
            'deposito_id' => $destino['deposito_id'],
            'deposito_origem_id' => $origem['deposito_id'],
            'deposito_destino_id' => $destino['deposito_id'],
            'user_id' => $user_id,
            'estoque_atual' => $estoqueDestino ? $estoqueDestino->quantidade : 0,
        ]);
    }

}
