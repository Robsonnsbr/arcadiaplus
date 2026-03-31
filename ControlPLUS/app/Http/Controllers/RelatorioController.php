<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProduto;
use Illuminate\Http\Request;
use App\Models\TipoDespesaFrete;
use App\Models\DespesaFrete;
use App\Models\Produto;
use App\Models\Cliente;
use App\Models\Reserva;
use App\Models\ComissaoVenda;
use App\Models\ContaPagar;
use App\Models\ContaReceber;
use App\Models\Fornecedor;
use App\Models\Acomodacao;
use App\Models\ItemNfe;
use App\Models\Empresa;
use App\Models\ItemNfce;
use App\Models\Nfe;
use App\Models\Nfce;
use App\Models\Cte;
use App\Models\Mdfe;
use App\Models\Funcionario;
use App\Models\OrdemServico;
use App\Models\Localizacao;
use App\Models\Deposito;
use App\Models\Marca;
use App\Models\TaxaPagamento;
use App\Models\Estoque;
use App\Models\MovimentacaoProduto;
use Dompdf\Dompdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RelatorioClientesExport;
use App\Exports\RelatorioFornecedoresExport;
use App\Exports\RelatorioNfeExport;
use App\Exports\RelatorioNfceExport;
use App\Exports\RelatorioCteExport;
use App\Exports\RelatorioMdfeExport;
use App\Exports\RelatorioContaPagarExport;
use App\Exports\RelatorioContaReceberExport;
use App\Exports\RelatorioPedidosFaturadosExport;
use App\Exports\RelatorioComissaoExport;
use App\Exports\RelatorioComprasExport;
use App\Exports\RelatorioDespesaFretesExport;
use App\Exports\RelatorioTotalizaProdutosExport;
use App\Exports\RelatorioVendasPorVendedorExport;
use App\Exports\RelatorioOrdemServicoExport;
use App\Exports\RelatorioProdutosExport;
use App\Exports\RelatorioLucroExport;
use App\Exports\RelatorioInventarioCustoMedioExport;
use App\Exports\RelatorioCurvaAbcClientesExport;
use App\Exports\RelatorioEntregaProdutosExport;
use App\Exports\RelatorioReservasExport;
use App\Exports\RelatorioLucroProdutoExport;
use App\Exports\RelatorioTiposPagamentoExport;
use App\Exports\RelatorioInventarioExport;
use App\Exports\RelatorioTaxasExport;
use App\Exports\RelatorioVendaProdutosExport;
use App\Exports\RelatorioMovimentacaoExport;
use App\Exports\RelatorioRegistroInventarioExport;
use App\Exports\RelatorioEstoqueExport;
use App\Exports\RelatorioVendasExport;
use Illuminate\Support\Facades\DB;

class RelatorioController extends Controller
{
    public function index()
    {
        $marcas = Marca::where('empresa_id', request()->empresa_id)->get();
        $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)
        // ->where('categoria_id', null)
        ->where('status', 1)->get();
        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)->get();
        $tiposDespesaFrete = TipoDespesaFrete::where('empresa_id', request()->empresa_id)->get();
        $depositosRelatorioSelect = $this->depositosRelatorioSelectOptions();

        return view('relatorios.index', compact('funcionarios', 'marcas', 'categorias', 'tiposDespesaFrete', 'depositosRelatorioSelect'));
    }

    private function relatorioLocalIds(): array
    {
        return __getLocaisAtivoUsuario()
            ->pluck('id')
            ->map(function ($id) {
                return (int)$id;
            })
            ->all();
    }

    private function depositosRelatorioSelectOptions(): array
    {
        $localIds = $this->relatorioLocalIds();
        $options = ['' => 'Todos'];

        if (empty($localIds)) {
            return $options;
        }

        $depositos = Deposito::with('localizacao:id,descricao')
            ->where('empresa_id', request()->empresa_id)
            ->where('ativo', 1)
            ->whereIn('local_id', $localIds)
            ->orderBy('nome')
            ->get();

        foreach ($depositos as $deposito) {
            $label = $deposito->nome;
            if ($deposito->localizacao && $deposito->localizacao->descricao) {
                $label .= ' (' . $deposito->localizacao->descricao . ')';
            }

            $options[$deposito->id] = $label;
        }

        return $options;
    }

    private function resolveRelatorioEstoqueContext(Request $request): array
    {
        $localIds = $this->relatorioLocalIds();
        $localId = $request->filled('local_id') ? (int)$request->local_id : null;
        $depositoId = $request->filled('deposito_id') ? (int)$request->deposito_id : null;
        $deposito = null;

        if ($depositoId) {
            $deposito = Deposito::with('localizacao:id,descricao')
                ->where('empresa_id', $request->empresa_id)
                ->where('ativo', 1)
                ->whereIn('local_id', $localIds)
                ->whereKey($depositoId)
                ->firstOrFail();

            $localId = (int)$deposito->local_id;
        } elseif ($localId && !in_array($localId, $localIds, true)) {
            abort(404);
        }

        return [
            'local_ids' => $localIds,
            'local_id' => $localId,
            'deposito_id' => $depositoId,
            'deposito' => $deposito,
        ];
    }

    private function applyRelatorioEstoqueContextToEstoqueQuery($query, ?int $depositoId, array $localIds, string $table = 'estoques')
    {
        return $query
            ->when($depositoId, function ($subQuery) use ($depositoId, $table) {
                return $subQuery->where($table . '.deposito_id', $depositoId);
            })
            ->when(!$depositoId, function ($subQuery) use ($localIds, $table) {
                return $subQuery->whereIn($table . '.local_id', $localIds);
            });
    }

    private function applyRelatorioEstoqueContextToProdutoQuery($query, ?int $depositoId, array $localIds)
    {
        return $query->whereExists(function ($sub) use ($depositoId, $localIds) {
            $sub->selectRaw('1')
                ->from('estoques')
                ->whereColumn('estoques.produto_id', 'produtos.id');

            $this->applyRelatorioEstoqueContextToEstoqueQuery($sub, $depositoId, $localIds);
        });
    }

    private function estoqueQuantidadePorProdutoMap(?int $depositoId, array $localIds): array
    {
        return Estoque::select('produto_id', DB::raw('SUM(quantidade) as quantidade_total'))
            ->when($depositoId, function ($query) use ($depositoId) {
                return $query->where('deposito_id', $depositoId);
            })
            ->when(!$depositoId, function ($query) use ($localIds) {
                return $query->whereIn('local_id', $localIds);
            })
            ->groupBy('produto_id')
            ->pluck('quantidade_total', 'produto_id')
            ->map(function ($quantidade) {
                return (float)$quantidade;
            })
            ->all();
    }

    private function estoqueQuantidadePorVariacaoMap(?int $depositoId, array $localIds): array
    {
        return Estoque::select(
            'produto_id',
            'produto_variacao_id',
            DB::raw('SUM(quantidade) as quantidade_total')
        )
            ->whereNotNull('produto_variacao_id')
            ->when($depositoId, function ($query) use ($depositoId) {
                return $query->where('deposito_id', $depositoId);
            })
            ->when(!$depositoId, function ($query) use ($localIds) {
                return $query->whereIn('local_id', $localIds);
            })
            ->groupBy('produto_id', 'produto_variacao_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->produto_id . ':' . $item->produto_variacao_id => (float)$item->quantidade_total];
            })
            ->all();
    }

    private function quantidadeVariacaoRelatorio(array $quantidadePorVariacao, int $produtoId, int $variacaoId): float
    {
        return (float)($quantidadePorVariacao[$produtoId . ':' . $variacaoId] ?? 0);
    }

    public function produtos(Request $request)
    {
        // dd($request);
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);
        $estoque = $request->estoque;
        $tipo = $request->tipo;
        $marca_id = $request->marca_id;
        $categoria_id = $request->categoria_id;
        $local_id = $request->local_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $esportar_excel = $request->esportar_excel;

        $data = Produto::select('produtos.*')
        ->where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('produtos.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('produtos.created_at', '<=', $end_date);
        })
        ->when($estoque != '', function ($query) use ($estoque) {
            if ($estoque == 1) {
                return $query->join('estoques', 'estoques.produto_id', '=', 'produtos.id')
                ->where('estoques.quantidade', '>', 0);
            } elseif($estoque == -1) {
                // return $query->leftJoin('estoques', 'estoques.produto_id', '=', 'produtos.id')
                // ->whereNull('estoques.produto_id')
                // ->orWhere(function ($q) use ($query) {
                //     return $q->join('estoques', 'estoques.produto_id', '=', 'produtos.id')
                //     ->where('estoques.quantidade', '=', 0);
                // });
                return $query->leftJoin('estoques', 'estoques.produto_id', '=', 'produtos.id')
                ->where(function ($q) {
                   $q->whereNull('estoques.id')
                   ->orWhere('estoques.quantidade', '=', 0);
               });
            }else{
                return $query->join('estoques', 'estoques.produto_id', '=', 'produtos.id')
                ->whereColumn('estoques.quantidade', '<', 'produtos.estoque_minimo')
                ->where('produtos.estoque_minimo', '>', 0);
            }
        })
        ->when(!empty($categoria_id), function ($query) use ($categoria_id) {
            return $query->where(function($t) use ($categoria_id) 
            {
                $t->where('categoria_id', $categoria_id)->orWhere('sub_categoria_id', $categoria_id);
            });
        })
        ->when(!empty($marca_id), function ($query) use ($marca_id) {
            return $query->where('marca_id', $marca_id);
        })
        ->when(!empty($local_id), function ($query) use ($local_id) {
            return $query->whereExists(function ($sub) use ($local_id) {
                $sub->selectRaw('1')
                ->from('estoques')
                ->whereColumn('estoques.produto_id', 'produtos.id')
                ->where('estoques.local_id', $local_id);
            });
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereExists(function ($sub) use ($locais) {
                $sub->selectRaw('1')
                ->from('estoques')
                ->whereColumn('estoques.produto_id', 'produtos.id')
                ->whereIn('estoques.local_id', $locais);
            });
        })
        ->get();

        if ($tipo != '') {
            if ($tipo ==1 || $tipo == -1) {
                foreach ($data as $item) {
                    $sumNfe = ItemNfe::where('produto_id', $item->id)
                    ->join('nves', 'nves.id', '=', 'item_nves.nfe_id')
                    ->where('nves.tpNF', 1)
                    ->sum('quantidade');

                    $sumNfce = ItemNfce::where('produto_id', $item->id)
                    ->sum('quantidade');

                    $item->quantidade_vendida = $sumNfe + $sumNfce;
                }
            }else{
                foreach ($data as $item) {
                    $sumNfe = ItemNfe::where('produto_id', $item->id)
                    ->select('item_nves.*')
                    ->join('nves', 'nves.id', '=', 'item_nves.nfe_id')
                    ->where('nves.tpNF', 0)
                    ->sum('quantidade');

                    $item->quantidade_vendida = $sumNfe;
                }
            }

            $data = $data->filter(function ($item) {
                return $item->quantidade_vendida > 0;
            });

            if ($tipo ==1 || $tipo == -1) {
                if ($tipo == 1) {
                    $data = $data->sortByDesc('quantidade_vendida');
                } else {
                    $data = $data->sortBy('quantidade_vendida');
                }
            }else{
                if ($tipo == 2) {
                    $data = $data->sortByDesc('quantidade_vendida');
                } else {
                    $data = $data->sortBy('quantidade_vendida');
                }
            }
        }

        $marca = null;
        if($marca_id != null){
            $marca = Marca::findOrFail($marca_id);
        }

        $categoria = null;
        if($categoria_id != null){
            $categoria = CategoriaProduto::findOrFail($categoria_id);
        }

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioProdutosExport($data, $tipo, $marca, $categoria);
            return Excel::download($relatorioEx, 'relatorio_produtos.xlsx');
        }

        $p = view('relatorios/produtos', compact('data', 'tipo', 'marca', 'categoria'))
        ->with('title', 'Relatório de Produtos');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);

        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Produtos.pdf", array("Attachment" => false));
    }

    public function clientes(Request $request)
    {
        $tipo = $request->tipo;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $esportar_excel = $request->esportar_excel;

        $data = Cliente::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })->get();

        if ($tipo != '') {
            foreach ($data as $item) {
                $sumNfe = Nfe::where('cliente_id', $item->id)
                ->sum('total');

                $sumNfce = Nfce::where('cliente_id', $item->id)
                ->sum('total');

                $item->total = $sumNfe + $sumNfce;
            }

            if ($tipo == 1) {
                $data = $data->sortByDesc('total');
            } else {
                $data = $data->sortBy('total');
            }
        }

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioClientesExport($data, $tipo);
            return Excel::download($relatorioEx, 'relatorio_clientes.xlsx');
        }

        $p = view('relatorios/clientes', compact('data', 'tipo'))
        ->with('title', 'Relatório de Clientes');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Clientes.pdf", array("Attachment" => false));
    }

    public function fornecedores(Request $request)
    {
        $tipo = $request->tipo;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $esportar_excel = $request->esportar_excel;

        $data = Fornecedor::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })->get();

        if ($tipo != '') {
            foreach ($data as $item) {
                $sumNfe = Nfe::where('fornecedor_id', $item->id)
                ->where('tpNF', 0)
                ->sum('total');

                $item->total = $sumNfe;
            }

            if ($tipo == 1) {
                $data = $data->sortByDesc('total');
            } else {
                $data = $data->sortBy('total');
            }
        }

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioFornecedoresExport($data, $tipo);
            return Excel::download($relatorioEx, 'relatorio_fornecedores.xlsx');
        }

        $p = view('relatorios/fornecedores', compact('data', 'tipo'))
        ->with('title', 'Relatório de Fornecedores');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Fornecedores.pdf", array("Attachment" => false));
    }

    public function nfe(Request $request)
    {

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $tipo = $request->tipo;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $finNFe = $request->finNFe;
        $cliente = $request->cliente;
        $estado = $request->estado;
        $local_id = $request->local_id;
        $esportar_excel = $request->esportar_excel;

        $data = Nfe::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('data_emissao', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('data_emissao', '<=', $end_date);
        })
        ->when(!empty($cliente), function ($query) use ($cliente) {
            return $query->where('cliente_id', $cliente);
        })
        ->when(!empty($estado), function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->when(!empty($tipo), function ($query) use ($tipo) {
            return $query->where('tpNF', $tipo);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->where('orcamento', 0)
        ->when(!empty($finNFe), function ($query) use ($finNFe) {
            return $query->where('finNFe', $finNFe);
        })->get();

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioNfeExport($data);
            return Excel::download($relatorioEx, 'relatorio_nfe.xlsx');
        }

        $p = view('relatorios/nfe', compact('data'))
        ->with('title', 'Relatório de NFe');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de NFe.pdf", array("Attachment" => false));
    }

    public function nfce(Request $request)
    {
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $cliente_id = $request->cliente;
        $estado = $request->estado;
        $local_id = $request->local_id;
        $esportar_excel = $request->esportar_excel;

        $data = Nfce::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('data_emissao', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('data_emissao', '<=', $end_date);
        })
        ->when(!empty($estado), function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })->get();

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioNfceExport($data);
            return Excel::download($relatorioEx, 'relatorio_nfce.xlsx');
        }

        $p = view('relatorios/nfce', compact('data'))
        ->with('title', 'Relatório de NFCe');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de NFCe.pdf", array("Attachment" => false));
    }

    public function cte(Request $request)
    {

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $estado = $request->estado;
        $local_id = $request->local_id;
        $esportar_excel = $request->esportar_excel;

        $data = Cte::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($estado), function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->get();

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioCteExport($data);
            return Excel::download($relatorioEx, 'relatorio_cte.xlsx');
        }

        $p = view('relatorios/cte', compact('data'))
        ->with('title', 'Relatório de CTe');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de CTe.pdf", array("Attachment" => false));
    }

    public function mdfe(Request $request)
    {
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $estado = $request->estado;
        $local_id = $request->local_id;
        $esportar_excel = $request->esportar_excel;

        $data = Mdfe::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($estado), function ($query) use ($estado) {
            return $query->where('estado_emissao', $estado);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->get();

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioMdfeExport($data);
            return Excel::download($relatorioEx, 'relatorio_mdfe.xlsx');
        }


        $p = view('relatorios/mdfe', compact('data'))
        ->with('title', 'Relatório de MDFe');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de MDFe.pdf", array("Attachment" => false));
    }

    public function conta_pagar(Request $request)
    {
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $status = $request->status;
        $local_id = $request->local_id;
        $fornecedor_id = $request->fornecedor_id;
        $esportar_excel = $request->esportar_excel;

        $data = ContaPagar::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('data_vencimento', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('data_vencimento', '<=', $end_date);
        })
        ->when(!empty($status), function ($query) use ($status) {
            if ($status == -1) {
                return $query->where('status', '!=', 1);
            } else {
                return $query->where('status', $status);
            }
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->when($fornecedor_id, function ($query) use ($fornecedor_id) {
            return $query->where('fornecedor_id', $fornecedor_id);
        })
        ->orderBy('data_vencimento')
        ->get();

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioContaPagarExport($data);
            return Excel::download($relatorioEx, 'relatorio_contas_pagar.xlsx');
        }

        $p = view('relatorios/conta_pagar', compact('data'))
        ->with('title', 'Relatório de Contas a Pagar');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Contas a Pagar.pdf", array("Attachment" => false));
    }

    public function conta_receber(Request $request)
    {
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $status = $request->status;
        $local_id = $request->local_id;
        $cliente_id = $request->cliente;
        $esportar_excel = $request->esportar_excel;

        $data = ContaReceber::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('data_vencimento', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('data_vencimento', '<=', $end_date);
        })
        ->when(!empty($status), function ($query) use ($status) {
            if ($status == -1) {
                return $query->where('status', '!=', 1);
            } else {
                return $query->where('status', $status);
            }
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->when($cliente_id, function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->orderBy('data_vencimento')
        ->get();

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioContaReceberExport($data);
            return Excel::download($relatorioEx, 'relatorio_contas_receber.xlsx');
        }

        $p = view('relatorios/conta_receber', compact('data'))
        ->with('title', 'Relatório de Contas a Receber');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Contas a Receber.pdf", array("Attachment" => false));
    }

    public function pedidosFaturados(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $status = $request->status;
        $cliente_id = $request->cliente;
        $esportar_excel = $request->esportar_excel;

        $data = DB::table('conta_recebers as cr')
        ->join('clientes as c', 'c.id', '=', 'cr.cliente_id')
        ->leftJoin('cidades as ci', 'ci.id', '=', 'c.cidade_id')
        ->leftJoin('nves as nv', 'nv.id', '=', 'cr.nfe_id')
        ->where('cr.empresa_id', $request->empresa_id)
        ->whereNotNull('cr.cliente_id')
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('cr.data_vencimento', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('cr.data_vencimento', '<=', $end_date);
        })
        ->when($cliente_id, function ($query) use ($cliente_id) {
            return $query->where('cr.cliente_id', $cliente_id);
        })
        ->when(!empty($status), function ($query) use ($status) {
            if ($status == -1) {
                return $query->whereRaw('COALESCE(cr.valor_recebido, 0) < cr.valor_integral');
            }
            return $query->whereRaw('COALESCE(cr.valor_recebido, 0) >= cr.valor_integral');
        })
        ->select([
            'cr.id as codigo',
            'c.razao_social as cliente',
            DB::raw("COALESCE(ci.nome, '') as cidade"),
            DB::raw("COALESCE(ci.uf, '') as estado"),
            'nv.id as numero_nfe',
            'nv.created_at as data_venda',
            'cr.valor_integral as valor_previsto',
            DB::raw('COALESCE(cr.valor_recebido, 0) as valor_recebido'),
            DB::raw('GREATEST(cr.valor_integral - COALESCE(cr.valor_recebido, 0), 0) as valor_a_receber'),
            'cr.data_vencimento',
            'cr.data_recebimento as data_pagamento',
            DB::raw("CASE WHEN COALESCE(cr.valor_recebido, 0) >= cr.valor_integral THEN 'Sim' ELSE 'Não' END as quitado"),
        ])
        ->orderBy('cr.data_vencimento', 'desc')
        ->orderBy('cr.id', 'desc')
        ->get();

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioPedidosFaturadosExport($data, $start_date, $end_date, $status);
            return Excel::download($relatorioEx, 'relatorio_pedidos_faturados.xlsx');
        }

        $p = view('relatorios/pedidos_faturados', compact('data', 'start_date', 'end_date', 'status'))
        ->with('title', 'Relatório de Pedidos Faturados (Contas a Receber)');

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Pedidos Faturados.pdf", array("Attachment" => false));
    }

    public function comissao(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $funcionario_id = $request->funcionario_id;
        $esportar_excel = $request->esportar_excel;

        $data = ComissaoVenda::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($funcionario_id), function ($query) use ($funcionario_id) {
            return $query->where('funcionario_id', $funcionario_id);
        })
        ->get();

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioComissaoExport($data);
            return Excel::download($relatorioEx, 'relatorio_comissao.xlsx');
        }

        $p = view('relatorios/comissao', compact('data'))
        ->with('title', 'Relatório de Comissao');

        // if ($funcionario_id == null) {
        //     session()->flash('flash_error', 'Selecione um funcionário para continuar');
        //     return redirect()->back();
        // }

        $p = view('relatorios/comissao', compact('data'))
        ->with('funcionário', $funcionario_id)
        ->with('title', 'Relatório de Comissão');

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Comissão.pdf", array("Attachment" => false));
    }

    public function vendas(Request $request)
    {
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $tipo = $request->tipo;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $local_id = $request->local_id;
        $cliente_id = $request->cliente;
        $funcionario_id = $request->funcionario_id;
        $start_time = $request->start_time;
        $end_time = $request->end_time;
        $estado = $request->estado;
        $esportar_excel = $request->esportar_excel;

        if($start_date){
            if($start_time){
                $start_date .= " $start_time:59";
            }else{
                $start_date .= " 00:00:00";
            }
        }

        if($end_date){
            if($end_time){
                $end_date .= " $end_time:59";
            }else{
                $end_date .= " 23:59:59";
            }
        }
        // dd($start_date);


        $vendas = Nfe::where('empresa_id', $request->empresa_id)->where('tpNF', 1)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->where('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->where('created_at', '<=', $end_date);
        })
        // ->where('nves.estado', '!=', 'cancelado')
        ->when(!empty($estado), function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->when(empty($estado), function ($query) use ($estado) {
            return $query->where('estado', '!=', 'cancelado');
        })
        ->limit($total_resultados ?? 1000000)
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->when($cliente_id, function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->when($funcionario_id, function ($query) use ($funcionario_id) {
            return $query->where('funcionario_id', $funcionario_id);
        })
        ->with(['cliente', 'localizacao', 'funcionario'])
        ->get();

        $vendasCaixa = Nfce::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->where('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->where('created_at', '<=', $end_date);
        })

        ->where('nfces.empresa_id', $request->empresa_id)
        ->when(!empty($estado), function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->when(empty($estado), function ($query) use ($estado) {
            return $query->where('estado', '!=', 'cancelado');
        })
        ->limit($total_resultados ?? 1000000)
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->when($cliente_id, function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->when($funcionario_id, function ($query) use ($funcionario_id) {
            return $query->where('funcionario_id', $funcionario_id);
        })
        ->with(['cliente', 'localizacao', 'funcionario'])
        ->get();

        // echo (sizeof($vendas)+sizeof($vendasCaixa));
        // die;

        $data = $this->uneArrayVendas($vendas, $vendasCaixa);

        usort($data, function($a, $b){
            return $a['data'] > $b['data'] ? 1 : -1;
        });

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioVendasExport($data);
            return Excel::download($relatorioEx, 'relatorio_vendas.xlsx');
        }

        // dd($data);
        $p = view('relatorios/vendas', compact('data', 'tipo'))
        ->with('title', 'Relatório de Vendas');
        // return $p;
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Vendas.pdf", array("Attachment" => false));
    }

    private function uneArrayVendas($vendas, $vendasCaixa)
    {
        $adicionados = [];
        $arr = [];
        foreach ($vendas as $v) {
            $temp = [
                'id' => $v->numero_sequencial,
                'data' => $v->created_at,
                'tipo' => 'Pedido',
                'total' => $v->total,
                'cliente' => $v->cliente ? $v->cliente->info : '--',
                'vendedor' => $v->funcionario ? $v->funcionario->nome : '--',
                'localizacao' => $v->localizacao
                // 'itens' => $v->itens
            ];
            array_push($adicionados, $v->id);
            array_push($arr, $temp);
        }
        foreach ($vendasCaixa as $v) {
            $temp = [
                'id' => $v->numero_sequencial,
                'data' => $v->created_at,
                'tipo' => 'PDV',
                'total' => $v->total,
                'cliente' => $v->cliente ? $v->cliente->info : '--',
                'vendedor' => $v->funcionario ? $v->funcionario->nome : '--',
                'localizacao' => $v->localizacao
                // 'itens' => $v->itens
            ];
            array_push($adicionados, $v->id);
            array_push($arr, $temp);
        }
        return $arr;
    }

    public function despesaFrete(Request $request)
    {

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $tipo_despesa_frete_id = $request->tipo_despesa_frete_id;
        $esportar_excel = $request->esportar_excel;

        $data = DespesaFrete::
        select('despesa_fretes.*')
        ->join('fretes', 'fretes.id', '=', 'despesa_fretes.frete_id')
        ->where('fretes.empresa_id', request()->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('despesa_fretes.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('despesa_fretes.created_at', '<=', $end_date);
        })
        ->when($tipo_despesa_frete_id, function ($query) use ($tipo_despesa_frete_id) {
            return $query->where('despesa_fretes.tipo_despesa_id', $tipo_despesa_frete_id);
        })
        ->get();

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioDespesaFretesExport($data);
            return Excel::download($relatorioEx, 'relatorio_despesa_fretes.xlsx');
        }

        $p = view('relatorios/despesa_fretes', compact('data'))
        ->with('title', 'Relatório de Despesas de Frete');
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Despesas de Frete.pdf", array("Attachment" => false));
    }

    public function compras(Request $request)
    {

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $local_id = $request->local_id;
        $esportar_excel = $request->esportar_excel;

        $data = Nfe::where('empresa_id', request()->empresa_id)->where('tpNF', 0)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->where('nves.empresa_id', $request->empresa_id)
        ->limit($total_resultados ?? 1000000)
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->get();

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioComprasExport($data);
            return Excel::download($relatorioEx, 'relatorio_compras.xlsx');
        }

        $p = view('relatorios/compras', compact('data'))
        ->with('title', 'Relatório de Compras');
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Compras.pdf", array("Attachment" => false));
    }

    public function taxas(Request $request)
    {
        $data_inicial = $request->data_inicial;
        $data_final = $request->data_final;
        $local_id = $request->local_id;
        $esportar_excel = $request->esportar_excel;
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);
        
        if ($data_final && $data_final) {
            $data_inicial = $this->parseDate($data_inicial);
            $data_final = $this->parseDate($data_final);
        }
        $taxas = TaxaPagamento::where('empresa_id', request()->empresa_id)->get();
        $tipos = $taxas->pluck('tipo_pagamento')->toArray();
        $vendas = Nfe::where('empresa_id', request()->empresa_id)
        ->when($data_inicial != '', function ($q) use ($data_inicial) {
            return $q->whereDate('created_at', '>=', $data_inicial);
        })
        ->when($data_final != '', function ($q) use ($data_final) {
            return $q->whereDate('created_at', '<=', $data_final);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->get();

        $data = [];
        foreach ($vendas as $v) {
            $bandeira_cartao = $v->bandeira_cartao;
            if (sizeof($v->fatura) > 1) {
                foreach ($v->fatura as $ft) {
                    $fp = $ft->tipo_pagamento;
                    if (in_array($fp, $tipos)) {
                        $taxa = TaxaPagamento::where('empresa_id', request()->empresa_id)
                        ->where('tipo_pagamento', $fp)
                        ->when($bandeira_cartao != '' && $bandeira_cartao != '99', function ($q) use ($bandeira_cartao) {
                            return $q->where('bandeira_cartao', $bandeira_cartao);
                        })
                        ->first();
                        if ($taxa != null) {
                            $item = [
                                'cliente' => $v->cliente ? ($v->cliente->razao_social . " " . $v->cliente->cpf_cnpj) :
                                'Consumidor final',
                                'total' => $ft->valor,
                                'taxa_perc' => $taxa ? $taxa->taxa : 0,
                                'taxa' => $taxa ? ($ft->valor * ($taxa->taxa / 100)) : 0,
                                'data' => \Carbon\Carbon::parse($v->created_at)->format('d/m/Y H:i'),
                                'tipo_pagamento' => Nfe::getTipo($fp),
                                'venda_id' => $v->id,
                                'tipo' => 'PEDIDO'
                            ];
                            array_push($data, $item);
                        }
                    }
                }
            } else {
                if (in_array($v->tipo_pagamento, $tipos)) {
                    $total = $v->valor_total - $v->desconto + $v->acrescimo;
                    $taxa = TaxaPagamento::where('empresa_id', request()->empresa_id)
                    ->when($bandeira_cartao != '' && $bandeira_cartao != '99', function ($q) use ($bandeira_cartao) {
                        return $q->where('bandeira_cartao', $bandeira_cartao);
                    })
                    ->where('tipo_pagamento', $v->tipo_pagamento)->first();
                    if ($taxa != null) {
                        $item = [
                            'cliente' => $v->cliente ? ($v->cliente->razao_social . " " . $v->cliente->cpf_cnpj) :
                            'Consumidor final',
                            'total' => $v->total,
                            'taxa_perc' => $taxa->taxa,
                            'taxa' => $taxa ? ($total * ($taxa->taxa / 100)) : 0,
                            'data' => \Carbon\Carbon::parse($v->created_at)->format('d/m/Y H:i'),
                            'tipo_pagamento' => Nfe::getTipo($v->tipo_pagamento),
                            'venda_id' => $v->id,
                            'tipo' => 'PEDIDO'
                        ];
                        array_push($data, $item);
                    } else {
                        echo $bandeira_cartao;
                        die;
                    }
                }
            }
        }

        $vendasCaixa = Nfce::where('empresa_id', request()->empresa_id)
        ->when($data_inicial != '', function ($q) use ($data_inicial) {
            return $q->whereDate('created_at', '>=', $data_inicial);
        })
        ->when($data_final != '', function ($q) use ($data_final) {
            return $q->whereDate('created_at', '<=', $data_final);
        })
        ->get();

        foreach ($vendasCaixa as $v) {
            $bandeira_cartao = $v->bandeira_cartao;
            if (sizeof($v->fatura) > 1) {
                foreach ($v->fatura as $ft) {
                    if (in_array($ft->tipo_pagamento, $tipos)) {
                        $taxa = TaxaPagamento::where('empresa_id', request()->empresa_id)
                        ->when($bandeira_cartao != '' && $bandeira_cartao != '99', function ($q) use ($bandeira_cartao) {
                            return $q->where('bandeira_cartao', $bandeira_cartao);
                        })
                        ->where('tipo_pagamento', $ft->tipo_pagamento)->first();

                        if ($taxa != null) {
                            $item = [
                                'cliente' => $v->cliente ? ($v->cliente->razao_social . " " . $v->cliente->cpf_cnpj) :
                                'Consumidor final',
                                'total' => $ft->valor,
                                'taxa_perc' => $taxa->taxa,
                                'taxa' => $taxa ? ($ft->valor * ($taxa->taxa / 100)) : 0,
                                'data' => \Carbon\Carbon::parse($v->created_at)->format('d/m/Y H:i'),
                                'tipo_pagamento' => Nfe::getTipo($ft->tipo_pagamento),
                                'venda_id' => $v->id,
                                'tipo' => 'PDV'
                            ];
                            array_push($data, $item);
                        }
                    }
                }
            } else {
                if (in_array($v->tipo_pagamento, $tipos)) {
                    $taxa = TaxaPagamento::where('empresa_id', request()->empresa_id)
                    ->when($bandeira_cartao != '' && $bandeira_cartao != '99', function ($q) use ($bandeira_cartao) {
                        return $q->where('bandeira_cartao', $bandeira_cartao);
                    })
                    ->where('tipo_pagamento', $v->tipo_pagamento)->first();

                    if ($taxa != null) {
                        $item = [
                            'cliente' => $v->cliente ? ($v->cliente->razao_social . " " . $v->cliente->cpf_cnpj) :
                            'Consumidor final',
                            'total' => $v->total,
                            'taxa_perc' => $taxa->taxa,
                            'taxa' => $taxa ? ($v->total * ($taxa->taxa / 100)) : 0,
                            'data' => \Carbon\Carbon::parse($v->created_at)->format('d/m/Y H:i'),
                            'tipo_pagamento' => Nfe::getTipo($v->tipo_pagamento),
                            'venda_id' => $v->id,
                            'tipo' => 'PDV'
                        ];
                        array_push($data, $item);
                    }
                }
            }
        }

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioTaxasExport($data);
            return Excel::download($relatorioEx, 'relatorio_taxas.xlsx');
        }

        $p = view('relatorios/taxas')
        ->with('data', $data)
        ->with('title', 'Taxas de Pagamento');

        // return $p;
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Taxas de pagamento.pdf", array("Attachment" => false));
    }

    public function lucro(Request $request)
    {

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $local_id = $request->local_id;
        $esportar_excel = $request->esportar_excel;

        $nfe = Nfe::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->where('orcamento', 0)
        ->where('tpNF', 1)
        ->get();

        $nfce = Nfce::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->get();

        $data = [];

        foreach($nfe as $n){
            $item = [
                'cliente' => $n->cliente ? $n->cliente->info : 'CONSUMIDOR FINAL',
                'data' => __data_pt($n->created_at),
                'valor_venda' => $n->total,
                'valor_custo' => $this->calculaCusto($n->itens),
                'localizacao' => $n->localizacao
            ];
            array_push($data, $item);
        }

        foreach($nfce as $n){
            $item = [
                'cliente' => $n->cliente ? $n->cliente->info : 'CONSUMIDOR FINAL',
                'data' => __data_pt($n->created_at),
                'valor_venda' => $n->total,
                'valor_custo' => $this->calculaCusto($n->itens),
                'localizacao' => $n->localizacao
            ];
            array_push($data, $item);
        }

        usort($data, function($a, $b){
            return $a['data'] < $b['data'] ? 1 : -1;
        });

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioLucroExport($data);
            return Excel::download($relatorioEx, 'relatorio_lucro.xlsx');
        }

        $p = view('relatorios/lucro', compact('data'))
        ->with('title', 'Relatório de Lucros');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Lucros.pdf", array("Attachment" => false));
    }

    public function vendaProdutos(Request $request){
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $local_id = $request->local_id;
        $marca_id = $request->marca_id;
        $categoria_id = $request->categoria_id;
        $produto_id = $request->produto_id;
        $esportar_excel = $request->esportar_excel;

        $diferenca = strtotime($end_date) - strtotime($start_date);
        $dias = floor($diferenca / (60 * 60 * 24));

        $dataAtual = $start_date;
        if($dias <= 0){
            $dias = 1;
        }

        $data = [];
        for($aux = 0; $aux < $dias; $aux++){
            $itensNfe = ItemNfe::
            select(\DB::raw('sum(sub_total) as subtotal, sum(quantidade) as soma_quantidade, item_nves.produto_id as produto_id, avg(item_nves.valor_unitario) as media, item_nves.valor_unitario as valor_unitario'))
            ->whereBetween('item_nves.created_at', 
                [
                    $dataAtual . " 00:00:00",
                    $dataAtual . " 23:59:59"
                ]
            )
            ->join('produtos', 'produtos.id', '=', 'item_nves.produto_id')
            ->join('nves', 'nves.id', '=', 'item_nves.nfe_id')
            ->groupBy('item_nves.produto_id')
            ->where('produtos.empresa_id', $request->empresa_id)
            ->when(!empty($categoria_id), function ($query) use ($categoria_id) {
                return $query->join('categoria_produtos', 'categoria_produtos.id', '=', 'produtos.categoria_id')
                ->where(function($t) use ($categoria_id) 
                {
                    $t->where('produtos.categoria_id', $categoria_id)->orWhere('produtos.sub_categoria_id', $categoria_id);
                });
            })
            ->when(!empty($produto_id), function ($query) use ($produto_id) {
                return $query->where('item_nves.produto_id', $produto_id);
            })
            ->when(!empty($marca_id), function ($query) use ($marca_id) {
                return $query->join('marcas', 'marcas.id', '=', 'produtos.marca_id')
                ->where('produtos.marca_id', $marca_id);
            })
            ->when(!empty($local_id), function ($query) use ($local_id) {
                return $query->whereExists(function ($sub) use ($local_id) {
                    $sub->selectRaw('1')
                    ->from('estoques')
                    ->whereColumn('estoques.produto_id', 'produtos.id')
                    ->where('estoques.local_id', $local_id);
                });
            })
            ->get();

            $itensNfce = ItemNfce::
            select(\DB::raw('sum(sub_total) as subtotal, sum(quantidade) as soma_quantidade, item_nfces.produto_id as produto_id, avg(item_nfces.valor_unitario) as media, item_nfces.valor_unitario as valor_unitario'))
            ->whereBetween('item_nfces.created_at', 
                [
                    $dataAtual . " 00:00:00",
                    $dataAtual . " 23:59:59"
                ]
            )
            ->join('produtos', 'produtos.id', '=', 'item_nfces.produto_id')
            ->join('nfces', 'nfces.id', '=', 'item_nfces.nfce_id')
            ->groupBy('item_nfces.produto_id')
            ->where('produtos.empresa_id', $request->empresa_id)
            ->when(!empty($categoria_id), function ($query) use ($categoria_id) {
                // return $query->join('categoria_produtos', 'categoria_produtos.id', '=', 'produtos.categoria_id')
                // ->where('produtos.categoria_id', $categoria_id);
                return $query->join('categoria_produtos', 'categoria_produtos.id', '=', 'produtos.categoria_id')
                ->where(function($t) use ($categoria_id) 
                {
                    $t->where('produtos.categoria_id', $categoria_id)->orWhere('produtos.sub_categoria_id', $categoria_id);
                });
            })
            ->when(!empty($produto_id), function ($query) use ($produto_id) {
                return $query->where('item_nfces.produto_id', $produto_id);
            })
            ->when(!empty($marca_id), function ($query) use ($marca_id) {
                return $query->join('marcas', 'marcas.id', '=', 'produtos.marca_id')
                ->where('produtos.marca_id', $marca_id);
            })
            ->when(!empty($local_id), function ($query) use ($local_id) {
                return $query->whereExists(function ($sub) use ($local_id) {
                    $sub->selectRaw('1')
                    ->from('estoques')
                    ->whereColumn('estoques.produto_id', 'produtos.id')
                    ->where('estoques.local_id', $local_id);
                });
            })
            ->get();

            $itens = $this->uneArrayItens($itensNfe, $itensNfce, $request->ordem);
            $temp = [
                'data' => $dataAtual,
                'itens' => $itens,
            ];
            array_push($data, $temp);
            $dataAtual = date('Y-m-d', strtotime($dataAtual. '+1day'));
        }

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioVendaProdutosExport($data, $start_date, $end_date);
            return Excel::download($relatorioEx, 'relatorio_venda_produtos.xlsx');
        }

        $p = view('relatorios/venda_por_produtos', compact('data', 'start_date', 'end_date'))
        ->with('title', 'Relatório de Venda por Produtos');
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de venda por produtos.pdf", array("Attachment" => false));
    }

    private function uneArrayItens($itens, $itensCaixa, $ordem){
        $data = [];
        $adicionados = [];
        foreach($itens as $i){

            $temp = [
                'quantidade' => $i->soma_quantidade,
                'subtotal' => $i->subtotal,
                'valor' => $i->produto->valor_unitario,
                'media' => $i->media,
                'produto' => $i->produto,
            ];
            array_push($data, $temp);
            // array_push($adicionados, $i->produto->id);
        }

        // print_r($data[0]['produto']);
        foreach($itensCaixa as $i){
            $indiceAdicionado = $this->jaAdicionadoProduto($data, $i->produto->id);
            if($indiceAdicionado == -1){

                $temp = [
                    'quantidade' => $i->soma_quantidade,
                    'subtotal' => $i->subtotal,
                    'valor' => $i->produto->valor_unitario,
                    'media' => $i->media,
                    'produto' => $i->produto,
                ];
                array_push($data, $temp);
            }else{
                $data[$indiceAdicionado]['quantidade'] += $i->soma_quantidade; 
                $data[$indiceAdicionado]['subtotal'] += $i->subtotal; 
                $data[$indiceAdicionado]['media'] = ($data[$indiceAdicionado]['media'] + $i->media) / 2; 
            }
        }
        
        usort($data, function($a, $b) use ($ordem){
            if($ordem == 'asc') return $a['quantidade'] > $b['quantidade'] ? 1 : 0;
            else if($ordem == 'desc') return $a['quantidade'] < $b['quantidade'] ? 1 : 0;
            else return $a['produto']->nome > $b['produto']->nome ? 1 : 0;
        });
        return $data;
    }

    private function calculaCusto($itens){
        $custo = 0;
        foreach($itens as $i){
            $custo += $i->quantidade * $i->produto->valor_compra;
        }
        return $custo;
    }

    private function jaAdicionadoProduto($array, $produtoId){
        for($i=0; $i<sizeof($array); $i++){
            if($array[$i]['produto']->id == $produtoId){
                return $i;
            }
        }
        return -1;
    }

    public function estoque(Request $request){
        $estoque_minimo = $request->estoque_minimo;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $estoque_critico = $request->estoque_critico;
        $categoria_id = $request->categoria_id;
        $esportar_excel = $request->esportar_excel;
        $contexto = $this->resolveRelatorioEstoqueContext($request);
        $local_id = $contexto['local_id'];
        $deposito_id = $contexto['deposito_id'];
        $deposito = $contexto['deposito'];
        $localIds = $contexto['local_ids'];
        $quantidadePorProduto = $this->estoqueQuantidadePorProdutoMap($deposito_id, $localIds);
        $quantidadePorVariacao = $this->estoqueQuantidadePorVariacaoMap($deposito_id, $localIds);

        $data = [];

        if($start_date || $end_date){
            $estoque_critico = null;
        }

        if($estoque_critico){
            $data = $this->getEstoqueCriticoData($request, $localIds, $local_id, $categoria_id, $estoque_minimo, (int)$estoque_critico, $deposito_id);
        }else if($estoque_minimo == 1){

            $produtosComEstoqueMinimo = Produto::where('produtos.empresa_id', $request->empresa_id)
            ->select('produtos.*')
            ->when($categoria_id, function ($query) use ($categoria_id) {
                return $query->where(function($t) use ($categoria_id) 
                {
                    $t->where('categoria_id', $categoria_id)->orWhere('sub_categoria_id', $categoria_id);
                });
            })
            ->when(!empty($start_date), function ($query) use ($start_date) {
                return $query->whereDate('produtos.created_at', '>=', $start_date);
            })
            ->when(!empty($end_date), function ($query) use ($end_date,) {
                return $query->whereDate('produtos.created_at', '<=', $end_date);
            })
            ->where('produtos.estoque_minimo', '>', 0);

            $produtosComEstoqueMinimo = $this->applyRelatorioEstoqueContextToProdutoQuery($produtosComEstoqueMinimo, $deposito_id, $localIds)->get();

            foreach($produtosComEstoqueMinimo as $produto){
                $quantidadeProduto = (float)($quantidadePorProduto[$produto->id] ?? 0);
                
                if($quantidadeProduto <= $produto->estoque_minimo){

                    if(sizeof($produto->variacoes) == 0){
                        $linha = [
                            'produto' => $produto->nome,
                            'quantidade' => $quantidadeProduto,
                            'estoque_minimo' => $produto->estoque_minimo,
                            'valor_compra' => $produto->valor_compra,
                            'valor_venda' => $produto->valor_unitario,
                            'categoria' => $produto->categoria ? $produto->categoria->nome : '--',
                            'data_cadastro' => __data_pt($produto->created_at)
                        ];
                        array_push($data, $linha);
                    }else{
                        foreach($produto->variacoes as $v){
                            $linha = [
                                'produto' => $produto->nome . " " . $v->descricao,
                                'quantidade' => $this->quantidadeVariacaoRelatorio($quantidadePorVariacao, $produto->id, $v->id),
                                'estoque_minimo' => $produto->estoque_minimo,
                                'valor_compra' => $produto->valor_compra,
                                'valor_venda' => $v->valor,
                                'categoria' => $produto->categoria ? $produto->categoria->nome : '--',
                                'data_cadastro' => __data_pt($produto->created_at)
                            ];
                            array_push($data, $linha);
                        }
                    }
                }
            }
        }else if($start_date || $end_date){
            $movimentacoes = MovimentacaoProduto::
            select('movimentacao_produtos.*')
            ->when(!empty($start_date), function ($query) use ($start_date) {
                return $query->whereDate('movimentacao_produtos.created_at', '>=', $start_date);
            })
            ->when(!empty($end_date), function ($query) use ($end_date,) {
                return $query->whereDate('movimentacao_produtos.created_at', '<=', $end_date);
            })
            ->join('produtos', 'produtos.id', '=', 'movimentacao_produtos.produto_id')
            ->when($categoria_id, function ($query) use ($categoria_id) {
                return $query->where(function($t) use ($categoria_id) 
                {
                    $t->where('categoria_id', $categoria_id)->orWhere('sub_categoria_id', $categoria_id);
                });
            })
            ->where('produtos.empresa_id', $request->empresa_id)
            ->groupBy('produtos.id')
            ->orderBy('movimentacao_produtos.created_at', 'desc');

            $movimentacoes = $this->applyRelatorioEstoqueContextToProdutoQuery($movimentacoes, $deposito_id, $localIds)->get();

            foreach($movimentacoes as $m){
                $produto = $m->produto;
                if(sizeof($produto->variacoes) == 0){
                    $linha = [
                        'produto' => $produto->nome,
                        'quantidade' => (float)($quantidadePorProduto[$produto->id] ?? 0),
                        'estoque_minimo' => $produto->estoque_minimo,
                        'valor_compra' => $produto->valor_compra,
                        'valor_venda' => $produto->valor_unitario,
                        'categoria' => $produto->categoria ? $produto->categoria->nome : '--',
                        'data_cadastro' => __data_pt($produto->created_at)
                    ];
                    array_push($data, $linha);
                }else{
                    foreach($produto->variacoes as $v){
                        $linha = [
                            'produto' => $produto->nome . " " . $v->descricao,
                            'quantidade' => $this->quantidadeVariacaoRelatorio($quantidadePorVariacao, $produto->id, $v->id),
                            'estoque_minimo' => $produto->estoque_minimo,
                            'valor_compra' => $produto->valor_compra,
                            'valor_venda' => $v->valor,
                            'categoria' => $produto->categoria ? $produto->categoria->nome : '--',
                            'data_cadastro' => __data_pt($produto->created_at)
                        ];
                        array_push($data, $linha);
                    }
                }
            }

        }else{

            $produtos = Produto::select('produtos.*')
            ->where('produtos.empresa_id', $request->empresa_id)
            ->when($categoria_id, function ($query) use ($categoria_id) {
                return $query->where(function($t) use ($categoria_id) 
                {
                    $t->where('categoria_id', $categoria_id)->orWhere('sub_categoria_id', $categoria_id);
                });
            });

            $produtos = $this->applyRelatorioEstoqueContextToProdutoQuery($produtos, $deposito_id, $localIds)->get();

            foreach($produtos as $produto){
                if(sizeof($produto->variacoes) == 0){
                    $linha = [
                        'produto' => $produto->nome,
                        'quantidade' => (float)($quantidadePorProduto[$produto->id] ?? 0),
                        'estoque_minimo' => $produto->estoque_minimo,
                        'valor_compra' => $produto->valor_compra,
                        'valor_venda' => $produto->valor_unitario,
                        'categoria' => $produto->categoria ? $produto->categoria->nome : '--',
                        'data_cadastro' => __data_pt($produto->created_at)
                    ];
                    array_push($data, $linha);
                }else{
                    foreach($produto->variacoes as $v){
                        $linha = [
                            'produto' => $produto->nome . " " . $v->descricao,
                            'quantidade' => $this->quantidadeVariacaoRelatorio($quantidadePorVariacao, $produto->id, $v->id),
                            'estoque_minimo' => $produto->estoque_minimo,
                            'valor_compra' => $produto->valor_compra,
                            'valor_venda' => $v->valor,
                            'categoria' => $produto->categoria ? $produto->categoria->nome : '--',
                            'data_cadastro' => __data_pt($produto->created_at)
                        ];
                        array_push($data, $linha);
                    }
                }
            }
        }

        if($esportar_excel == -1){
            $p = view('relatorios/estoque', compact('data', 'start_date', 'end_date', 'estoque_minimo', 'deposito', 'estoque_critico'))
            ->with('title', 'Relatório de Estoque');
            $domPdf = new Dompdf(["enable_remote" => true]);
            $domPdf->loadHtml($p);

            $domPdf->setPaper("A4", "landscape");
            $domPdf->render();
            $domPdf->stream("Relatório de estoque.pdf", array("Attachment" => false));
        }else{
            $relatorioEx = new RelatorioEstoqueExport($data, $estoque_critico, $deposito);
            return Excel::download($relatorioEx, 'estoque.xlsx');
        }
    }

    private function getEstoqueCriticoData($request, $locais, $local_id, $categoria_id, $estoque_minimo, int $dias, ?int $deposito_id = null)
    {
        $limite = now()->subDays($dias)->endOfDay();

        $ultimasMovimentacoes = MovimentacaoProduto::select(
            'produto_id',
            DB::raw('MAX(movimentacao_produtos.created_at) as ultima_movimentacao')
        )
        ->when($deposito_id, function ($query) use ($deposito_id) {
            return $query->where(function ($sub) use ($deposito_id) {
                $sub->where('movimentacao_produtos.deposito_id', $deposito_id)
                    ->orWhere('movimentacao_produtos.deposito_origem_id', $deposito_id)
                    ->orWhere('movimentacao_produtos.deposito_destino_id', $deposito_id);
            });
        })
        ->groupBy('produto_id');

        $estoqueAtual = Estoque::select(
            'produto_id',
            DB::raw('SUM(estoques.quantidade) as quantidade_total')
        )
        ->when($deposito_id, function ($query) use ($deposito_id) {
            return $query->where('estoques.deposito_id', $deposito_id);
        })
        ->when(!$deposito_id, function ($query) use ($local_id, $locais) {
            if (!empty($local_id)) {
                return $query->where('estoques.local_id', $local_id);
            }

            return $query->whereIn('estoques.local_id', $locais);
        })
        ->groupBy('produto_id');

        $produtos = Produto::where('produtos.empresa_id', $request->empresa_id)
        ->select(
            'produtos.*',
            'estoque_atual.quantidade_total',
            DB::raw('COALESCE(ultimas_movimentacoes.ultima_movimentacao, produtos.created_at) as ultima_movimentacao')
        )
        ->joinSub($estoqueAtual, 'estoque_atual', function ($join) {
            $join->on('estoque_atual.produto_id', '=', 'produtos.id');
        })
        ->leftJoinSub($ultimasMovimentacoes, 'ultimas_movimentacoes', function ($join) {
            $join->on('ultimas_movimentacoes.produto_id', '=', 'produtos.id');
        })
        ->when($categoria_id, function ($query) use ($categoria_id) {
            return $query->where(function($t) use ($categoria_id) 
            {
                $t->where('produtos.categoria_id', $categoria_id)->orWhere('produtos.sub_categoria_id', $categoria_id);
            });
        })
        ->when($estoque_minimo == 1, function ($query) {
            return $query->where('produtos.estoque_minimo', '>', 0)
            ->whereColumn('estoque_atual.quantidade_total', '<=', 'produtos.estoque_minimo');
        })
        ->where('estoque_atual.quantidade_total', '>', 0)
        ->whereRaw('COALESCE(ultimas_movimentacoes.ultima_movimentacao, produtos.created_at) <= ?', [
            $limite->format('Y-m-d H:i:s')
        ])
        ->with('categoria')
        ->orderBy('ultima_movimentacao')
        ->get();

        return $produtos->map(function ($produto) {
            return [
                'produto' => $produto->nome,
                'quantidade' => $produto->quantidade_total,
                'estoque_minimo' => $produto->estoque_minimo,
                'valor_compra' => $produto->valor_compra,
                'valor_venda' => $produto->valor_unitario,
                'categoria' => $produto->categoria ? $produto->categoria->nome : '--',
                'data_cadastro' => __data_pt($produto->created_at),
                'ultima_movimentacao' => __data_pt($produto->ultima_movimentacao)
            ];
        })->toArray();
    }

    public function totalizaProdutos(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $local_id = $request->local_id;
        $esportar_excel = $request->esportar_excel;

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $data = Produto::select('produtos.*')
        ->where('produtos.empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('produtos.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('produtos.created_at', '<=', $end_date);
        })
        ->when(!empty($local_id), function ($query) use ($local_id) {
            return $query->whereExists(function ($sub) use ($local_id) {
                $sub->selectRaw('1')
                ->from('estoques')
                ->whereColumn('estoques.produto_id', 'produtos.id')
                ->where('estoques.local_id', $local_id);
            });
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereExists(function ($sub) use ($locais) {
                $sub->selectRaw('1')
                ->from('estoques')
                ->whereColumn('estoques.produto_id', 'produtos.id')
                ->whereIn('estoques.local_id', $locais);
            });
        })->get();

        $local = null;
        if($local_id){
            $local = Localizacao::findOrFail($local_id);
        }

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioTotalizaProdutosExport($data, $local_id, $local);
            return Excel::download($relatorioEx, 'relatorio_totaliza_produtos.xlsx');
        }

        $p = view('relatorios/totaliza_produtos', compact('data', 'local_id', 'local'))
        ->with('title', 'Relatório Totalizador Produtos');
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        // return $p;

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório totalizador de produtos.pdf", array("Attachment" => false));
    }

    public function vendasPorVendedor(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $funcionario_id = $request->funcionario_id;
        $local_id = $request->local_id;
        $esportar_excel = $request->esportar_excel;

        $funcionario = Funcionario::findOrFail($funcionario_id);
        $nves = Nfe::
        where('empresa_id', $request->empresa_id)
        ->where('funcionario_id', $funcionario_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })->get();

        $nfces = Nfce::
        where('empresa_id', $request->empresa_id)
        ->where('funcionario_id', $funcionario_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })->get();

        $data = [];
        foreach($nves as $n){
            $data[] = [
                'id' => $n->numero_sequencial,
                'cliente' => $n->cliente ? $n->cliente->info : 'Consumidor final',
                'data' => $n->created_at,
                'total' => $n->total,
                'localizacao' => $n->localizacao
            ];
        }

        foreach($nfces as $n){
            $data[] = [
                'id' => $n->numero_sequencial,
                'cliente' => $n->cliente ? $n->cliente->info : 'Consumidor final',
                'data' => $n->created_at,
                'total' => $n->total,
                'localizacao' => $n->localizacao
            ];
        }

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioVendasPorVendedorExport($data, $funcionario);
            return Excel::download($relatorioEx, 'relatorio_vendas_por_vendedor.xlsx');
        }

        $p = view('relatorios/vendas_por_vendedor', compact('data', 'funcionario'))
        ->with('title', 'Relatório Vendas por Vendedor');
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        // return $p;

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório vendas por vendedor.pdf", array("Attachment" => false));
    }

    public function custoMedio(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $categoria_id = $request->categoria_id;
        $ordem = $request->ordem;
        $esportar_excel = $request->esportar_excel;
        $contexto = $this->resolveRelatorioEstoqueContext($request);
        $deposito = $contexto['deposito'];
        $deposito_id = $contexto['deposito_id'];
        $localIds = $contexto['local_ids'];
        $quantidadePorProduto = $this->estoqueQuantidadePorProdutoMap($deposito_id, $localIds);

        $data = Produto::select('produtos.*')
        ->where('produtos.empresa_id', $request->empresa_id)
        ->where('gerenciar_estoque', 1)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('produtos.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('produtos.created_at', '<=', $end_date);
        })
        ->when(!empty($categoria_id), function ($query) use ($categoria_id) {
            return $query->where(function($t) use ($categoria_id) 
            {
                $t->where('categoria_id', $categoria_id)->orWhere('sub_categoria_id', $categoria_id);
            });
        });

        $data = $this->applyRelatorioEstoqueContextToProdutoQuery($data, $deposito_id, $localIds)->get();

        foreach($data as $item){
            $valor = ItemNfe::where('produto_id', $item->id)
            ->sum('sub_total');

            $quantidade = (float)($quantidadePorProduto[$item->id] ?? 0);
            $item->custo_medio = $valor/($quantidade > 0 ? $quantidade : 1);
            $item->quantidade = $quantidade;
            $item->categoria_nome = $item->categoria ? $item->categoria->nome : '--';
            $item->nome = $item->nome;
        }

        $data = $data->toArray();

        usort($data, function($a, $b) use ($ordem){
            if($ordem == 'asc') return $a['quantidade'] > $b['quantidade'] ? 1 : -1;
            else if($ordem == 'desc') return $a['quantidade'] < $b['quantidade'] ? 1 : -1;
            else return $a['nome'] > $b['nome'] ? 1 : -1;
        });

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioInventarioCustoMedioExport($data, $deposito);
            return Excel::download($relatorioEx, 'relatorio_inventario_custo_medio.xlsx');
        }

        $p = view('relatorios/inventario_custo_medio', compact('data', 'deposito'))
        ->with('title', 'Relatório inventário custo médio');
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório inventário custo médio.pdf", array("Attachment" => false));

    }

    public function registroInventario(Request $request){
        $date = $request->date;
        $livro = $request->livro;
        $tipo_custo = $request->tipo_custo;
        $esportar_excel = $request->esportar_excel;

        // $data = MovimentacaoProduto::
        // select('movimentacao_produtos.*')
        // ->whereDate('movimentacao_produtos.created_at', '<=', $date)
        // ->join('produtos', 'produtos.id', '=', 'movimentacao_produtos.produto_id')
        // ->where('produtos.empresa_id', $request->empresa_id)
        // ->groupBy('movimentacao_produtos.produto_id')
        // ->orderBy('produtos.nome')
        // ->having('movimentacao_produtos.quantidade', '>', 0)
        // ->limit(10)
        // ->get();

        $sub = MovimentacaoProduto::select(
            'produto_id',
            DB::raw('MAX(movimentacao_produtos.created_at) as ultima_data')
        )
        ->whereDate('movimentacao_produtos.created_at', '<=', $date)
        ->join('produtos', 'produtos.id', '=', 'movimentacao_produtos.produto_id')
        ->where('produtos.empresa_id', $request->empresa_id)
        ->groupBy('produto_id');

        $data = MovimentacaoProduto::select('movimentacao_produtos.*', 'produtos.nome')
        ->join('produtos', 'produtos.id', '=', 'movimentacao_produtos.produto_id')
        ->joinSub($sub, 'ultimas', function ($join) {
            $join->on('ultimas.produto_id', '=', 'movimentacao_produtos.produto_id')
            ->on('ultimas.ultima_data', '=', 'movimentacao_produtos.created_at');
        })
        ->where('produtos.empresa_id', $request->empresa_id)
        ->where('movimentacao_produtos.quantidade', '>', 0)
        ->orderBy('produtos.nome')
        // ->limit(10)
        ->get();

        if($tipo_custo == 'media'){
            // ver como faz
            foreach($data as $item){

                $valor = ItemNfe::where('produto_id', $item->produto_id)
                ->join('nves', 'nves.id', '=', 'item_nves.nfe_id')
                ->where('nves.tpNF', 0)
                ->sum('sub_total');

                $item->quantidade = $item->estoque_atual;
                $item->valor_unitario = $item->produto->valor_compra;

                if($valor > 0){

                    $qtd = ItemNfe::where('produto_id', $item->produto_id)
                    ->join('nves', 'nves.id', '=', 'item_nves.nfe_id')
                    ->where('nves.tpNF', 0)
                    ->sum('quantidade');
                    $custo_medio = $valor/$qtd;
                    $item->valor_unitario = $custo_medio;
                }
                $item->sub_total = $item->valor_unitario * $item->quantidade;

            }
        }else{
            foreach($data as $item){
                $item->valor_unitario = $item->produto->valor_compra;

                $item->quantidade = $item->estoque_atual;
                $item->sub_total = $item->produto->valor_compra * $item->quantidade;                
            }
        }

        $empresa = Empresa::findOrFail($request->empresa_id);

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioRegistroInventarioExport($data, $livro, $empresa, date('Y-m-d H:i'));
            return Excel::download($relatorioEx, 'relatorio_registro_inventario.xlsx');
        }

        $p = view('relatorios.registro_inventario', compact('data', 'livro', 'empresa'))
        ->with('title', 'Relatório registro inventário');
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório registro inventário", array("Attachment" => false));
    }

    public function inventario(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $ordem = $request->ordem;
        $livro = $request->livro;
        $esportar_excel = $request->esportar_excel;
        $contexto = $this->resolveRelatorioEstoqueContext($request);
        $deposito = $contexto['deposito'];
        $deposito_id = $contexto['deposito_id'];
        $localIds = $contexto['local_ids'];
        $quantidadePorProduto = $this->estoqueQuantidadePorProdutoMap($deposito_id, $localIds);
        
        $data = Produto::select('produtos.*')
        ->where('produtos.empresa_id', $request->empresa_id)
        ->where('gerenciar_estoque', 1)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('produtos.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('produtos.created_at', '<=', $end_date);
        })
        ;

        $data = $this->applyRelatorioEstoqueContextToProdutoQuery($data, $deposito_id, $localIds)->get();


        $empresa = Empresa::findOrFail($request->empresa_id);

        foreach($data as $item){

            $item->custo_unuitario = $item->valor_compra;
            $item->quantidade = (float)($quantidadePorProduto[$item->id] ?? 0);
            $item->sub_total = $item->quantidade * $item->valor_compra;
            $item->nome = $item->nome;
        }

        $data = $data->toArray();

        usort($data, function($a, $b) use ($ordem){
            if($ordem == 'asc') return $a['quantidade'] > $b['quantidade'] ? 1 : -1;
            else if($ordem == 'desc') return $a['quantidade'] < $b['quantidade'] ? 1 : -1;
            else return $a['nome'] > $b['nome'] ? 1 : -1;
        });

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioInventarioExport($data, $deposito, $empresa, $livro);
            return Excel::download($relatorioEx, 'relatorio_inventario.xlsx');
        }

        $p = view('relatorios/inventario', compact('data', 'deposito', 'livro', 'empresa'))
        ->with('title', 'Relatório inventário');
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório inventário.pdf", array("Attachment" => false));

    }

    public function curvaAbcClientes(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $esportar_excel = $request->esportar_excel;

        $nfe = Nfe::where('nves.empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('nves.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('nves.created_at', '<=', $end_date);
        })
        ->join('clientes', 'clientes.id', '=', 'nves.cliente_id')
        ->groupBy('cliente_id')
        ->select('clientes.id as cliente_id', 'clientes.razao_social as nome', \DB::raw('sum(nves.total) as total'), \DB::raw('count(nves.id) as count'))
        ->get();

        $nfce = Nfce::where('nfces.empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('nfces.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('nfces.created_at', '<=', $end_date);
        })
        ->join('clientes', 'clientes.id', '=', 'nfces.cliente_id')
        ->groupBy('cliente_id')
        ->select('clientes.id as cliente_id', 'clientes.razao_social as nome', \DB::raw('sum(nfces.total) as total'), \DB::raw('count(nfces.id) as count'))
        ->get();


        $data = $this->agrupaArrayCurva($nfe, $nfce);

        $soma = 0;
        foreach($data as $a){
            $soma += $a['total'];
        }

        foreach($data as $key => $a){
            $totalLinha = $data[$key]['total'];
            $v = 100 - (((($totalLinha-$soma)/$soma)*100)*-1);

            $data[$key]['percentual'] = number_format($v, 2);
        }

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioCurvaAbcClientesExport($data, $soma);
            return Excel::download($relatorioEx, 'relatorio_curva_abc_clientes.xlsx');
        }

        $p = view('relatorios/curva_abc_clientes')
        ->with('data', $data)
        ->with('soma', $soma)
        ->with('title', 'Curva ABC Clientes');

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Curva ABC Clientes.pdf", array("Attachment" => false));

    }

    public function entregaDeProdutos(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $vendas = $request->vendas;
        $esportar_excel = $request->esportar_excel;

        $vNfe = [];
        $vNfce = [];
        $filtroVenda = 0;

        if($vendas){
            $filtroVenda = 1;
            foreach($vendas as $v){
                $ex = explode("_", $v);
                if($ex[0] == 'pedido'){
                    $vNfe[] = $ex[1];
                }else{
                    $vNfce[] = $ex[1];
                }
            }
        }

        $itensNfe = ItemNfe::where('nves.empresa_id', $request->empresa_id)->where('nves.tpNF', 1)
        ->join('nves', 'nves.id', '=', 'item_nves.nfe_id')
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->where('nves.created_at', '>=', $start_date);
        })
        ->when(sizeof($vNfe) > 0, function ($query) use ($vNfe) {
            return $query->whereIn('nves.id', $vNfe);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->where('nves.created_at', '<=', $end_date);
        })
        ->where('nves.empresa_id', $request->empresa_id)
        ->get();

        if($filtroVenda == 1 && sizeof($vNfe) == 0){
            $itensNfe = [];
        }

        $itensNfce = ItemNfce::where('nfces.empresa_id', $request->empresa_id)
        ->join('nfces', 'nfces.id', '=', 'item_nfces.nfce_id')
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->where('nfces.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->where('nfces.created_at', '<=', $end_date);
        })
        ->when(sizeof($vNfce) > 0, function ($query) use ($vNfce) {
            return $query->whereIn('nfces.id', $vNfce);
        })
        ->where('nfces.empresa_id', $request->empresa_id)
        ->get();

        if($filtroVenda == 1 && sizeof($vNfce) == 0){
            $itensNfce = [];
        }

        $data = [];
        $dataPushId = [];

        foreach($itensNfe as $i){
            if(!in_array($i->produto_id, $dataPushId)){
                $obj = [
                    'produto_id' => $i->produto_id,
                    'numero_sequencial' => $i->produto->numero_sequencial,
                    'quantidade' => (int)$i->quantidade,
                    'produto_nome' => $i->produto->nome
                ];

                $data[] = $obj;
                $dataPushId[] = $i->produto_id;
            }else{

                for($j=0; $j<sizeof($data); $j++){
                    if($data[$j]['produto_id'] == $i->produto_id){
                        $data[$j]['quantidade'] += (int)$i->quantidade;
                    }
                }
            }
        }

        foreach($itensNfce as $i){
            if(!in_array($i->produto_id, $dataPushId)){
                $obj = [
                    'produto_id' => $i->produto_id,
                    'numero_sequencial' => $i->produto->numero_sequencial,
                    'quantidade' => (int)$i->quantidade,
                    'produto_nome' => $i->produto->nome
                ];

                $data[] = $obj;
                $dataPushId[] = $i->produto_id;
            }else{

                for($j=0; $j<sizeof($data); $j++){
                    if($data[$j]['produto_id'] == $i->produto_id){
                        $data[$j]['quantidade'] += (int)$i->quantidade;
                    }
                }
            }
        }

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioEntregaProdutosExport($data);
            return Excel::download($relatorioEx, 'relatorio_entrega_produtos.xlsx');
        }

        $p = view('relatorios/entrega_produtos')
        ->with('data', $data)
        ->with('title', 'Entrega de Produtos');

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4");
        $domPdf->render();
        $domPdf->stream("Entrega de Produtos.pdf", array("Attachment" => false));
    }    

    private function agrupaArrayCurva($nfe, $nfce){
        $clientes = [];
        $clientesId = [];
        foreach($nfe as $v){
            $temp = [
                'nome' => $v->nome,
                'total' => $v->total,
                'cliente_id' => $v->cliente_id,
                'count' => $v->count,
                'percentual' => 0
            ];
            $clientesId[] = $v->cliente_id;
            array_push($clientes, $temp);
        }

        foreach($nfce as $v){

            if(!in_array($v->cliente_id, $clientesId)){
                $temp = [
                    'nome' => $v->nome,
                    'total' => $v->total,
                    'cliente_id' => $v->cliente_id,
                    'count' => $v->count,
                    'percentual' => 0
                ];
                array_push($clientes, $temp);
            }else{
                $v['total'] += $v->total;
                $v['count'] += $v->count;
            }

        }
        return $clientes;
    }

    public function movimentacao(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $marca_id = $request->marca_id;
        $categoria_id = $request->categoria_id;
        $produto_id = $request->produto_id;
        $esportar_excel = $request->esportar_excel;
        $movimentacoes = MovimentacaoProduto::with([
            'produto.categoria',
            'produtoVariacao'
        ])
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('movimentacao_produtos.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('movimentacao_produtos.created_at', '<=', $end_date);
        })
        ->whereHas('produto', function ($query) use ($request, $produto_id, $marca_id, $categoria_id) {
            $query->where('empresa_id', $request->empresa_id)
            ->when(!empty($produto_id), function ($subQuery) use ($produto_id) {
                return $subQuery->where('produtos.id', $produto_id);
            })
            ->when(!empty($marca_id), function ($subQuery) use ($marca_id) {
                return $subQuery->where('marca_id', $marca_id);
            })
            ->when(!empty($categoria_id), function ($subQuery) use ($categoria_id) {
                return $subQuery->where(function($t) use ($categoria_id) {
                    $t->where('categoria_id', $categoria_id)->orWhere('sub_categoria_id', $categoria_id);
                });
            });
        })
        ->orderBy('movimentacao_produtos.created_at', 'desc')
        ->get();

        $data = $movimentacoes
        ->map(function ($item) {
            $nomeProduto = optional($item->produto)->nome ?? '--';
            if($item->produtoVariacao && $item->produtoVariacao->descricao){
                $nomeProduto .= ' ' . $item->produtoVariacao->descricao;
            }

            return [
                'tipo' => $item->tipo == 'incremento' ? 'Entrada' : 'Saída',
                'quantidade' => $item->quantidade,
                'data' => $item->created_at,
                'movimentacao' => $this->movimentacaoTipoTransacaoLabel($item->tipo_transacao),
                'produto' => $nomeProduto,
                'categoria' => optional(optional($item->produto)->categoria)->nome ?? '--',
                'codigo' => $item->codigo_transacao,
                'estoque_atual' => $item->estoque_atual,
            ];
        })
        ->values()
        ->all();

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioMovimentacaoExport($data, $start_date, $end_date);
            return Excel::download($relatorioEx, 'relatorio_movimentacao.xlsx');
        }

        $p = view('relatorios/movimentacao')
        ->with('data', $data)
        ->with('start_date', $start_date)
        ->with('end_date', $end_date)
        ->with('title', 'Movimentação');

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Movimentação.pdf", array("Attachment" => false));
    }

    private function movimentacaoTipoTransacaoLabel($tipoTransacao)
    {
        if($tipoTransacao == 'venda_nfe'){
            return 'Venda NF-e';
        }
        if($tipoTransacao == 'venda_nfce'){
            return 'Venda NFC-e';
        }
        if($tipoTransacao == 'compra'){
            return 'Compra';
        }
        if($tipoTransacao == 'transferencia_estoque'){
            return 'Transferência de estoque';
        }
        return 'Ajuste';
    }

    public function ordemServico(Request $request)
    {
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $cliente_id = $request->cliente;
        $local_id = $request->local_id;
        $esportar_excel = $request->esportar_excel;

        $data = OrdemServico::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->get();

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioOrdemServicoExport($data);
            return Excel::download($relatorioEx, 'relatorio_ordem_servico.xlsx');
        }


        $p = view('relatorios/ordem_servico', compact('data'))
        ->with('title', 'Relatório de Ordem de Serviço');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Ordem de Serviço.pdf", array("Attachment" => false));
    }

    public function tiposDePagamento(Request $request)
    {
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $tipo_pagamento = $request->tipo_pagamento;
        $local_id = $request->local_id;
        $esportar_excel = $request->esportar_excel;

        $nves = Nfe::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->get();

        $nfces = Nfce::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->get();

        $data = $this->getTiposPagamento($tipo_pagamento);
        foreach($nves as $n){
            foreach($n->fatura as $f){
                if(isset($data[$f->tipo_pagamento])){
                    $data[$f->tipo_pagamento] += $f->valor;
                }
            }
        }

        foreach($nfces as $n){
            foreach($n->fatura as $f){
                if(isset($data[$f->tipo_pagamento])){
                    $data[$f->tipo_pagamento] += $f->valor;
                }
            }
        }

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioTiposPagamentoExport($data);
            return Excel::download($relatorioEx, 'relatorio_tipos_pagamento.xlsx');
        }

        $p = view('relatorios/tipos_pagamento', compact('data'))
        ->with('title', 'Relatório de Tipos de Pagamento');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Tipos de Pagamento.pdf", array("Attachment" => false));
    }

    private function getTiposPagamento($tipo_pagamento = null){
        $data = [];
        foreach(Nfe::tiposPagamento() as $key => $n){
            if($tipo_pagamento != null){
                if($tipo_pagamento == $key){
                    $data[$key] = 0;
                }
            }else{
                $data[$key] = 0;
            }
        }
        return $data;
    }

    public function reservas(Request $request)
    {

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $estado = $request->estado;
        $vagos = $request->vagos;
        $esportar_excel = $request->esportar_excel;

        if($vagos == 1){

            $reservas = Reserva::where('empresa_id', $request->empresa_id)
            ->where(function ($query) use ($start_date, $end_date) {
                $query->whereDate('data_checkin', '<=', $start_date)
                ->whereDate('data_checkout', '>=', $end_date);
            })
            ->where('estado', '!=', 'cancelado')
            ->pluck('acomodacao_id')
            ->all();

            $data = Acomodacao::where('empresa_id', request()->empresa_id)
            ->whereNotIn('id', $reservas)
            ->where('status', 1)
            ->get();

            if($esportar_excel == 1){
                $relatorioEx = new RelatorioReservasExport($data, $start_date, $end_date, $vagos);
                return Excel::download($relatorioEx, 'relatorio_reservas.xlsx');
            }

            $p = view('relatorios/reserva_vagos', compact('data', 'start_date', 'end_date'))
            ->with('title', 'Relatório de acomodações vagas por período');
        }else{
            $data = Reserva::where('empresa_id', $request->empresa_id)
            ->when(!empty($start_date), function ($query) use ($start_date) {
                return $query->whereDate('data_checkin', '>=', $start_date);
            })
            ->when(!empty($end_date), function ($query) use ($end_date,) {
                return $query->whereDate('data_checkout', '<=', $end_date);
            })
            ->when($estado != "", function ($query) use ($estado) {
                return $query->where('estado', $estado);
            })
            ->get();

            if($esportar_excel == 1){
                $relatorioEx = new RelatorioReservasExport($data, $start_date, $end_date, $vagos);
                return Excel::download($relatorioEx, 'relatorio_reservas.xlsx');
            }

            $p = view('relatorios/reservas', compact('data', 'start_date', 'end_date'))
            ->with('title', 'Relatório de Reservas');
        }

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Reservas.pdf", array("Attachment" => false));
    }

    public function lucroProduto(Request $request){

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $marca_id = $request->marca_id;
        $categoria_id = $request->categoria_id;
        $produto_id = $request->produto_id;
        $esportar_excel = $request->esportar_excel;

        $dataNfe = ItemNfe::where('produtos.empresa_id', $request->empresa_id)
        ->select('produtos.id as produto_id')
        ->join('produtos', 'produtos.id', '=', 'item_nves.produto_id')
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('item_nves.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('item_nves.created_at', '<=', $end_date);
        })
        ->when(!empty($categoria_id), function ($query) use ($categoria_id) {
            return $query->where(function($t) use ($categoria_id) 
            {
                $t->where('produtos.categoria_id', $categoria_id)->orWhere('produtos.sub_categoria_id', $categoria_id);
            });
        })
        ->when(!empty($marca_id), function ($query) use ($marca_id) {
            return $query->where('produtos.marca_id', $marca_id);
        })
        ->when(!empty($produto_id), function ($query) use ($produto_id) {
            return $query->where('produtos.id', $produto_id);
        })
        ->groupBy('produto_id')
        ->pluck('produto_id')->toArray();

        $dataNfce = ItemNfce::where('produtos.empresa_id', $request->empresa_id)
        ->select('produtos.id as produto_id')
        ->join('produtos', 'produtos.id', '=', 'item_nfces.produto_id')
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('item_nfces.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('item_nfces.created_at', '<=', $end_date);
        })
        ->when(!empty($categoria_id), function ($query) use ($categoria_id) {
            return $query->where(function($t) use ($categoria_id) 
            {
                $t->where('produtos.categoria_id', $categoria_id)->orWhere('produtos.sub_categoria_id', $categoria_id);
            });
        })
        ->when(!empty($marca_id), function ($query) use ($marca_id) {
            return $query->where('produtos.marca_id', $marca_id);
        })
        ->when(!empty($produto_id), function ($query) use ($produto_id) {
            return $query->where('produtos.id', $produto_id);
        })
        ->groupBy('produto_id')
        ->pluck('produto_id')->toArray();

        $resultado = array_unique(array_merge($dataNfe, $dataNfce));
        $data = [];
        foreach($resultado as $produto_id){
            $produto = Produto::findOrFail($produto_id);

            $subVenda = ItemNfe::where('produto_id', $produto_id)
            ->where('nves.tpNF', 1)
            ->join('nves', 'nves.id', '=', 'item_nves.nfe_id')
            ->sum('sub_total');

            $subVendaNfce = ItemNfce::where('produto_id', $produto_id)
            ->join('nfces', 'nfces.id', '=', 'item_nfces.nfce_id')
            ->sum('sub_total');

            $subCompra = ItemNfe::where('produto_id', $produto_id)
            ->where('nves.tpNF', 0)
            ->join('nves', 'nves.id', '=', 'item_nves.nfe_id')
            ->sum('sub_total');

        $data[] = [
                'produto_id' => $produto_id,
                'numero_sequencial' => $produto->numero_sequencial,
                'produto_nome' => $produto->nome,
                'total_vendas' => $subVenda + $subVendaNfce,
                'total_compras' => $subCompra,
            ];
        }

        if($esportar_excel == 1){
            $relatorioEx = new RelatorioLucroProdutoExport($data, $start_date, $end_date);
            return Excel::download($relatorioEx, 'relatorio_lucro_produto.xlsx');
        }

        $p = view('relatorios.lucro_produto', compact('data', 'start_date', 'end_date'))
        ->with('title', 'Relatório de Lucro por Produto');

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Lucro por Produto.pdf", array("Attachment" => false));
    }
}
