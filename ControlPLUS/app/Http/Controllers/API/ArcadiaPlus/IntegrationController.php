<?php

namespace App\Http\Controllers\API\ArcadiaPlus;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\ContaReceber;
use App\Models\Empresa;
use App\Models\Fornecedor;
use App\Models\Nfce;
use App\Models\Nfe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IntegrationController extends Controller
{
    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'version' => 'arcadia-plus-db-v1',
            'service' => 'controlplus',
        ]);
    }

    public function listarEmpresas(Request $request): JsonResponse
    {
        $empresaId = $this->resolveEmpresaId($request);

        $query = Empresa::query()->orderByDesc('id');
        if ($empresaId) {
            $query->where('id', $empresaId);
        }

        $data = $query->get()->map(function (Empresa $empresa) {
            return [
                'id' => $empresa->id,
                'nome' => $empresa->nome,
                'nome_fantasia' => $empresa->nome_fantasia,
                'cpf_cnpj' => $empresa->cpf_cnpj,
                'email' => $empresa->email,
                'celular' => $empresa->celular,
                'status' => (bool) $empresa->status,
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

        if ($request->filled('cpf_cnpj')) {
            $cpfCnpj = preg_replace('/\D+/', '', (string) $request->query('cpf_cnpj'));
            $query->whereRaw("REGEXP_REPLACE(cpf_cnpj, '[^0-9]', '') = ?", [$cpfCnpj]);
        }

        $data = $query->limit(1000)->get()->map(function (Cliente $cliente) {
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
                'status' => (int) ($cliente->status ?? 1) === 1 ? 'ativo' : 'inativo',
            ];
        });

        return response()->json($data);
    }

    public function criarCliente(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cpf_cnpj' => 'nullable|string|max:30',
            'telefone' => 'nullable|string|max:30',
            'celular' => 'nullable|string|max:30',
            'email' => 'nullable|string|max:255',
            'cep' => 'nullable|string|max:20',
            'logradouro' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:120',
            'cidade' => 'nullable|string|max:120',
            'uf' => 'nullable|string|max:2',
        ]);

        $empresaId = $this->resolveEmpresaId($request) ?? $this->fallbackEmpresaId();
        if (!$empresaId) {
            return response()->json(['message' => 'Nenhuma empresa encontrada para vincular o cliente'], 422);
        }

        $cliente = Cliente::create([
            'empresa_id' => $empresaId,
            'razao_social' => $payload['razao_social'],
            'nome_fantasia' => $payload['nome_fantasia'] ?? $payload['razao_social'],
            'cpf_cnpj' => $payload['cpf_cnpj'] ?? null,
            'telefone' => $payload['celular'] ?? ($payload['telefone'] ?? null),
            'email' => $payload['email'] ?? null,
            'cep' => $payload['cep'] ?? '00000000',
            'rua' => $payload['logradouro'] ?? 'NAO INFORMADO',
            'numero' => $payload['numero'] ?? 'S/N',
            'bairro' => $payload['bairro'] ?? 'CENTRO',
            'status' => 1,
        ]);

        return response()->json([
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
            'status' => 'ativo',
        ], 201);
    }

    public function buscarCliente(Request $request, int $id): JsonResponse
    {
        $empresaId = $this->resolveEmpresaId($request);

        $query = Cliente::query()->with('cidade:id,nome,uf')->where('id', $id);
        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        $cliente = $query->first();
        if (!$cliente) {
            return response()->json(['message' => 'Cliente nao encontrado'], 404);
        }

        return response()->json([
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
            'status' => (int) ($cliente->status ?? 1) === 1 ? 'ativo' : 'inativo',
        ]);
    }

    public function atualizarCliente(Request $request, int $id): JsonResponse
    {
        $payload = $request->validate([
            'razao_social' => 'sometimes|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cpf_cnpj' => 'nullable|string|max:30',
            'telefone' => 'nullable|string|max:30',
            'celular' => 'nullable|string|max:30',
            'email' => 'nullable|string|max:255',
            'cep' => 'nullable|string|max:20',
            'logradouro' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:120',
            'status' => 'nullable|string|max:20',
        ]);

        $empresaId = $this->resolveEmpresaId($request);
        $query = Cliente::query()->where('id', $id);
        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        $cliente = $query->first();
        if (!$cliente) {
            return response()->json(['message' => 'Cliente nao encontrado'], 404);
        }

        $cliente->fill([
            'razao_social' => $payload['razao_social'] ?? $cliente->razao_social,
            'nome_fantasia' => $payload['nome_fantasia'] ?? $cliente->nome_fantasia,
            'cpf_cnpj' => $payload['cpf_cnpj'] ?? $cliente->cpf_cnpj,
            'telefone' => $payload['celular'] ?? ($payload['telefone'] ?? $cliente->telefone),
            'email' => $payload['email'] ?? $cliente->email,
            'cep' => $payload['cep'] ?? $cliente->cep,
            'rua' => $payload['logradouro'] ?? $cliente->rua,
            'numero' => $payload['numero'] ?? $cliente->numero,
            'bairro' => $payload['bairro'] ?? $cliente->bairro,
            'status' => ($payload['status'] ?? 'ativo') === 'ativo' ? 1 : 0,
        ]);
        $cliente->save();

        return $this->buscarCliente($request, $cliente->id);
    }

    public function criarFornecedor(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cpf_cnpj' => 'nullable|string|max:30',
            'telefone' => 'nullable|string|max:30',
            'celular' => 'nullable|string|max:30',
            'email' => 'nullable|string|max:255',
        ]);

        $empresaId = $this->resolveEmpresaId($request) ?? $this->fallbackEmpresaId();
        if (!$empresaId) {
            return response()->json(['message' => 'Nenhuma empresa encontrada para vincular o fornecedor'], 422);
        }

        $fornecedor = Fornecedor::create([
            'empresa_id' => $empresaId,
            'razao_social' => $payload['razao_social'],
            'nome_fantasia' => $payload['nome_fantasia'] ?? $payload['razao_social'],
            'cpf_cnpj' => $payload['cpf_cnpj'] ?? '',
            'telefone' => $payload['celular'] ?? ($payload['telefone'] ?? null),
            'email' => $payload['email'] ?? null,
            'cep' => '00000000',
            'rua' => 'NAO INFORMADO',
            'numero' => 'S/N',
            'bairro' => 'CENTRO',
        ]);

        return response()->json([
            'id' => $fornecedor->id,
            'razao_social' => $fornecedor->razao_social,
            'nome_fantasia' => $fornecedor->nome_fantasia,
            'cpf_cnpj' => $fornecedor->cpf_cnpj,
            'telefone' => $fornecedor->telefone,
            'email' => $fornecedor->email,
            'status' => 'ativo',
        ], 201);
    }

    public function listarProdutos(Request $request): JsonResponse
    {
        $empresaId = $this->resolveEmpresaId($request);

        $query = DB::table('produtos as p')
            ->leftJoin('categoria_produtos as c', 'c.id', '=', 'p.categoria_id')
            ->leftJoin('estoques as e', 'e.produto_id', '=', 'p.id')
            ->selectRaw('p.id, p.referencia as codigo, p.nome, p.descricao, p.unidade, p.valor_compra, p.valor_unitario, p.estoque_minimo, p.ncm, p.codigo_barras, p.status, c.nome as categoria_nome, COALESCE(SUM(e.quantidade), 0) as estoque_atual')
            ->groupBy('p.id', 'p.referencia', 'p.nome', 'p.descricao', 'p.unidade', 'p.valor_compra', 'p.valor_unitario', 'p.estoque_minimo', 'p.ncm', 'p.codigo_barras', 'p.status', 'c.nome')
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
                'unidade' => $produto->unidade,
                'valor_compra' => (float) ($produto->valor_compra ?? 0),
                'valor_unitario' => (float) ($produto->valor_unitario ?? 0),
                'estoque_atual' => (float) ($produto->estoque_atual ?? 0),
                'estoque_minimo' => (float) ($produto->estoque_minimo ?? 0),
                'ncm' => $produto->ncm,
                'codigo_barras' => $produto->codigo_barras,
                'status' => (bool) $produto->status,
                'categoria' => [
                    'nome' => $produto->categoria_nome,
                ],
            ];
        });

        return response()->json($data);
    }

    public function criarVenda(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'cliente_id' => 'nullable|integer',
            'natureza_operacao_id' => 'nullable|integer',
            'valor_total' => 'required|numeric',
            'desconto' => 'nullable|numeric',
            'forma_pagamento' => 'nullable|string|max:60',
            'observacao' => 'nullable|string|max:500',
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'nullable|integer',
            'itens.*.quantidade' => 'required|numeric',
            'itens.*.valor_unitario' => 'required|numeric',
            'itens.*.valor_total' => 'required|numeric',
            'faturar' => 'nullable|array',
        ]);

        $empresaId = $this->resolveEmpresaId($request) ?? $this->fallbackEmpresaId();
        if (!$empresaId) {
            return response()->json(['message' => 'Nenhuma empresa encontrada para vincular a venda'], 422);
        }

        $formaPagamento = strtolower((string) ($payload['forma_pagamento'] ?? ''));
        $tipoPagamento = $this->mapFormaPagamentoToCodigo($formaPagamento);

        $nfce = Nfce::create([
            'empresa_id' => $empresaId,
            'cliente_id' => $payload['cliente_id'] ?? null,
            'total' => (float) $payload['valor_total'],
            'desconto' => (float) ($payload['desconto'] ?? 0),
            'tipo_pagamento' => $tipoPagamento,
            'observacao' => $payload['observacao'] ?? 'Venda criada via integracao Arcadia',
            'estado' => 'aprovado',
            'ambiente' => 2,
        ]);

        return response()->json([
            'id' => $nfce->id,
            'cliente_id' => $nfce->cliente_id,
            'valor_total' => (float) ($nfce->total ?? 0),
            'desconto' => (float) ($nfce->desconto ?? 0),
            'forma_pagamento' => $nfce->tipo_pagamento,
            'status' => $nfce->estado,
            'created_at' => optional($nfce->created_at)->toISOString(),
        ], 201);
    }

    public function consultarEstoque(Request $request): JsonResponse
    {
        $empresaId = $this->resolveEmpresaId($request);

        $query = DB::table('produtos as p')
            ->leftJoin('estoques as e', 'e.produto_id', '=', 'p.id')
            ->selectRaw('p.id as produto_id, p.nome, COALESCE(SUM(e.quantidade), 0) as estoque_atual, p.estoque_minimo')
            ->groupBy('p.id', 'p.nome', 'p.estoque_minimo')
            ->orderByDesc('p.id');

        if ($empresaId) {
            $query->where('p.empresa_id', $empresaId);
        }

        $data = $query->limit(1000)->get()->map(function ($item) {
            $atual = (float) ($item->estoque_atual ?? 0);
            $minimo = (float) ($item->estoque_minimo ?? 0);

            return [
                'produto_id' => (int) $item->produto_id,
                'nome' => $item->nome,
                'estoque_atual' => $atual,
                'estoque_minimo' => $minimo,
                'status' => $atual <= $minimo ? 'baixo' : 'ok',
            ];
        });

        return response()->json($data);
    }

    public function listarContasReceber(Request $request): JsonResponse
    {
        $empresaId = $this->resolveEmpresaId($request);

        $query = ContaReceber::query()->orderByDesc('id');
        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        $data = $query->limit(1000)->get()->map(function (ContaReceber $conta) {
            return [
                'id' => $conta->id,
                'cliente_id' => $conta->cliente_id,
                'venda_id' => $conta->nfce_id ?? $conta->nfe_id,
                'data_vencimento' => $conta->data_vencimento,
                'valor' => (float) ($conta->valor_integral ?? 0),
                'valor_recebido' => (float) ($conta->valor_recebido ?? 0),
                'status' => (int) ($conta->status ?? 0) === 1 ? 'recebido' : 'pendente',
                'forma_pagamento' => $conta->tipo_pagamento,
            ];
        });

        return response()->json($data);
    }

    public function emitirNfe(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'itens' => 'required|array|min:1',
            'venda_id' => 'nullable|integer',
            'cliente_id' => 'nullable|integer',
            'pagamentos' => 'nullable|array',
        ]);

        $chave = $this->buildFiscalKey('55');

        if (!empty($payload['venda_id'])) {
            $nfe = Nfe::find((int) $payload['venda_id']);
            if ($nfe) {
                $nfe->fill(['chave' => $chave, 'estado' => 'autorizado'])->save();
            }
        }

        return response()->json([
            'chave' => $chave,
            'status' => 'autorizado',
            'tipo' => 'nfe',
        ]);
    }

    public function emitirNfce(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'itens' => 'required|array|min:1',
            'venda_id' => 'nullable|integer',
            'cliente_id' => 'nullable|integer',
            'pagamentos' => 'nullable|array',
        ]);

        $chave = $this->buildFiscalKey('65');

        if (!empty($payload['venda_id'])) {
            $nfce = Nfce::find((int) $payload['venda_id']);
            if ($nfce) {
                $nfce->fill(['chave' => $chave, 'estado' => 'autorizado'])->save();
            }
        }

        return response()->json([
            'chave' => $chave,
            'status' => 'autorizado',
            'tipo' => 'nfce',
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

    private function fallbackEmpresaId(): ?int
    {
        $empresa = Empresa::query()->where('status', 1)->orderBy('id')->first();
        return $empresa ? (int) $empresa->id : null;
    }

    private function mapFormaPagamentoToCodigo(string $forma): string
    {
        $normalizada = strtolower(trim($forma));

        if (in_array($normalizada, ['pix', '17'], true)) {
            return '17';
        }

        if (in_array($normalizada, ['credito', 'cartao_credito', 'cartao de credito', '03'], true)) {
            return '03';
        }

        if (in_array($normalizada, ['debito', 'cartao_debito', 'cartao de debito', '04'], true)) {
            return '04';
        }

        return '01';
    }

    private function buildFiscalKey(string $modelo): string
    {
        return '35' . now()->format('ym') . '12345678000190' . $modelo . str_pad((string) random_int(1, 999999999), 9, '0', STR_PAD_LEFT);
    }
}
