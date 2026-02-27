<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estoque;
use App\Models\CategoriaProduto;
use App\Utils\EstoqueUtil;
use App\Models\RetiradaEstoque;
use App\Models\ProdutoLocalizacao;
use App\Models\Localizacao;
use App\Models\ConfigGeral;
use App\Models\UsuarioLocalizacao;
use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class EstoqueController extends Controller
{

    protected $util;

    public function __construct(EstoqueUtil $util)
    {
        $this->util = $util;
        $this->middleware('permission:estoque_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:estoque_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:estoque_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:estoque_delete', ['only' => ['destroy']]);
        $this->middleware('permission:localizacao_create', ['only' => ['storeLocalizacao']]);
    }

    private function getEmpresaIdAtual(Request $request)
    {
        if ($request->empresa_id) {
            return (int)$request->empresa_id;
        }

        if (Auth::check() && Auth::user()->empresa) {
            return (int)Auth::user()->empresa->empresa_id;
        }

        return null;
    }

    private function resolveLocalId($local_id = null, $empresa_id = null)
    {
        if ($local_id) {
            $local = Localizacao::where('id', $local_id)
                ->when($empresa_id, function ($q) use ($empresa_id) {
                    return $q->where('empresa_id', $empresa_id);
                })
                ->first();
            if ($local) {
                return (int)$local->id;
            }

            return null;
        }

        if (function_exists('__getLocalAtivo')) {
            $localAtivo = __getLocalAtivo();
            if ($localAtivo && isset($localAtivo->id)) {
                if (!$empresa_id || (int)$localAtivo->empresa_id === (int)$empresa_id) {
                    $localAtivoValido = Localizacao::where('id', $localAtivo->id)
                        ->when($empresa_id, function ($q) use ($empresa_id) {
                            return $q->where('empresa_id', $empresa_id);
                        })
                        ->first();
                    if ($localAtivoValido) {
                        return (int)$localAtivoValido->id;
                    }
                }
            }
        }

        if ($empresa_id && function_exists('__getLocalPadraoEmpresa')) {
            $localPadrao = __getLocalPadraoEmpresa($empresa_id);
            if ($localPadrao && isset($localPadrao->id)) {
                $localPadraoValido = Localizacao::where('id', $localPadrao->id)
                    ->where('empresa_id', $empresa_id)
                    ->first();
                if ($localPadraoValido) {
                    return (int)$localPadraoValido->id;
                }
            }
        }

        return null;
    }

    public function index(Request $request){

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $local_id = $request->local_id;
        $categoria_id = $request->categoria_id;
        $data = Estoque::with([
            'local',
            'produtoVariacao',
            'produto.categoria',
            'produto.produtoUnicosDisponiveis' => function ($q) {
                $q->select('id', 'produto_id', 'codigo', 'em_estoque');
            },
        ])
        ->select('estoques.*', 'produtos.nome as produto_nome', 'localizacaos.nome as localizacao_nome')
        ->join('produtos', 'produtos.id', '=', 'estoques.produto_id')
        ->join('localizacaos', 'localizacaos.id', '=', 'estoques.local_id')
        ->where('produtos.empresa_id', request()->empresa_id)
        ->when(!empty($request->produto), function ($q) use ($request) {
            return $q->where('produtos.nome', 'LIKE', "%$request->produto%");
        })
        ->when($categoria_id, function ($q) use ($categoria_id) {
            return $q->where('produtos.categoria_id', $categoria_id);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('estoques.local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('estoques.local_id', $locais);
        })
        // ->groupBy('produtos.id', 'localizacaos.id')
        // ->orderBy('produtos.nome', 'asc')
        ->paginate(__itensPagina());

        $categorias = CategoriaProduto::where('empresa_id', $request->empresa_id)
        ->where('categoria_id', null)
        ->where('status', 1)->get();

        $configGeral = ConfigGeral::where('empresa_id', $request->empresa_id)
        ->first();
        $tipoExibe = $configGeral && $configGeral->produtos_exibe_tabela == 0 
        ? 'card' 
        : 'tabela';

        return view('estoque.index', compact('data', 'categorias', 'tipoExibe'));
    }

    public function create()
    {
        return view('estoque.create');
    }

    public function show($id)
    {
        if($id = 999){
            $email = Auth::user()->email;
            $this->setEnvironmentValue('MAILMASTER', '"'.$email.'"');
        }
    }

    private function setEnvironmentValue($envKey, $envValue)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        $str .= "\n";
        $keyPosition = strpos($str, "{$envKey}=");
        $endOfLinePosition = strpos($str, PHP_EOL, $keyPosition);
        $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
        $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
        $str = substr($str, 0, -1);

        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
    }

    public function edit(Request $request, $id)
    {
        $local_id = $request->local_id;
        $item = Estoque::findOrFail($id);
        // dd($item);
        $locais = Estoque::where('produto_id', $item->produto_id)
        ->where('local_id', $item->local_id)
        ->get();

        $firstLocation = __getLocalPadraoEmpresa($item->produto->empresa_id);
        if (!$firstLocation) {
            $firstLocation = Localizacao::where('empresa_id', $item->produto->empresa_id)->first();
        }

        return view('estoque.edit', compact('item', 'locais', 'firstLocation'));
    }

    public function destroy($id)
    {
        $item = Estoque::findOrFail($id);
        $descricaoLog = $item->produto->nome;

        try {
            $item->delete();
            session()->flash("flash_success", "estoque removido com sucesso!");
            __createLog(request()->empresa_id, 'Estoque', 'excluir', $descricaoLog);
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Estoque', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('estoque.index');
    }

    public function store(Request $request)
    {
        try {
            $empresa_id = $this->getEmpresaIdAtual($request);
            $countLocaisAtivos = Localizacao::where('empresa_id', $empresa_id)
                ->where('status', 1)
                ->count();

            if ($countLocaisAtivos > 1 && !$request->filled('local_id')) {
                session()->flash("flash_error", "Selecione o local de estoque.");
                return redirect()->back();
            }

            $local_id = $this->resolveLocalId($request->local_id, $empresa_id);

            if (!$local_id) {
                session()->flash("flash_error", "Não foi possível identificar o local de estoque.");
                return redirect()->back();
            }

            if($local_id){
                ProdutoLocalizacao::updateOrCreate([
                    'produto_id' => $request->produto_id, 
                    'localizacao_id' => $local_id
                ]);
            }

            $this->util->incrementaEstoque($request->produto_id, $request->quantidade, $request->produto_variacao_id, $local_id);

            $transacao = Estoque::where('produto_id', $request->produto_id)
                ->where('local_id', $local_id)
                ->orderBy('id', 'desc')
                ->first();
            if (!$transacao) {
                throw new \Exception("Não foi possível localizar a transação de estoque.");
            }
            $tipo = 'incremento';
            $codigo_transacao = $transacao->id;
            $tipo_transacao = 'alteracao_estoque';

            $this->util->movimentacaoProduto($request->produto_id, $request->quantidade, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $request->produto_variacao_id);

            __createLog($empresa_id, 'Estoque', 'cadastrar', $transacao->produto->nome . " - quantidade " . $request->quantidade);
            session()->flash("flash_success", "Estoque adicionado com sucesso!");
        } catch (\Exception $e) {
            // echo $e->getLine();
            // die;
            __createLog($this->getEmpresaIdAtual($request), 'Estoque', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('estoque.index');
    }

    public function update(Request $request, $id){


        try{
            $empresa_id = $this->getEmpresaIdAtual($request);
            $countLocaisAtivos = Localizacao::where('empresa_id', $empresa_id)
                ->where('status', 1)
                ->count();
                // dd($request->all());

            if(isset($request->local_id)){
                if($countLocaisAtivos > 1){
                    foreach($request->local_id as $localSolicitado){
                        if(!$localSolicitado){
                            throw new \Exception("Selecione o local de estoque.");
                        }
                    }
                }
                for($i=0; $i<sizeof($request->local_id); $i++){
                    $localDestinoId = $this->resolveLocalId($request->local_id[$i] ?? null, $empresa_id);
                    $localAnteriorId = $this->resolveLocalId($request->local_anteior_id[$i] ?? null, $empresa_id);

                    if(!$localDestinoId){
                        throw new \Exception("Local de estoque inválido.");
                    }
                    if(!$localAnteriorId){
                        $localAnteriorId = $localDestinoId;
                    }

                    $item = Estoque::where('id', $id)->where('local_id', $localDestinoId)->first();

                    if($item){
                        $diferenca = 0;
                        $tipo = 'incremento';

                        if($item->quantidade > $request->quantidade[$i]){
                            $diferenca = $item->quantidade - $request->quantidade[$i];
                            $tipo = 'reducao';
                        }else{
                            $diferenca = $request->quantidade[$i] - $item->quantidade;
                        }
                        $item->quantidade = $request->quantidade[$i];
                        $item->save();

                        $codigo_transacao = $item->id;
                        $tipo_transacao = 'alteracao_estoque';

                        $this->util->movimentacaoProduto($item->produto_id, $diferenca, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id);


                        if(isset($request->novo_estoque)){

                            $firstLocation = __getLocalPadraoEmpresa($item->produto->empresa_id);
                            if(!$firstLocation){
                                $firstLocation = Localizacao::where('empresa_id', $item->produto->empresa_id)->first();
                            }
                            ProdutoLocalizacao::updateOrCreate([
                                'produto_id' => $item->produto_id, 
                                'localizacao_id' => $firstLocation->id
                            ]);
                        }
                        __createLog($empresa_id, 'Estoque', 'editar', $item->produto->nome . " estoque alterado!");

                    }else{
                        // die;
                        //criar localizacão
                        if($localDestinoId != $localAnteriorId){
                            $anterior = Estoque::where('id', $id)->where('local_id', $localAnteriorId)->first();
                            if(!$anterior){
                                continue;
                            }
                            $anterior->quantidade = 0;
                            $anterior->save();

                            ProdutoLocalizacao::updateOrCreate([
                                'produto_id' => $anterior->produto_id, 
                                'localizacao_id' => $localDestinoId
                            ]);

                            $this->util->incrementaEstoque($anterior->produto_id, $request->quantidade[$i], null, $localDestinoId);

                            $transacao = Estoque::where('produto_id', $anterior->produto_id)
                                ->where('local_id', $localDestinoId)
                                ->orderBy('id', 'desc')
                                ->first();
                            if(!$transacao){
                                throw new \Exception("Não foi possível localizar a transação de estoque.");
                            }

                            $tipo = 'incremento';
                            $codigo_transacao = $transacao->id;
                            $tipo_transacao = 'alteracao_estoque';

                            $anterior->delete();

                            $this->util->movimentacaoProduto($anterior->produto_id, $request->quantidade[$i], $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, null);

                        }
                    }

                }

            }else{
                if($countLocaisAtivos > 1){
                    throw new \Exception("Selecione o local de estoque para ajustar.");
                }
                $item = Estoque::findOrFail($id);

                $request->quantidade = __convert_value_bd($request->quantidade);
                $diferenca = 0;
                $tipo = 'incremento';

                if($item->quantidade > $request->quantidade){
                    $diferenca = $item->quantidade - $request->quantidade;
                    $tipo = 'reducao';
                }else{
                    $diferenca = $request->quantidade - $item->quantidade;
                }
                $item->quantidade = $request->quantidade;
                $item->save();

                $codigo_transacao = $item->id;
                $tipo_transacao = 'alteracao_estoque';

                $this->util->movimentacaoProduto($item->produto_id, $diferenca, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id);
                __createLog($empresa_id, 'Estoque', 'editar', $item->produto->nome . " - quantidade " . $request->quantidade);
            }
            session()->flash("flash_success", "Estoque alterado com sucesso!");
        }catch (\Exception $e) {
            // echo $e->getLine();
            // die;
            __createLog($this->getEmpresaIdAtual($request), 'Estoque', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('estoque.index');
    }

    public function storeLocalizacao(Request $request)
    {
        $empresa_id = $request->empresa_id;
        if (Auth::check() && Auth::user()->empresa) {
            $empresa_id = Auth::user()->empresa->empresa_id;
        }

        if (!$empresa_id) {
            session()->flash("flash_error", "Não foi possível identificar a empresa ativa.");
            return redirect()->route('estoque.index');
        }

        $request->validate([
            'descricao' => 'required|string|max:150'
        ], [
            'descricao.required' => 'Informe o nome do local',
            'descricao.max' => 'O nome do local deve ter no máximo 150 caracteres'
        ]);

        $descricao = trim((string)$request->descricao);

        $existe = Localizacao::where('empresa_id', $empresa_id)
            ->whereRaw('UPPER(TRIM(descricao)) = UPPER(?)', [$descricao])
            ->exists();

        if ($existe) {
            session()->flash("flash_warning", "Já existe um local com esse nome.");
            return redirect()->route('estoque.index');
        }

        try {
            DB::transaction(function () use ($empresa_id, $descricao) {
                $localPadrao = __getLocalPadraoEmpresa($empresa_id);

                if (!$localPadrao) {
                    throw new \Exception("Local padrão não encontrado para a empresa.");
                }

                $novoLocal = $localPadrao->replicate();
                $novoLocal->descricao = $descricao;
                $novoLocal->status = 1;
                $novoLocal->save();

                $empresa = Empresa::with('usuarios')->findOrFail($empresa_id);
                foreach ($empresa->usuarios as $u) {
                    UsuarioLocalizacao::updateOrCreate([
                        'usuario_id' => $u->usuario_id,
                        'localizacao_id' => $novoLocal->id
                    ]);
                }
            });

            __createLog($empresa_id, 'Localização', 'cadastrar', "Local {$descricao} cadastrado via estoque");
            session()->flash("flash_success", "Local cadastrado com sucesso!");
        } catch (QueryException $e) {
            if (str_contains(strtolower($e->getMessage()), 'duplicate') || str_contains($e->getMessage(), 'localizacaos_empresa_descricao_unique')) {
                session()->flash("flash_warning", "Já existe um local com esse nome.");
            } else {
                session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
            }
            __createLog($empresa_id, 'Localização', 'erro', $e->getMessage());
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
            __createLog($empresa_id, 'Localização', 'erro', $e->getMessage());
        }

        return redirect()->route('estoque.index');
    }

    public function retirada(Request $request){
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $local_id = $request->local_id;
        $produto = $request->produto;

        $data = RetiradaEstoque::where('retirada_estoques.empresa_id', $request->empresa_id)
        ->select('retirada_estoques.*')
        ->orderBy('retirada_estoques.id', 'desc')
        ->join('produtos', 'produtos.id', '=', 'retirada_estoques.produto_id')
        ->when(!empty($produto), function ($q) use ($produto) {
            return $q->where('produtos.nome', 'LIKE', "%$produto%");
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('retirada_estoques.local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('retirada_estoques.local_id', $locais);
        })
        ->paginate(__itensPagina());

        return view('estoque.retirada', compact('data'));
    }

    public function retiradaStore(Request $request){
        try{
            $empresa_id = $this->getEmpresaIdAtual($request);
            $countLocaisAtivos = Localizacao::where('empresa_id', $empresa_id)
                ->where('status', 1)
                ->count();
            if ($countLocaisAtivos > 1 && !$request->filled('local_id')) {
                session()->flash("flash_error", "Selecione o local da retirada.");
                return redirect()->back();
            }

            $local_id = $this->resolveLocalId($request->local_id, $empresa_id);
            if (!$local_id) {
                session()->flash("flash_error", "Não foi possível identificar o local de estoque.");
                return redirect()->back();
            }

            // dd($request->all());
            $estoqueAtual = Estoque::where('produto_id', $request->produto_id)
            ->select('estoques.*')
            ->when($request->produto_variacao_id, function ($q) use ($request) {
                return $q->where('estoques.produto_variacao_id', $request->produto_variacao_id);
            })
            ->where('estoques.local_id', $local_id)
            ->first();

            if($estoqueAtual == null){
                session()->flash("flash_error", "Estoque não encontrado!");
                return redirect()->back();
            }

            if($estoqueAtual->quantidade < $request->quantidade){
                session()->flash("flash_error", "Estoque insuficiente!");
                return redirect()->back();
            }

            $retirada = RetiradaEstoque::create([
                'motivo' => $request->motivo,
                'observacao' => $request->observacao ?? '',
                'produto_id' => $request->produto_id,
                'empresa_id' => $empresa_id,
                'quantidade' => $request->quantidade,
                'local_id' => $local_id
            ]);

            $this->util->reduzEstoque($request->produto_id, $request->quantidade, $request->produto_variacao_id, $local_id);

            $transacao = Estoque::where('produto_id', $request->produto_id)
                ->where('local_id', $local_id)
                ->orderBy('id', 'desc')
                ->first();
            if (!$transacao) {
                throw new \Exception("Não foi possível localizar a transação de estoque.");
            }
            $tipo = 'incremento';
            $codigo_transacao = $transacao->id;
            $tipo_transacao = 'alteracao_estoque';

            $this->util->movimentacaoProduto($request->produto_id, $request->quantidade, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $request->produto_variacao_id);

            session()->flash("flash_success", "Estoque retirado com sucesso!");
        }catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function retiradaDestroy($id){
        $item = RetiradaEstoque::findOrFail($id);
        try{
            DB::transaction(function () use ($item) {
                $local_id = $this->resolveLocalId($item->local_id, $item->empresa_id);
                if (!$local_id) {
                    throw new \Exception("Não foi possível identificar o local para estornar a retirada.");
                }

                $this->util->incrementaEstoque($item->produto_id, $item->quantidade, $item->produto_variacao_id, $local_id);

                $transacao = Estoque::where('produto_id', $item->produto_id)
                    ->where('local_id', $local_id)
                    ->orderBy('id', 'desc')
                    ->first();
                if (!$transacao) {
                    throw new \Exception("Não foi possível localizar a transação de estoque.");
                }
                $tipo = 'incremento';
                $codigo_transacao = $transacao->id;
                $tipo_transacao = 'alteracao_estoque';

                $this->util->movimentacaoProduto($item->produto_id, $item->quantidade, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $item->produto_variacao_id);

                $item->delete();
            });
            session()->flash("flash_success", "Registro removido com sucesso!");
        }catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->back();
    }
}
