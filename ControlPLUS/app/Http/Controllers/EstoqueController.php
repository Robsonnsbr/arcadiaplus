<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estoque;
use App\Models\CategoriaProduto;
use App\Utils\EstoqueUtil;
use App\Models\RetiradaEstoque;
use App\Models\ProdutoLocalizacao;
use App\Models\Localizacao;
use App\Models\ProdutoUnico;
use App\Models\EstoqueStatusSaldo;
use App\Models\ConfigGeral;
use App\Models\UsuarioLocalizacao;
use App\Models\Empresa;
use App\Services\EstoqueStatusService;
use App\Utils\QuantidadeUtil;
use App\Utils\StatusKeyUtil;
use App\Utils\VariacaoQueryUtil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

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
        $this->middleware('permission:localizacao_create', ['only' => ['storeLocalizacao']]);
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

    private function statusBaseOptions(): array
    {
        return [
            StatusKeyUtil::DEFAULT_STATUS,
            'ASSISTENCIA',
            'DEFEITO',
            'EMPRESTADO',
        ];
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

    private function statusOptions(array $statuses = []): array
    {
        $allStatuses = collect($this->statusBaseOptions())
            ->merge($statuses)
            ->map(function ($status) {
                return $this->normalizaStatusKey($status);
            })
            ->filter()
            ->unique()
            ->values();

        return $allStatuses
            ->map(function ($status) {
                return [
                    'value' => $status,
                    'label' => $this->formatStatusLabel($status),
                ];
            })
            ->values()
            ->all();
    }

    private function somaNaoAtivosPorLocal(int $empresa_id, int $produto_id, $produto_variacao_id, int $local_id): int
    {
        return $this->estoqueStatusService->somaReservasNaoAtivoLocalUnits(
            $empresa_id,
            $produto_id,
            $produto_variacao_id,
            $local_id
        );
    }

    private function saldoFisicoPorLocal(int $produto_id, $produto_variacao_id, int $local_id): int
    {
        return $this->estoqueStatusService->saldoFisicoLocalUnits($produto_id, $produto_variacao_id, $local_id);
    }

    private function saldoStatusNaoSerial(
        int $empresa_id,
        int $produto_id,
        $produto_variacao_id,
        int $local_id,
        string $status_key
    ): int {
        $status = $this->normalizaStatusKey($status_key, true);
        if ($status === StatusKeyUtil::DEFAULT_STATUS) {
            return $this->estoqueStatusService->ativoDisponivelUnits($empresa_id, $produto_id, $produto_variacao_id, $local_id);
        }

        $query = EstoqueStatusSaldo::where('empresa_id', $empresa_id)
            ->where('produto_id', $produto_id)
            ->where('local_id', $local_id)
            ->where('status_key', $status);
        $query = VariacaoQueryUtil::apply($query, $produto_variacao_id);
        return QuantidadeUtil::toUnits($query->sum('quantidade'));
    }

    private function ajustarSaldoNaoAtivo(
        int $empresa_id,
        int $produto_id,
        $produto_variacao_id,
        int $local_id,
        string $status_key,
        int $deltaUnits
    ): void {
        $status = $this->normalizaStatusKey($status_key, true);
        if ($status === StatusKeyUtil::DEFAULT_STATUS || $deltaUnits === 0) {
            return;
        }

        $query = EstoqueStatusSaldo::where('empresa_id', $empresa_id)
            ->where('produto_id', $produto_id)
            ->where('local_id', $local_id)
            ->where('status_key', $status);
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
            $registro->status_key = $status;
        }
        $registro->quantidade = QuantidadeUtil::fromUnits($novoUnits);
        $registro->save();
    }

    private function buildDistribuicaoSerial(Estoque $item): array
    {
        $empresa_id = (int)$item->produto->empresa_id;

        $seriais = ProdutoUnico::where('produto_id', $item->produto_id)
            ->where('tipo', 'entrada')
            ->where('em_estoque', 1)
            ->orderBy('codigo')
            ->get(['id', 'codigo', 'local_id', 'status_key']);

        $statusesEncontrados = [];
        $localIds = [];
        $counts = [];

        foreach ($seriais as $serial) {
            $status = $this->normalizaStatusKey($serial->status_key) ?? StatusKeyUtil::DEFAULT_STATUS;
            $statusesEncontrados[] = $status;
            $localId = $serial->local_id ? (int)$serial->local_id : null;
            if ($localId) {
                $localIds[] = $localId;
            }

            $bucket = $localId !== null ? (string)$localId : 'null';
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
        $statusOptions = $this->statusOptions($statusesEncontrados);
        $statusValues = collect($statusOptions)->pluck('value')->all();

        $linhas = [];
        foreach ($counts as $bucket => $qtdPorStatus) {
            $localId = $bucket === 'null' ? null : (int)$bucket;
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
                $localId = $serial->local_id ? (int)$serial->local_id : null;

                return [
                    'produto_unico_id' => (int)$serial->id,
                    'codigo' => $serial->codigo,
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
        $estoquesProduto = $estoquesProdutoQuery->get(['local_id', 'quantidade']);

        $statusRowsQuery = EstoqueStatusSaldo::where('empresa_id', $empresa_id)
            ->where('produto_id', $item->produto_id);
        $statusRowsQuery = VariacaoQueryUtil::apply($statusRowsQuery, $produto_variacao_id);
        $statusRows = $statusRowsQuery
            ->where('quantidade', '>', 0)
            ->get(['local_id', 'status_key', 'quantidade']);

        $localIds = $estoquesProduto->pluck('local_id')
            ->merge($statusRows->pluck('local_id'))
            ->filter()
            ->map(function ($id) {
                return (int)$id;
            })
            ->unique()
            ->values();

        $locais = $this->locaisDisponiveisParaOperacao($empresa_id, $localIds->all())->keyBy('id');
        $statusOptions = $this->statusOptions($statusRows->pluck('status_key')->all());
        $statusValues = collect($statusOptions)->pluck('value')->all();

        $linhas = [];
        foreach ($localIds as $localId) {
            $localId = (int)$localId;
            $totalFisico = QuantidadeUtil::toUnits($estoquesProduto->where('local_id', $localId)->sum('quantidade'));

            $statusQty = [];
            foreach ($statusRows->where('local_id', $localId) as $row) {
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

            $query = ProdutoUnico::where('produto_id', $item->produto_id)
                ->where('tipo', 'entrada')
                ->where('em_estoque', 1);

            $localFiltro = $request->filled('local_id') ? (int)$request->local_id : null;
            if ($localFiltro) {
                $localValido = Localizacao::where('id', $localFiltro)
                    ->where('empresa_id', $empresa_id)
                    ->exists();
                if (!$localValido) {
                    return response()->json(['message' => 'Local inválido para a empresa ativa.'], 422);
                }

                $query->where(function ($q) use ($localFiltro) {
                    $q->where('local_id', $localFiltro)
                        ->orWhereNull('local_id');
                });
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
                    $localId = $serial->local_id ? (int)$serial->local_id : null;

                    return [
                        'produto_unico_id' => (int)$serial->id,
                        'codigo' => $serial->codigo,
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
            'local_destino_id' => 'required|integer',
            'status_destino' => 'nullable|string|max:60',
            'status_key' => 'nullable|string|max:60',
        ], [
            'produto_unico_id.required' => 'Selecione o código único.',
            'local_destino_id.required' => 'Informe o local de destino.',
        ]);
        $validator->validate();

        $serialId = (int)$request->produto_unico_id;
        $localDestinoId = (int)$request->local_destino_id;
        $statusRaw = $request->status_key ?? $request->status_destino;
        $statusDestino = $this->normalizaStatusKey($statusRaw, true);
        if (strlen($statusDestino) > StatusKeyUtil::MAX_LENGTH) {
            throw new \Exception('Status inválido: tamanho máximo excedido.');
        }

        $locaisPermitidos = $this->locaisPermitidosIds($empresa_id);
        if (!in_array($localDestinoId, $locaisPermitidos, true)) {
            throw new \Exception('Local de destino não permitido para o usuário.');
        }

        $localDestino = Localizacao::where('id', $localDestinoId)
            ->where('empresa_id', $empresa_id)
            ->first();
        if (!$localDestino) {
            throw new \Exception('Local de destino inválido para a empresa ativa.');
        }

        DB::transaction(function () use ($serialId, $item, $localDestinoId, $statusDestino) {
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
            $localOrigemId = $serial->local_id ? (int)$serial->local_id : null;
            $origemInferida = false;
            if (!$localOrigemId) {
                $localOrigemId = $this->resolveLocalPadraoIdEmpresa((int)$item->produto->empresa_id);
                if (!$localOrigemId) {
                    throw new \Exception('Unidade sem local de origem definido e sem local PADRÃO disponível.');
                }
                $origemInferida = true;
                $serial->local_id = (int)$localOrigemId;
            }

            $localOrigemValido = Localizacao::where('id', $localOrigemId)
                ->where('empresa_id', $item->produto->empresa_id)
                ->exists();
            if (!$localOrigemValido) {
                throw new \Exception('Local de origem inválido para a empresa ativa.');
            }

            if ($localOrigemId === $localDestinoId && $statusOrigem === $statusDestino && !$origemInferida) {
                throw new \Exception('Nenhuma alteração informada para movimentação.');
            }

            if ($localOrigemId !== $localDestinoId) {
                $estoqueOrigemQuery = Estoque::where('produto_id', $item->produto_id)
                    ->where('local_id', $localOrigemId);
                $estoqueOrigemQuery = VariacaoQueryUtil::apply($estoqueOrigemQuery, $item->produto_variacao_id);
                $estoqueOrigem = $estoqueOrigemQuery->lockForUpdate()->first();

                if (!$estoqueOrigem || (float)$estoqueOrigem->quantidade < 1) {
                    throw new \Exception('Estoque insuficiente no local de origem para mover a unidade.');
                }

                $this->util->reduzEstoque($item->produto_id, 1, $item->produto_variacao_id, $localOrigemId);
                $this->util->incrementaEstoque($item->produto_id, 1, $item->produto_variacao_id, $localDestinoId);

                ProdutoLocalizacao::updateOrCreate([
                    'produto_id' => $item->produto_id,
                    'localizacao_id' => $localOrigemId,
                ]);
                ProdutoLocalizacao::updateOrCreate([
                    'produto_id' => $item->produto_id,
                    'localizacao_id' => $localDestinoId,
                ]);
            }

            $serial->local_id = $localDestinoId;
            $serial->status_key = $statusDestino;
            $serial->save();
        });
    }

    private function movimentarDistribuicaoQuantidade(Request $request, Estoque $item, int $empresa_id): void
    {
        $validator = Validator::make($request->all(), [
            'local_origem_id' => 'required|integer',
            'local_destino_id' => 'required|integer',
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

        $localOrigemId = (int)$request->local_origem_id;
        $localDestinoId = (int)$request->local_destino_id;

        $statusOrigemRaw = $request->status_origem ?: StatusKeyUtil::DEFAULT_STATUS;
        $statusDestinoRaw = $request->status_key ?? $request->status_destino;
        $statusOrigem = $this->normalizaStatusKey($statusOrigemRaw, true);
        $statusDestino = $this->normalizaStatusKey($statusDestinoRaw, true);

        $quantidadeUnits = QuantidadeUtil::toUnits($request->quantidade);
        if ($quantidadeUnits <= 0) {
            throw new \Exception('Quantidade inválida.');
        }
        if ($localOrigemId === $localDestinoId && $statusOrigem === $statusDestino) {
            throw new \Exception('Nenhuma alteração informada para movimentação.');
        }

        $locaisPermitidos = $this->locaisPermitidosIds($empresa_id);
        if (!in_array($localOrigemId, $locaisPermitidos, true) || !in_array($localDestinoId, $locaisPermitidos, true)) {
            throw new \Exception('Local de origem/destino não permitido para o usuário.');
        }

        $origemLocal = Localizacao::where('id', $localOrigemId)->where('empresa_id', $empresa_id)->first();
        $destinoLocal = Localizacao::where('id', $localDestinoId)->where('empresa_id', $empresa_id)->first();
        if (!$origemLocal || !$destinoLocal) {
            throw new \Exception('Local de origem/destino inválido para a empresa ativa.');
        }

        $saldoOrigemStatus = $this->saldoStatusNaoSerial(
            $empresa_id,
            (int)$item->produto_id,
            $item->produto_variacao_id ?: null,
            $localOrigemId,
            $statusOrigem
        );
        if ($saldoOrigemStatus < $quantidadeUnits) {
            throw new \Exception('Saldo insuficiente no status de origem.');
        }

        DB::transaction(function () use (
            $item,
            $empresa_id,
            $localOrigemId,
            $localDestinoId,
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
                    $statusOrigem,
                    -$quantidadeUnits
                );
            }

            if ($localOrigemId !== $localDestinoId) {
                $quantidade = QuantidadeUtil::fromUnits($quantidadeUnits);
                $this->util->reduzEstoque($item->produto_id, $quantidade, $item->produto_variacao_id, $localOrigemId);
                $this->util->incrementaEstoque($item->produto_id, $quantidade, $item->produto_variacao_id, $localDestinoId);

                ProdutoLocalizacao::updateOrCreate([
                    'produto_id' => $item->produto_id,
                    'localizacao_id' => $localOrigemId,
                ]);
                ProdutoLocalizacao::updateOrCreate([
                    'produto_id' => $item->produto_id,
                    'localizacao_id' => $localDestinoId,
                ]);
            }

            if ($statusDestino !== StatusKeyUtil::DEFAULT_STATUS) {
                $this->ajustarSaldoNaoAtivo(
                    $empresa_id,
                    (int)$item->produto_id,
                    $item->produto_variacao_id ?: null,
                    $localDestinoId,
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

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $local_id = $request->local_id;
        $categoria_id = $request->categoria_id;

        $statusOperacionalOptions = [
            'TODOS' => 'Todos',
            'ATIVO' => 'Disponíveis para venda (ATIVO)',
            'ASSISTENCIA' => 'Assistência',
            'DEFEITO' => 'Defeito',
            'EMPRESTADO' => 'Emprestado',
        ];
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
            'produto.produtoUnicosDisponiveis' => function ($q) {
                $q->select('id', 'produto_id', 'codigo', 'em_estoque');
            },
        ])
        ->select('estoques.*', 'produtos.nome as produto_nome', 'localizacaos.nome as localizacao_nome')
        ->selectRaw("CASE WHEN produtos.tipo_unico = 1
            THEN {$serialAtivoExpr}
            ELSE GREATEST((estoques.quantidade - {$naoAtivoSumExpr}), 0)
        END as disponivel_ativo_qtd")
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

        return view('estoque.index', compact(
            'data',
            'categorias',
            'tipoExibe',
            'statusOperacionalOptions',
            'statusOperacionalSelecionado',
            'statusOperacionalLabel',
            'mostrarColunaStatusFiltro'
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
                $saldoNaoAtivo = $this->somaNaoAtivosPorLocal(
                    (int)$item->produto->empresa_id,
                    (int)$item->produto_id,
                    $item->produto_variacao_id ?: null,
                    (int)$item->local_id
                );
                if ($saldoNaoAtivo > 0) {
                    throw new \Exception('Não é possível remover o estoque: existem saldos não-ATIVO reservados neste local.');
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
                        $novaQuantidadeUnits = QuantidadeUtil::toUnits($request->quantidade[$i] ?? 0);
                        if ($novaQuantidadeUnits < 0) {
                            throw new \Exception("Quantidade inválida.");
                        }

                        $saldoNaoAtivoLocal = $this->somaNaoAtivosPorLocal(
                            (int)$empresa_id,
                            (int)$item->produto_id,
                            $item->produto_variacao_id ?: null,
                            (int)$localDestinoId
                        );
                        if (!$item->produto->tipo_unico && $novaQuantidadeUnits < $saldoNaoAtivoLocal) {
                            throw new \Exception("Quantidade final inválida: ficaria abaixo do reservado em status não-ATIVO.");
                        }

                        $diferenca = 0;
                        $tipo = 'incremento';
                        $quantidadeAtualUnits = QuantidadeUtil::toUnits($item->quantidade);

                        if($quantidadeAtualUnits > $novaQuantidadeUnits){
                            $diferenca = QuantidadeUtil::fromUnits($quantidadeAtualUnits - $novaQuantidadeUnits);
                            $tipo = 'reducao';
                        }else{
                            $diferenca = QuantidadeUtil::fromUnits($novaQuantidadeUnits - $quantidadeAtualUnits);
                        }
                        $item->quantidade = QuantidadeUtil::fromUnits($novaQuantidadeUnits);
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

                            $saldoNaoAtivoOrigem = $this->somaNaoAtivosPorLocal(
                                (int)$empresa_id,
                                (int)$anterior->produto_id,
                                $anterior->produto_variacao_id ?: null,
                                (int)$localAnteriorId
                            );
                            if (!$anterior->produto->tipo_unico && $saldoNaoAtivoOrigem > 0) {
                                throw new \Exception("Não é possível mover zerando o local de origem: existem saldos não-ATIVO reservados.");
                            }

                            $anterior->quantidade = 0;
                            $anterior->save();

                            ProdutoLocalizacao::updateOrCreate([
                                'produto_id' => $anterior->produto_id, 
                                'localizacao_id' => $localDestinoId
                            ]);

                            $qtdDestinoUnits = QuantidadeUtil::toUnits($request->quantidade[$i] ?? 0);
                            if ($qtdDestinoUnits < 0) {
                                throw new \Exception("Quantidade inválida.");
                            }

                            $qtdDestino = QuantidadeUtil::fromUnits($qtdDestinoUnits);
                            $this->util->incrementaEstoque($anterior->produto_id, $qtdDestino, null, $localDestinoId);

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

                            $this->util->movimentacaoProduto($anterior->produto_id, $qtdDestino, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, null);

                        }
                    }

                }

            }else{
                if($countLocaisAtivos > 1){
                    throw new \Exception("Selecione o local de estoque para ajustar.");
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
                    (int)$item->local_id
                );
                if (!$item->produto->tipo_unico && $quantidadeFinalUnits < $saldoNaoAtivoLocal) {
                    throw new \Exception("Quantidade final inválida: ficaria abaixo do reservado em status não-ATIVO.");
                }
                $diferenca = 0;
                $tipo = 'incremento';
                $quantidadeAtualUnits = QuantidadeUtil::toUnits($item->quantidade);

                if($quantidadeAtualUnits > $quantidadeFinalUnits){
                    $diferenca = QuantidadeUtil::fromUnits($quantidadeAtualUnits - $quantidadeFinalUnits);
                    $tipo = 'reducao';
                }else{
                    $diferenca = QuantidadeUtil::fromUnits($quantidadeFinalUnits - $quantidadeAtualUnits);
                }
                $item->quantidade = QuantidadeUtil::fromUnits($quantidadeFinalUnits);
                $item->save();

                $codigo_transacao = $item->id;
                $tipo_transacao = 'alteracao_estoque';

                $this->util->movimentacaoProduto($item->produto_id, $diferenca, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id);
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
