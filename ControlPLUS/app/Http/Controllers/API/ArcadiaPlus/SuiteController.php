<?php

namespace App\Http\Controllers\API\ArcadiaPlus;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\ContaPagar;
use App\Models\ContaReceber;
use App\Models\Empresa;
use App\Models\Fornecedor;
use App\Models\Nfce;
use App\Models\Nfe;
use App\Models\Plano;
use App\Models\PlanoEmpresa;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuiteController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $empresaId = $this->resolveEmpresaId($request);

        $empresasQuery = Empresa::query();
        if ($empresaId) {
            $empresasQuery->where('id', $empresaId);
        }

        $usuariosQuery = User::query();
        $nfesQuery = Nfe::query()->where('tpNF', 1);
        $nfcesQuery = Nfce::query();
        $mrrQuery = PlanoEmpresa::query();

        if ($empresaId) {
            $usuariosQuery->whereHas('empresa', function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            });
            $nfesQuery->where('empresa_id', $empresaId);
            $nfcesQuery->where('empresa_id', $empresaId);
            $mrrQuery->where('empresa_id', $empresaId);
        }

        return response()->json([
            'empresas' => (int) $empresasQuery->count(),
            'planos' => (int) Plano::query()->count(),
            'mrr' => (float) $mrrQuery->sum('valor'),
            'usuarios' => (int) $usuariosQuery->count(),
            'nfes' => (int) $nfesQuery->count(),
            'nfces' => (int) $nfcesQuery->count(),
        ]);
    }

    public function listarPlanos(): JsonResponse
    {
        return response()->json(
            Plano::query()->orderByDesc('id')->get()
        );
    }

    public function criarPlano(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'nome' => 'required|string|max:120',
            'descricao' => 'nullable|string',
            'maximo_nfes' => 'required|integer|min:0',
            'maximo_nfces' => 'required|integer|min:0',
            'maximo_ctes' => 'nullable|integer|min:0',
            'maximo_mdfes' => 'nullable|integer|min:0',
            'maximo_usuarios' => 'nullable|integer|min:1',
            'maximo_locais' => 'nullable|integer|min:1',
            'valor' => 'required',
            'valor_implantacao' => 'nullable',
            'intervalo_dias' => 'nullable|integer|min:1',
            'status' => 'required|boolean',
            'visivel_clientes' => 'nullable|boolean',
            'visivel_contadores' => 'nullable|boolean',
            'fiscal' => 'nullable|boolean',
            'modulos' => 'nullable|string',
        ]);

        $payload['descricao'] = $payload['descricao'] ?? '';
        $payload['maximo_ctes'] = $payload['maximo_ctes'] ?? 0;
        $payload['maximo_mdfes'] = $payload['maximo_mdfes'] ?? 0;
        $payload['maximo_usuarios'] = $payload['maximo_usuarios'] ?? 1;
        $payload['maximo_locais'] = $payload['maximo_locais'] ?? 1;
        $payload['intervalo_dias'] = $payload['intervalo_dias'] ?? 30;
        $payload['visivel_clientes'] = $payload['visivel_clientes'] ?? true;
        $payload['visivel_contadores'] = $payload['visivel_contadores'] ?? false;
        $payload['fiscal'] = $payload['fiscal'] ?? true;
        $payload['imagem'] = '';

        $plano = Plano::create($payload);
        return response()->json($plano, 201);
    }

    public function atualizarPlano(Request $request, int $id): JsonResponse
    {
        $plano = Plano::find($id);
        if (!$plano) {
            return response()->json(['message' => 'Plano nao encontrado'], 404);
        }

        $plano->fill($request->all());
        $plano->save();

        return response()->json($plano);
    }

    public function removerPlano(int $id): JsonResponse
    {
        $plano = Plano::find($id);
        if (!$plano) {
            return response()->json(['message' => 'Plano nao encontrado'], 404);
        }

        $plano->delete();
        return response()->json(['success' => true]);
    }

    public function listarEmpresas(Request $request): JsonResponse
    {
        $empresaId = $this->resolveEmpresaId($request);

        $query = Empresa::query()->with(['plano' => function ($q) {
            $q->with('plano');
        }])->orderByDesc('id');

        if ($empresaId) {
            $query->where('id', $empresaId);
        }

        $data = $query->get()->map(function (Empresa $empresa) {
            $plano = optional(optional($empresa->plano)->plano);

            return [
                'id' => $empresa->id,
                'nome' => $empresa->nome,
                'nome_fantasia' => $empresa->nome_fantasia,
                'cpf_cnpj' => $empresa->cpf_cnpj,
                'email' => $empresa->email,
                'celular' => $empresa->celular,
                'status' => (bool) $empresa->status,
                'plano_id' => optional($empresa->plano)->plano_id,
                'plano' => $plano ? [
                    'id' => $plano->id,
                    'nome' => $plano->nome,
                    'descricao' => $plano->descricao,
                    'maximo_nfes' => (int) ($plano->maximo_nfes ?? 0),
                    'maximo_nfces' => (int) ($plano->maximo_nfces ?? 0),
                    'maximo_ctes' => (int) ($plano->maximo_ctes ?? 0),
                    'maximo_mdfes' => (int) ($plano->maximo_mdfes ?? 0),
                    'maximo_usuarios' => (int) ($plano->maximo_usuarios ?? 0),
                    'maximo_locais' => (int) ($plano->maximo_locais ?? 0),
                    'valor' => (string) ($plano->valor ?? '0'),
                    'valor_implantacao' => (string) ($plano->valor_implantacao ?? '0'),
                    'intervalo_dias' => (int) ($plano->intervalo_dias ?? 30),
                    'visivel_clientes' => (bool) ($plano->visivel_clientes ?? false),
                    'visivel_contadores' => (bool) ($plano->visivel_contadores ?? false),
                    'status' => (bool) ($plano->status ?? false),
                    'fiscal' => (bool) ($plano->fiscal ?? true),
                    'modulos' => (string) ($plano->modulos ?? ''),
                ] : null,
            ];
        });

        return response()->json($data);
    }

    public function criarEmpresa(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'nome' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cpf_cnpj' => 'required|string|max:30',
            'email' => 'nullable|string|max:255',
            'celular' => 'nullable|string|max:30',
            'plano_id' => 'nullable|integer',
            'status' => 'required|boolean',
        ]);

        $empresa = Empresa::create([
            'nome' => $payload['nome'],
            'nome_fantasia' => $payload['nome_fantasia'] ?? null,
            'cpf_cnpj' => $payload['cpf_cnpj'],
            'email' => $payload['email'] ?? null,
            'celular' => $payload['celular'] ?? null,
            'status' => $payload['status'],
            'ambiente' => 2,
            'tributacao' => 'Simples Nacional',
        ]);

        if (!empty($payload['plano_id'])) {
            PlanoEmpresa::create([
                'empresa_id' => $empresa->id,
                'plano_id' => $payload['plano_id'],
                'data_expiracao' => now()->addDays(30)->toDateString(),
                'valor' => (float) (optional(Plano::find($payload['plano_id']))->valor ?? 0),
                'forma_pagamento' => 'PIX',
            ]);
        }

        return $this->listarEmpresas(new Request(['empresa_id' => $empresa->id]))->setStatusCode(201);
    }

    public function atualizarEmpresa(Request $request, int $id): JsonResponse
    {
        $empresa = Empresa::find($id);
        if (!$empresa) {
            return response()->json(['message' => 'Empresa nao encontrada'], 404);
        }

        $empresa->fill($request->only([
            'nome', 'nome_fantasia', 'cpf_cnpj', 'email', 'celular', 'status',
        ]));
        $empresa->save();

        if ($request->has('plano_id')) {
            $planoId = $request->input('plano_id');
            $latest = PlanoEmpresa::query()->where('empresa_id', $empresa->id)->orderByDesc('id')->first();

            if ($planoId) {
                if ($latest) {
                    $latest->fill([
                        'plano_id' => $planoId,
                        'valor' => (float) (optional(Plano::find($planoId))->valor ?? $latest->valor),
                    ])->save();
                } else {
                    PlanoEmpresa::create([
                        'empresa_id' => $empresa->id,
                        'plano_id' => $planoId,
                        'data_expiracao' => now()->addDays(30)->toDateString(),
                        'valor' => (float) (optional(Plano::find($planoId))->valor ?? 0),
                        'forma_pagamento' => 'PIX',
                    ]);
                }
            }
        }

        return $this->listarEmpresas(new Request(['empresa_id' => $empresa->id]));
    }

    public function removerEmpresa(int $id): JsonResponse
    {
        $empresa = Empresa::find($id);
        if (!$empresa) {
            return response()->json(['message' => 'Empresa nao encontrada'], 404);
        }

        PlanoEmpresa::query()->where('empresa_id', $empresa->id)->delete();
        $empresa->delete();

        return response()->json(['success' => true]);
    }

    public function listarUsuarios(Request $request): JsonResponse
    {
        $empresaId = $this->resolveEmpresaId($request);

        $query = User::query()
            ->with(['empresa.empresa'])
            ->orderByDesc('id');

        if ($empresaId) {
            $query->whereHas('empresa', function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            });
        }

        $data = $query->get()->map(function (User $user) {
            $empresa = optional(optional($user->empresa)->empresa);

            return [
                'id' => $user->id,
                'nome' => $user->name,
                'email' => $user->email,
                'login' => strstr((string) $user->email, '@', true) ?: $user->email,
                'admin' => (bool) $user->admin,
                'status' => (bool) $user->status,
                'empresa' => $empresa ? [
                    'id' => $empresa->id,
                    'nome' => $empresa->nome,
                    'nome_fantasia' => $empresa->nome_fantasia,
                ] : null,
            ];
        });

        return response()->json($data);
    }

    public function listarClientes(Request $request): JsonResponse
    {
        $empresaId = $this->resolveEmpresaId($request);

        $query = Cliente::query()->with('cidade:id,nome,uf')->orderByDesc('id');
        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        $data = $query->limit(500)->get()->map(function (Cliente $cliente) {
            return [
                'id' => $cliente->id,
                'razao_social' => $cliente->razao_social,
                'nome_fantasia' => $cliente->nome_fantasia,
                'cpf_cnpj' => $cliente->cpf_cnpj,
                'telefone' => $cliente->telefone,
                'celular' => $cliente->telefone,
                'email' => $cliente->email,
                'cep' => $cliente->cep,
                'logradouro' => $cliente->rua,
                'numero' => $cliente->numero,
                'bairro' => $cliente->bairro,
                'cidade' => optional($cliente->cidade)->nome,
                'uf' => optional($cliente->cidade)->uf,
                'status' => $cliente->status ? 'ativo' : 'inativo',
            ];
        });

        return response()->json($data);
    }

    public function listarFornecedores(Request $request): JsonResponse
    {
        $empresaId = $this->resolveEmpresaId($request);

        $query = Fornecedor::query()->with('cidade:id,nome,uf')->orderByDesc('id');
        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        $data = $query->limit(500)->get()->map(function (Fornecedor $fornecedor) {
            return [
                'id' => $fornecedor->id,
                'razao_social' => $fornecedor->razao_social,
                'nome_fantasia' => $fornecedor->nome_fantasia,
                'cpf_cnpj' => $fornecedor->cpf_cnpj,
                'telefone' => $fornecedor->telefone,
                'email' => $fornecedor->email,
                'cep' => $fornecedor->cep,
                'logradouro' => $fornecedor->rua,
                'numero' => $fornecedor->numero,
                'bairro' => $fornecedor->bairro,
                'cidade' => optional($fornecedor->cidade)->nome,
                'uf' => optional($fornecedor->cidade)->uf,
                'status' => 'ativo',
            ];
        });

        return response()->json($data);
    }

    public function listarProdutos(Request $request): JsonResponse
    {
        $empresaId = $this->resolveEmpresaId($request);

        $query = DB::table('produtos as p')
            ->leftJoin('categoria_produtos as c', 'c.id', '=', 'p.categoria_id')
            ->leftJoin('estoques as e', 'e.produto_id', '=', 'p.id')
            ->selectRaw('p.id, p.nome, p.referencia as codigo, p.descricao, p.unidade, p.valor_compra, p.valor_unitario, p.estoque_minimo, p.ncm, p.codigo_barras, p.status, c.nome as categoria_nome, COALESCE(SUM(e.quantidade), 0) as estoque_atual')
            ->groupBy('p.id', 'p.nome', 'p.referencia', 'p.descricao', 'p.unidade', 'p.valor_compra', 'p.valor_unitario', 'p.estoque_minimo', 'p.ncm', 'p.codigo_barras', 'p.status', 'c.nome')
            ->orderByDesc('p.id');

        if ($empresaId) {
            $query->where('p.empresa_id', $empresaId);
        }

        $data = $query->limit(1000)->get()->map(function ($produto) {
            return [
                'id' => (int) $produto->id,
                'codigo' => $produto->codigo,
                'nome' => $produto->nome,
                'descricao' => $produto->descricao,
                'categoria' => [
                    'nome' => $produto->categoria_nome,
                ],
                'unidade' => $produto->unidade,
                'valor_compra' => (float) ($produto->valor_compra ?? 0),
                'valor_unitario' => (float) ($produto->valor_unitario ?? 0),
                'estoque_atual' => (float) ($produto->estoque_atual ?? 0),
                'estoque_minimo' => (float) ($produto->estoque_minimo ?? 0),
                'ncm' => $produto->ncm,
                'codigo_barras' => $produto->codigo_barras,
                'status' => (bool) $produto->status,
            ];
        });

        return response()->json($data);
    }

    public function listarVendas(Request $request): JsonResponse
    {
        $empresaId = $this->resolveEmpresaId($request);

        $nfces = Nfce::query()
            ->when($empresaId, function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->orderByDesc('id')
            ->limit(300)
            ->get(['id', 'cliente_id', 'total', 'desconto', 'tipo_pagamento', 'estado', 'created_at'])
            ->map(function (Nfce $item) {
                return [
                    'id' => $item->id,
                    'cliente_id' => $item->cliente_id,
                    'valor_total' => (float) ($item->total ?? 0),
                    'desconto' => (float) ($item->desconto ?? 0),
                    'forma_pagamento' => $item->tipo_pagamento,
                    'status' => $item->estado,
                    'created_at' => optional($item->created_at)->toISOString(),
                    'origem' => 'nfce',
                ];
            });

        $nfes = Nfe::query()
            ->where('tpNF', 1)
            ->when($empresaId, function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->orderByDesc('id')
            ->limit(300)
            ->get(['id', 'cliente_id', 'total', 'desconto', 'tipo_pagamento', 'estado', 'created_at'])
            ->map(function (Nfe $item) {
                return [
                    'id' => $item->id,
                    'cliente_id' => $item->cliente_id,
                    'valor_total' => (float) ($item->total ?? 0),
                    'desconto' => (float) ($item->desconto ?? 0),
                    'forma_pagamento' => $item->tipo_pagamento,
                    'status' => $item->estado,
                    'created_at' => optional($item->created_at)->toISOString(),
                    'origem' => 'nfe',
                ];
            });

        $data = $nfces->concat($nfes)
            ->sortByDesc('created_at')
            ->values()
            ->take(500);

        return response()->json($data);
    }

    public function dashboard(Request $request): JsonResponse
    {
        $empresaId = $this->resolveEmpresaId($request);
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        $vendasNfce = Nfce::query()
            ->where('estado', '!=', 'cancelado')
            ->when($empresaId, function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->whereBetween('created_at', [$start, $end])
            ->sum('total');

        $vendasNfe = Nfe::query()
            ->where('tpNF', 1)
            ->where('estado', '!=', 'cancelado')
            ->when($empresaId, function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->whereBetween('created_at', [$start, $end])
            ->sum('total');

        $compras = Nfe::query()
            ->where('tpNF', 0)
            ->when($empresaId, function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->whereBetween('created_at', [$start, $end])
            ->sum('total');

        $aReceber = ContaReceber::query()
            ->where('status', 0)
            ->when($empresaId, function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->sum('valor_integral');

        $aPagar = ContaPagar::query()
            ->where('status', 0)
            ->when($empresaId, function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->sum('valor_integral');

        $nfeMes = Nfe::query()
            ->where('tpNF', 1)
            ->when($empresaId, function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $nfceMes = Nfce::query()
            ->when($empresaId, function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->whereBetween('created_at', [$start, $end])
            ->count();

        return response()->json([
            'vendas_dia' => (float) ($vendasNfce + $vendasNfe),
            'vendas_mes' => (float) ($vendasNfce + $vendasNfe),
            'compras_mes' => (float) $compras,
            'contas_receber' => (float) $aReceber,
            'contas_pagar' => (float) $aPagar,
            'nfe_mes' => (int) $nfeMes,
            'nfce_mes' => (int) $nfceMes,
        ]);
    }

    private function resolveEmpresaId(Request $request): ?int
    {
        $fromHeader = (int) $request->header('X-Empresa-Id');
        if ($fromHeader > 0) {
            return $fromHeader;
        }

        $fromQuery = (int) $request->query('empresa_id');
        if ($fromQuery > 0) {
            return $fromQuery;
        }

        $fromBody = (int) $request->input('empresa_id');
        if ($fromBody > 0) {
            return $fromBody;
        }

        return null;
    }
}
