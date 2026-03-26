<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('nves', function (Blueprint $table) {
            $table->unsignedBigInteger('deposito_id')->nullable()->after('local_id');
            $table->index('deposito_id', 'nves_deposito_id_index');
            $table->foreign('deposito_id', 'nves_deposito_id_foreign')->references('id')->on('depositos')->nullOnDelete();
        });

        $localIds = DB::table('nves')
            ->whereNotNull('local_id')
            ->whereNull('deposito_id')
            ->distinct()
            ->pluck('local_id')
            ->map(function ($id) {
                return (int)$id;
            })
            ->filter()
            ->values();

        if ($localIds->isEmpty()) {
            return;
        }

        $now = now();
        $depositoMap = [];

        $locais = DB::table('localizacaos')
            ->whereIn('id', $localIds->all())
            ->select('id', 'empresa_id', 'descricao', 'status')
            ->get();

        foreach ($locais as $local) {
            $deposito = DB::table('depositos')
                ->where('local_id', (int)$local->id)
                ->where('nome', 'Depósito Padrão')
                ->first();

            if (!$deposito) {
                $descricaoLocal = trim((string)($local->descricao ?? ''));
                $descricaoDeposito = $descricaoLocal !== ''
                    ? "Depósito padrão vinculado à unidade {$descricaoLocal}"
                    : 'Depósito padrão vinculado à unidade';

                $depositoId = DB::table('depositos')->insertGetId([
                    'empresa_id' => (int)$local->empresa_id,
                    'local_id' => (int)$local->id,
                    'nome' => 'Depósito Padrão',
                    'descricao' => $descricaoDeposito,
                    'ativo' => (int)$local->status === 1 ? 1 : 0,
                    'padrao' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } else {
                $depositoId = (int)$deposito->id;
            }

            $depositoMap[(int)$local->id] = $depositoId;
        }

        foreach ($depositoMap as $localId => $depositoId) {
            DB::table('nves')
                ->where('local_id', $localId)
                ->whereNull('deposito_id')
                ->update(['deposito_id' => $depositoId]);
        }
    }

    public function down()
    {
        Schema::table('nves', function (Blueprint $table) {
            $table->dropForeign('nves_deposito_id_foreign');
            $table->dropIndex('nves_deposito_id_index');
            $table->dropColumn('deposito_id');
        });
    }
};
