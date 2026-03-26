<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const DEFAULT_NOME = 'Depósito Padrão';

    public function up()
    {
        if (!Schema::hasTable('depositos')) {
            Schema::create('depositos', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('empresa_id')->index('depositos_empresa_id_foreign');
                $table->unsignedBigInteger('local_id')->index('depositos_local_id_foreign');
                $table->string('nome', 150);
                $table->string('descricao', 255)->nullable();
                $table->boolean('ativo')->default(true);
                $table->boolean('padrao')->default(false);
                $table->timestamps();

                $table->unique(['local_id', 'nome'], 'depositos_local_nome_unique');
                $table->index(['empresa_id', 'local_id', 'ativo'], 'depositos_empresa_local_ativo_idx');

                $table->foreign('empresa_id')->references('id')->on('empresas')->cascadeOnDelete();
                $table->foreign('local_id')->references('id')->on('localizacaos')->cascadeOnDelete();
            });
        }

        $this->backfillDepositosPadrao();
    }

    public function down()
    {
        Schema::dropIfExists('depositos');
    }

    private function backfillDepositosPadrao(): void
    {
        if (!Schema::hasTable('depositos') || !Schema::hasTable('localizacaos')) {
            return;
        }

        DB::table('localizacaos')
            ->select('id', 'empresa_id', 'descricao', 'status')
            ->orderBy('id')
            ->chunkById(200, function ($locais) {
                foreach ($locais as $local) {
                    $descricaoLocal = trim((string)($local->descricao ?? ''));
                    $descricaoDeposito = $descricaoLocal !== ''
                        ? "Depósito padrão vinculado à unidade {$descricaoLocal}"
                        : 'Depósito padrão vinculado à unidade';

                    $existente = DB::table('depositos')
                        ->where('local_id', (int)$local->id)
                        ->where('nome', self::DEFAULT_NOME)
                        ->first();

                    $payload = [
                        'empresa_id' => (int)$local->empresa_id,
                        'local_id' => (int)$local->id,
                        'nome' => self::DEFAULT_NOME,
                        'descricao' => $descricaoDeposito,
                        'ativo' => (int)$local->status === 1,
                        'padrao' => true,
                        'updated_at' => now(),
                    ];

                    if ($existente) {
                        DB::table('depositos')
                            ->where('id', $existente->id)
                            ->update($payload);
                        continue;
                    }

                    $payload['created_at'] = now();
                    DB::table('depositos')->insert($payload);
                }
            });
    }
};
