<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Troca;
use App\Models\ItemTroca;
use App\Models\Nfce;
use App\Models\Nfe;
use App\Models\Funcionario;
use App\Models\CategoriaProduto;
use App\Models\Caixa;
use App\Models\Empresa;
use App\Models\ConfigGeral;
use App\Models\UsuarioEmpresa;
use App\Models\Cliente;
use App\Utils\EstoqueUtil;
use App\Services\TrocaSerialService;
use App\Models\ProdutoUnico;
use App\Models\CreditoCliente;
use Illuminate\Support\Facades\DB;
use NFePHP\DA\NFe\CupomNaoFiscal;
use Dompdf\Dompdf;

class TrocaController extends Controller
{
    protected $util;

    /** @var TrocaSerialService */
    protected $seriais;

    public function __construct(EstoqueUtil $util, TrocaSerialService $seriais)
    {
        $this->util = $util;
        $this->seriais = $seriais;

        $this->middleware('permission:troca_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:troca_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:troca_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request){
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $cliente_id = $request->get('cliente_id');

        $data = Troca::with(['nfce.cliente', 'nfe.cliente'])
        ->where('trocas.empresa_id', $request->empresa_id)
        ->select('trocas.*')
        ->leftJoin('nfces', 'nfces.id', '=', 'trocas.nfce_id')
        ->leftJoin('nves', 'nves.id', '=', 'trocas.nfe_id')
        ->when($start_date, fn($q) => $q->whereDate('trocas.created_at', '>=', $start_date))
        ->when($end_date, fn($q) => $q->whereDate('trocas.created_at', '<=', $end_date))
        ->when($cliente_id, function ($q) use ($cliente_id) {
            return $q->where(function ($q) use ($cliente_id) {
                $q->where('nfces.cliente_id', $cliente_id)
                ->orWhere('nves.cliente_id', $cliente_id);
            });
        })
        ->orderBy('trocas.created_at', 'desc')
        ->paginate(__itensPagina());

        return view('trocas.index', compact('data'));
    }

    public function create(Request $request){
        $tipo = $request->tipo;
        $id = $request->id;
        $modalidade = $request->get('modalidade', \App\Models\Troca::MODALIDADE_TROCA);
        if (!in_array($modalidade, [\App\Models\Troca::MODALIDADE_TROCA, \App\Models\Troca::MODALIDADE_DEVOLUCAO_PDV], true)) {
            $modalidade = \App\Models\Troca::MODALIDADE_TROCA;
        }

        if($tipo == 'nfce'){
            $item = Nfce::findOrFail($id);
        }else{
            $item = Nfe::findOrFail($id);
        }

        if($item == null){
            session()->flash("flash_error", "Nenhuma venda encontrada!");
            return redirect()->back();
        }

        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }
        __validaObjetoEmpresa($item);

        if ($modalidade === \App\Models\Troca::MODALIDADE_DEVOLUCAO_PDV) {
            $fimPrazo = \Carbon\Carbon::parse($item->created_at)->addHours(24);
            if (now()->gt($fimPrazo)) {
                session()->flash('flash_error', 'Prazo de 24 horas para devolução desta venda já expirou.');
                return redirect()->route('trocas.index');
            }
        }

        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)->get();
        $cliente = $item->cliente;
        $funcionario = $item->funcionario;
        $caixa = __isCaixaAberto();
        $abertura = Caixa::where('usuario_id', get_id_user())
        ->where('status', 1)
        ->first();

        $isVendaSuspensa = 0;
        $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)->where('status', 1)
        ->where('categoria_id', null)
        ->get();
        $config = ConfigGeral::where('empresa_id', request()->empresa_id)->first();

        $tiposPagamento = Nfce::tiposPagamento();

        // dd($tiposPagamento);
        if($config != null){
            $config->tipos_pagamento_pdv = $config != null && $config->tipos_pagamento_pdv ? json_decode($config->tipos_pagamento_pdv) : [];
            $temp = [];
            if(sizeof($config->tipos_pagamento_pdv) > 0){
                foreach($tiposPagamento as $key => $t){
                    if(in_array($t, $config->tipos_pagamento_pdv)){
                        $temp[$key] = $t;
                    }
                }
                $tiposPagamento = $temp;
            }
        }
        $tiposPagamento['00'] = 'Vale Crédito';

        $msgTroca = "";
        if(sizeof($item->troca) > 0){
            $msgTroca = "Essa venda já possui troca!";
        }

        return view('trocas.create', compact('item', 'funcionarios', 'cliente', 'funcionario', 'caixa', 'abertura', 
            'isVendaSuspensa', 'categorias', 'tiposPagamento', 'msgTroca', 'config', 'modalidade'));
    }

    public function show($id)
    {
        $item = Troca::findOrFail($id);
        return view('trocas.show', compact('item'));
    }

    public function destroy($id)
    {
        $item = Troca::with(['itens.produto', 'nfce', 'nfe'])->findOrFail($id);
        __validaObjetoEmpresa($item);
        $descricaoLog = "#$item->numero_sequencial - R$ " . __moeda($item->valor_troca);
        try {
            DB::transaction(function () use ($item) {
                $venda = $item->nfce ?? $item->nfe;
                if (!$venda) {
                    throw new \Exception('Venda vinculada à troca não encontrada.');
                }
                $localId = (int) $venda->local_id;
                $isNfce = (bool) $item->nfce_id;
                $nfceId = $isNfce ? $venda->id : null;
                $nfeId = $isNfce ? null : $venda->id;

                foreach ($item->itens as $it) {
                    $p = $it->produto;
                    if (!$p->gerenciar_estoque) {
                        continue;
                    }
                    if ($p->tipo_unico && $it->serial_codigo) {
                        $q = ProdutoUnico::query()
                            ->where('tipo', 'saida')
                            ->where('produto_id', $p->id)
                            ->where('codigo', $it->serial_codigo);
                        if ($isNfce) {
                            $q->where('nfce_id', $venda->id);
                        } else {
                            $q->where('nfe_id', $venda->id);
                        }
                        $saida = $q->lockForUpdate()->first();
                        if ($saida) {
                            $this->seriais->restaurarUmaSaidaSerial($saida, $localId);
                        }
                    }
                    $this->util->incrementaEstoque($p->id, $it->quantidade, null, $localId);
                }

                foreach ($item->seriais_devolvidos ?? [] as $entry) {
                    if (empty($entry['produto_id']) || empty($entry['codigo'])) {
                        continue;
                    }
                    $this->seriais->expedirSerialComoVendido(
                        (int) $entry['produto_id'],
                        (string) $entry['codigo'],
                        $localId,
                        $nfceId,
                        $nfeId
                    );
                }

                $venda->load('itens.produto');
                foreach ($venda->itens as $linha) {
                    if ($linha->produto->gerenciar_estoque) {
                        $this->util->reduzEstoque($linha->produto_id, $linha->quantidade, null, $localId);
                    }
                }

                $venda->total = $item->valor_original;
                $venda->save();

                foreach (CreditoCliente::where('troca_id', $item->id)->get() as $cred) {
                    if ($cred->cliente_id) {
                        $cliente = Cliente::lockForUpdate()->find($cred->cliente_id);
                        if ($cliente) {
                            $cliente->valor_credito = max(0, (float) $cliente->valor_credito - (float) $cred->valor);
                            $cliente->save();
                        }
                    }
                    $cred->delete();
                }

                $item->itens()->delete();
                $item->delete();
            });

            __createLog(request()->empresa_id, 'PDV Troca', 'excluir', $descricaoLog);

            session()->flash("flash_success", "Removido com sucesso!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'PDV Troca', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function imprimir($id)
    {

        $item = Troca::with([
            'itens.produto',
            'nfce.itens.produto',
            'nfe.itens.produto',
        ])->findOrFail($id);
        __validaObjetoEmpresa($item);

        $config = Empresa::where('id', $item->empresa_id)
        ->first();

        $localEmissao = $item->nfce->local_id ?? $item->nfe->local_id ?? null;
        $config = __objetoParaEmissao($config, $localEmissao);
        
        $usuario = UsuarioEmpresa::find(get_id_user());

        $logo = null;
        if($config->logo && file_exists(public_path('/uploads/logos/') . $config->logo)){
            $logo = public_path('/uploads/logos/') . $config->logo;
        }

        $configGeral = ConfigGeral::where('empresa_id', $item->empresa_id)->first();

        $p = view('trocas.cupom_nao_fiscal', compact('config', 'item', 'configGeral'));

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);
        $pdf = ob_get_clean();

        $domPdf->setPaper('a4', 'portrait');
        $pdf = $domPdf->render();

        $domPdf->stream("Doc. Troca $item->numero_sequencial.pdf", array("Attachment" => false));
    }

}
