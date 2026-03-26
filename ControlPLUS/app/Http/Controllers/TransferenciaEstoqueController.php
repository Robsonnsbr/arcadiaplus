<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\TransferenciaEstoque;
use App\Models\ItemTransferenciaEstoque;
use App\Models\Deposito;
use App\Models\Estoque;
use App\Models\MovimentacaoProduto;
use App\Models\Produto;
use App\Models\Empresa;
use App\Models\ProdutoLocalizacao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use App\Utils\EstoqueUtil;

class TransferenciaEstoqueController extends Controller
{

    protected $utilEstoque;
    public function __construct(EstoqueUtil $utilEstoque)
    {
        $this->utilEstoque = $utilEstoque;

        $this->middleware('permission:transferencia_estoque_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:transferencia_estoque_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:transferencia_estoque_delete', ['only' => ['destroy']]);
    }

    private function localIdsAtivosUsuario(): array
    {
        if (!function_exists('__getLocaisAtivoUsuario')) {
            return [];
        }

        return __getLocaisAtivoUsuario()->pluck('id')
            ->map(function ($id) {
                return (int)$id;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function depositosAtivosUsuario(int $empresaId)
    {
        $localIds = $this->localIdsAtivosUsuario();

        if (empty($localIds)) {
            return collect();
        }

        foreach ($localIds as $localId) {
            Deposito::ensureDefaultForLocalId((int)$localId);
        }

        return Deposito::with('localizacao:id,descricao')
            ->where('empresa_id', $empresaId)
            ->where('ativo', 1)
            ->whereIn('local_id', $localIds)
            ->orderBy('local_id')
            ->orderByDesc('padrao')
            ->orderBy('nome')
            ->get();
    }

    private function resolveDepositoTransferencia(
        int $empresaId,
        ?int $depositoId,
        ?int $localId,
        string $label
    ): Deposito {
        $depositoResolvidoId = $depositoId ?: Deposito::resolveIdForLocalId($localId, null);
        if (!$depositoResolvidoId) {
            throw new \Exception("Depósito de {$label} inválido para a transferência.");
        }

        $deposito = Deposito::where('id', $depositoResolvidoId)
            ->where('empresa_id', $empresaId)
            ->where('ativo', 1)
            ->first();

        if (!$deposito) {
            throw new \Exception("Depósito de {$label} inválido para a empresa ativa.");
        }

        if ($localId && (int)$deposito->local_id !== (int)$localId) {
            throw new \Exception("Depósito de {$label} incompatível com a unidade informada.");
        }

        $localIdsPermitidos = $this->localIdsAtivosUsuario();
        if (!empty($localIdsPermitidos) && !in_array((int)$deposito->local_id, $localIdsPermitidos, true)) {
            throw new \Exception("Depósito de {$label} fora das unidades permitidas para o usuário.");
        }

        return $deposito;
    }

    private function applyDepositoFiltro($query, int $depositoId, int $localId)
    {
        return $query->where(function ($q) use ($depositoId, $localId) {
            $q->where('deposito_id', $depositoId)
                ->orWhere(function ($legacy) use ($localId) {
                    $legacy->whereNull('deposito_id')
                        ->where('local_id', $localId);
                });
        });
    }
    
    public function index(Request $request){

        $depositosCount = $this->depositosAtivosUsuario((int)$request->empresa_id)->count();

        if($depositosCount < 2){
            session()->flash('flash_error', 'É necessário ter ao menos 2 depósitos ativos para realizar transferências!');
            return redirect()->back();
        }

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $produto = $request->get('produto');

        $data = TransferenciaEstoque::where('transferencia_estoques.empresa_id', $request->empresa_id)
        ->orderBy('transferencia_estoques.id', 'desc')
        ->select('transferencia_estoques.*')
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('transferencia_estoques.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('transferencia_estoques.created_at', '<=', $end_date);
        })
        ->when(!empty($produto), function ($query) use ($produto) {
            return $query->join('item_transferencia_estoques', 
                'item_transferencia_estoques.transferencia_id', '=', 'transferencia_estoques.id')
            ->join('produtos', 'produtos.id', '=', 'item_transferencia_estoques.produto_id')
            ->where('produtos.nome', 'like', "%$produto%");
        })
        ->paginate(__itensPagina());

        return view('transferencia_estoque.index', compact('data'));
    }

    public function create(){
        $depositos = $this->depositosAtivosUsuario((int)request()->empresa_id);

        if($depositos->count() < 2){
            session()->flash('flash_error', 'É necessário ter ao menos 2 depósitos ativos para realizar transferências!');
            return redirect()->route('transferencia-estoque.index');
        }
        return view('transferencia_estoque.create', compact('depositos'));
    }

    public function store(Request $request){
        try{
            $request->validate([
                'produto_id' => 'required|array|min:1',
                'produto_id.*' => 'required|integer',
                'quantidade' => 'required|array|min:1',
                'quantidade.*' => 'required',
            ]);

            $empresa_id = (int)$request->empresa_id;
            $depositoSaida = $this->resolveDepositoTransferencia(
                $empresa_id,
                $request->filled('deposito_saida_id') ? (int)$request->deposito_saida_id : null,
                $request->filled('local_saida_id') ? (int)$request->local_saida_id : null,
                'saída'
            );
            $depositoEntrada = $this->resolveDepositoTransferencia(
                $empresa_id,
                $request->filled('deposito_entrada_id') ? (int)$request->deposito_entrada_id : null,
                $request->filled('local_entrada_id') ? (int)$request->local_entrada_id : null,
                'entrada'
            );

            if((int)$depositoSaida->id === (int)$depositoEntrada->id){
                session()->flash("flash_error", "Depósitos de saída e entrada devem ser diferentes.");
                return redirect()->back()->withInput();
            }

            if(sizeof($request->produto_id) !== sizeof($request->quantidade)){
                session()->flash("flash_error", "Itens de transferência inválidos.");
                return redirect()->back()->withInput();
            }

            $itens = [];
            for($i=0; $i<sizeof($request->produto_id); $i++){
                $produto = Produto::where('id', $request->produto_id[$i])
                    ->where('empresa_id', $empresa_id)
                    ->first();
                if(!$produto){
                    session()->flash("flash_error", "Produto inválido para a empresa ativa.");
                    return redirect()->back()->withInput();
                }

                if ((bool)$produto->tipo_unico) {
                    session()->flash("flash_error", "{$produto->nome} é serializado. Use 'Gerenciar unidades' no estoque para mover seriais.");
                    return redirect()->back()->withInput();
                }

                $qtd = __convert_value_bd($request->quantidade[$i]);
                if($qtd <= 0){
                    session()->flash("flash_error", "Quantidade inválida para {$produto->nome}.");
                    return redirect()->back()->withInput();
                }

                $estoque = Estoque::where('produto_id', $produto->id)
                    ->when(
                        true,
                        function ($query) use ($depositoSaida) {
                            return $this->applyDepositoFiltro($query, (int)$depositoSaida->id, (int)$depositoSaida->local_id);
                        }
                    )
                    ->first();

                if($estoque == null){
                    session()->flash("flash_error", "{$produto->nome} sem estoque no depósito de saída!");
                    return redirect()->back()->withInput();
                }

                if($estoque->quantidade < $qtd){
                    session()->flash("flash_error", "{$produto->nome} com estoque insuficiente no depósito de saída!");
                    return redirect()->back()->withInput();
                }

                $itens[] = [
                    'produto_id' => (int)$produto->id,
                    'quantidade' => $qtd,
                    'observacao' => $request->observacao_item[$i] ?? ''
                ];
            }

            DB::transaction(function () use ($request, $empresa_id, $depositoSaida, $depositoEntrada, $itens) {
                $item = TransferenciaEstoque::create([
                    'empresa_id' => $empresa_id,
                    'local_saida_id' => $depositoSaida->local_id,
                    'deposito_saida_id' => $depositoSaida->id,
                    'local_entrada_id' => $depositoEntrada->local_id,
                    'deposito_entrada_id' => $depositoEntrada->id,
                    'usuario_id' => Auth::user()->id,
                    'observacao' => $request->observacao ?? '',
                    'codigo_transacao' => Str::random(10)
                ]);

                foreach($itens as $i){
                    ItemTransferenciaEstoque::create([
                        'transferencia_id' => $item->id,
                        'produto_id' => $i['produto_id'],
                        'quantidade' => $i['quantidade'],
                        'observacao' => $i['observacao']
                    ]);

                    ProdutoLocalizacao::updateOrCreate([
                        'produto_id' => $i['produto_id'], 
                        'localizacao_id' => $depositoEntrada->local_id
                    ]);

                    ProdutoLocalizacao::updateOrCreate([
                        'produto_id' => $i['produto_id'], 
                        'localizacao_id' => $depositoSaida->local_id
                    ]);

                    $this->utilEstoque->incrementaEstoque(
                        $i['produto_id'],
                        $i['quantidade'],
                        null,
                        $depositoEntrada->local_id,
                        $depositoEntrada->id
                    );
                    $this->utilEstoque->reduzEstoque(
                        $i['produto_id'],
                        $i['quantidade'],
                        null,
                        $depositoSaida->local_id,
                        $depositoSaida->id
                    );

                    $this->utilEstoque->movimentacaoTransferenciaProduto(
                        $i['produto_id'],
                        $i['quantidade'],
                        $item->id,
                        \Auth::user()->id,
                        null,
                        $depositoSaida->local_id,
                        $depositoSaida->id,
                        $depositoEntrada->local_id,
                        $depositoEntrada->id
                    );
                }
            });

            $descricaoLog = "Saída de {$depositoSaida->nome} para {$depositoEntrada->nome}";
            __createLog($request->empresa_id, 'Transferência de Estoque', 'cadastrar', $descricaoLog);
            session()->flash("flash_success", "Transferência salva!");

        }catch(\Exception $e){
            __createLog(request()->empresa_id, 'Transferência de Estoque', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
            return redirect()->back()->withInput();
        }
        return redirect()->route('transferencia-estoque.index');

    }

    public function destroy($id)
    {
        $item = TransferenciaEstoque::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $depositoSaida = $this->resolveDepositoTransferencia(
                (int)$item->empresa_id,
                $item->deposito_saida_id ? (int)$item->deposito_saida_id : null,
                $item->local_saida_id ? (int)$item->local_saida_id : null,
                'saída'
            );
            $depositoEntrada = $this->resolveDepositoTransferencia(
                (int)$item->empresa_id,
                $item->deposito_entrada_id ? (int)$item->deposito_entrada_id : null,
                $item->local_entrada_id ? (int)$item->local_entrada_id : null,
                'entrada'
            );

            DB::transaction(function () use ($item, $depositoSaida, $depositoEntrada) {
                foreach($item->itens as $p){
                    $this->utilEstoque->incrementaEstoque(
                        $p->produto_id,
                        $p->quantidade,
                        null,
                        $depositoSaida->local_id,
                        $depositoSaida->id
                    );
                    $this->utilEstoque->reduzEstoque(
                        $p->produto_id,
                        $p->quantidade,
                        null,
                        $depositoEntrada->local_id,
                        $depositoEntrada->id
                    );

                }
                MovimentacaoProduto::where('tipo_transacao', 'transferencia_estoque')
                    ->where('codigo_transacao', $item->id)
                    ->delete();

                $item->itens()->delete();
                $item->delete();
            });

            $descricaoLog = "Saída de " . $depositoSaida->nome . " para " . $depositoEntrada->nome;
            __createLog($item->empresa_id, 'Transferência de Estoque', 'excluir', $descricaoLog);

            session()->flash("flash_success", "Transferência removida com sucesso!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Transferência de Estoque', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('transferencia-estoque.index');
    }

    public function imprimir($id)
    {
        $item = TransferenciaEstoque::findOrFail($id);
        __validaObjetoEmpresa($item);

        $empresa = Empresa::findOrFail($item->empresa_id);

        $p = view('transferencia_estoque.print', compact('empresa', 'item'))
        ->with('title', 'Transferência de estoque');

        $domPdf = new Dompdf(["enable_remote" => true]);

        $domPdf->loadHtml($p);

        $domPdf->setPaper("A4");
        $domPdf->render();
        $domPdf->stream("Transferência de estoque $id.pdf", array("Attachment" => false));
    }

}
