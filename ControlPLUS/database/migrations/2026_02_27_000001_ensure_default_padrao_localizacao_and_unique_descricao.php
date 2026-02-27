<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    private string $uniqueIndexName = 'localizacaos_empresa_descricao_unique';

    public function up()
    {
        if (!Schema::hasTable('localizacaos')) {
            return;
        }

        DB::statement('UPDATE localizacaos SET descricao = TRIM(descricao)');

        $empresaIds = collect();
        if (Schema::hasTable('empresas')) {
            $empresaIds = $empresaIds->merge(
                DB::table('empresas')->pluck('id')
            );
        }

        $empresaIds = $empresaIds
            ->merge(
                DB::table('localizacaos')
                    ->whereNotNull('empresa_id')
                    ->distinct()
                    ->pluck('empresa_id')
            )
            ->unique()
            ->values();

        foreach ($empresaIds as $empresaId) {
            DB::transaction(function () use ($empresaId) {
                $defaultId = $this->ensureDefaultPadraoForEmpresa((int)$empresaId);
                $this->dedupeDescricoesPorEmpresa((int)$empresaId, $defaultId);
            });
        }

        if (!$this->indexExists('localizacaos', $this->uniqueIndexName)) {
            Schema::table('localizacaos', function (Blueprint $table) {
                $table->unique(['empresa_id', 'descricao'], 'localizacaos_empresa_descricao_unique');
            });
        }
    }

    public function down()
    {
        if (!Schema::hasTable('localizacaos')) {
            return;
        }

        if ($this->indexExists('localizacaos', $this->uniqueIndexName)) {
            Schema::table('localizacaos', function (Blueprint $table) {
                $table->dropUnique('localizacaos_empresa_descricao_unique');
            });
        }
    }

    private function ensureDefaultPadraoForEmpresa(int $empresaId): ?int
    {
        $locais = DB::table('localizacaos')
            ->where('empresa_id', $empresaId)
            ->orderBy('id')
            ->get();

        $normalizadoPadrao = $this->normalizeDescricaoKey('PADRÃO');

        $localPadrao = $locais->first(function ($row) {
            return trim((string)$row->descricao) === 'PADRÃO';
        });

        if (!$localPadrao) {
            $localPadrao = $locais->first(function ($row) use ($normalizadoPadrao) {
                return $this->normalizeDescricaoKey((string)$row->descricao) === $normalizadoPadrao;
            });
        }

        if ($localPadrao) {
            if ((int)($localPadrao->status ?? 0) !== 1) {
                DB::table('localizacaos')
                    ->where('id', $localPadrao->id)
                    ->update([
                        'status' => 1,
                        'updated_at' => now(),
                    ]);
            }
            if (trim((string)$localPadrao->descricao) !== 'PADRÃO') {
                DB::table('localizacaos')
                    ->where('id', $localPadrao->id)
                    ->update([
                        'descricao' => 'PADRÃO',
                        'updated_at' => now(),
                    ]);
            }
            return (int)$localPadrao->id;
        }

        $localLegado = DB::table('localizacaos')
            ->where('empresa_id', $empresaId)
            ->where(function ($query) {
                $query->whereRaw('UPPER(TRIM(descricao)) = ?', ['BL0001'])
                    ->orWhereRaw('UPPER(TRIM(descricao)) = ?', ['PADRAO'])
                    ->orWhereRaw('UPPER(TRIM(descricao)) LIKE ?', ['BL000%'])
                    ->orWhereRaw('UPPER(TRIM(descricao)) LIKE ?', ['LOCAL DE ARMAZENAMENTO%']);
            })
            ->orderBy('id')
            ->first();

        if ($localLegado) {
            DB::table('localizacaos')
                ->where('id', $localLegado->id)
                ->update([
                    'descricao' => 'PADRÃO',
                    'status' => 1,
                    'updated_at' => now(),
                ]);
            return (int)$localLegado->id;
        }

        $primeiroLocal = DB::table('localizacaos')
            ->where('empresa_id', $empresaId)
            ->orderBy('id')
            ->first();

        if ($primeiroLocal) {
            $data = (array)$primeiroLocal;
            unset($data['id'], $data['descricao'], $data['created_at'], $data['updated_at']);
            $data['empresa_id'] = $empresaId;
            $data['descricao'] = 'PADRÃO';
            $data['status'] = 1;
            $data['created_at'] = now();
            $data['updated_at'] = now();

            DB::table('localizacaos')->insert($data);
            return (int)DB::getPdo()->lastInsertId();
        }

        if (!Schema::hasTable('empresas')) {
            return null;
        }

        $empresa = DB::table('empresas')->where('id', $empresaId)->first();
        if (!$empresa) {
            return null;
        }

        DB::table('localizacaos')->insert($this->montaLocalPadraoComEmpresa($empresa));
        return (int)DB::getPdo()->lastInsertId();
    }

    private function montaLocalPadraoComEmpresa(object $empresa): array
    {
        $tributacao = match ($empresa->tributacao) {
            'MEI' => 'MEI',
            'Simples Nacional' => 'Simples Nacional',
            'Simples Nacional, excesso sublimite de receita bruta' => 'Simples Nacional',
            'Regime Normal' => 'Regime Normal',
            default => 'Regime Normal',
        };

        return [
            'empresa_id' => $empresa->id,
            'descricao' => 'PADRÃO',
            'status' => 1,
            'nome' => $empresa->nome,
            'nome_fantasia' => $empresa->nome_fantasia,
            'cpf_cnpj' => $empresa->cpf_cnpj,
            'aut_xml' => $empresa->aut_xml,
            'ie' => $empresa->ie,
            'email' => $empresa->email,
            'celular' => $empresa->celular,
            'arquivo' => $empresa->arquivo,
            'senha' => $empresa->senha,
            'cep' => $empresa->cep,
            'rua' => $empresa->rua,
            'numero' => $empresa->numero,
            'bairro' => $empresa->bairro,
            'complemento' => $empresa->complemento,
            'cidade_id' => $empresa->cidade_id,
            'numero_ultima_nfe_producao' => $empresa->numero_ultima_nfe_producao,
            'numero_ultima_nfe_homologacao' => $empresa->numero_ultima_nfe_homologacao,
            'numero_serie_nfe' => $empresa->numero_serie_nfe,
            'numero_ultima_nfce_producao' => $empresa->numero_ultima_nfce_producao,
            'numero_ultima_nfce_homologacao' => $empresa->numero_ultima_nfce_homologacao,
            'numero_serie_nfce' => $empresa->numero_serie_nfce,
            'numero_ultima_cte_producao' => $empresa->numero_ultima_cte_producao,
            'numero_ultima_cte_homologacao' => $empresa->numero_ultima_cte_homologacao,
            'numero_serie_cte' => $empresa->numero_serie_cte,
            'numero_ultima_mdfe_producao' => $empresa->numero_ultima_mdfe_producao,
            'numero_ultima_mdfe_homologacao' => $empresa->numero_ultima_mdfe_homologacao,
            'numero_serie_mdfe' => $empresa->numero_serie_mdfe,
            'numero_ultima_nfse' => $empresa->numero_ultima_nfse,
            'numero_serie_nfse' => $empresa->numero_serie_nfse,
            'csc' => $empresa->csc,
            'csc_id' => $empresa->csc_id,
            'ambiente' => $empresa->ambiente ?? 2,
            'tributacao' => $tributacao,
            'token_nfse' => $empresa->token_nfse,
            'logo' => $empresa->logo ?? '',
            'perc_ap_cred' => $empresa->perc_ap_cred ?? 0,
            'mensagem_aproveitamento_credito' => $empresa->mensagem_aproveitamento_credito,
            'token_whatsapp' => null,
            'substituto_tributario' => $empresa->substituto_tributario ?? false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function dedupeDescricoesPorEmpresa(int $empresaId, ?int $defaultId = null): void
    {
        $registros = DB::table('localizacaos')
            ->where('empresa_id', $empresaId)
            ->orderBy('id')
            ->get();

        if ($registros->count() <= 1) {
            return;
        }

        $grupos = [];
        foreach ($registros as $registro) {
            $key = $this->normalizeDescricaoKey((string)$registro->descricao);
            if (!isset($grupos[$key])) {
                $grupos[$key] = [];
            }
            $grupos[$key][] = $registro;
        }

        foreach ($grupos as $key => $itens) {
            if (count($itens) <= 1) {
                continue;
            }

            $idMantido = (int)$itens[0]->id;
            if ($defaultId !== null) {
                foreach ($itens as $item) {
                    if ((int)$item->id === (int)$defaultId) {
                        $idMantido = (int)$defaultId;
                        break;
                    }
                }
            }

            $descricaoBase = trim((string)$itens[0]->descricao);
            if ($descricaoBase === '') {
                $descricaoBase = 'LOCAL';
            }

            $sufixo = 2;
            foreach ($itens as $item) {
                if ((int)$item->id === $idMantido) {
                    continue;
                }

                $novaDescricao = $this->geraDescricaoUnica($empresaId, $descricaoBase, $sufixo);
                DB::table('localizacaos')
                    ->where('id', $item->id)
                    ->update([
                        'descricao' => $novaDescricao,
                        'updated_at' => now(),
                    ]);
                $sufixo++;
            }
        }
    }

    private function geraDescricaoUnica(int $empresaId, string $descricaoBase, int $sufixoInicial): string
    {
        $base = trim($descricaoBase);
        if ($base === '') {
            $base = 'LOCAL';
        }

        $sufixo = $sufixoInicial;
        while (true) {
            $complemento = ' (' . $sufixo . ')';
            $limiteBase = 150 - mb_strlen($complemento);
            $descricao = mb_substr($base, 0, max(1, $limiteBase)) . $complemento;

            $descricaoKey = $this->normalizeDescricaoKey($descricao);
            $existe = DB::table('localizacaos')
                ->where('empresa_id', $empresaId)
                ->get(['descricao'])
                ->contains(function ($row) use ($descricaoKey) {
                    return $this->normalizeDescricaoKey((string)$row->descricao) === $descricaoKey;
                });

            if (!$existe) {
                return $descricao;
            }

            $sufixo++;
        }
    }

    private function normalizeDescricaoKey(string $descricao): string
    {
        $descricao = trim($descricao);
        if ($descricao === '') {
            return '';
        }

        $descricao = Str::ascii($descricao);
        $descricao = mb_strtoupper($descricao, 'UTF-8');
        $descricao = preg_replace('/\s+/', ' ', $descricao);

        return trim((string)$descricao);
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $result = DB::select(
            'SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ? LIMIT 1',
            [$table, $indexName]
        );

        return !empty($result);
    }
};
