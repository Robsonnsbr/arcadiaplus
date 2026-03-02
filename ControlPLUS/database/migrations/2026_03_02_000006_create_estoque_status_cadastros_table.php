<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('estoque_status_cadastros')) {
            Schema::create('estoque_status_cadastros', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('empresa_id');
                $table->string('status_key', 40);
                $table->string('descricao', 80);
                $table->boolean('is_system')->default(false);
                $table->boolean('ativo')->default(true);
                $table->timestamps();

                $table->unique(['empresa_id', 'status_key'], 'estoque_status_cadastros_empresa_status_unique');
                $table->index(['empresa_id', 'ativo'], 'estoque_status_cadastros_empresa_ativo_idx');
                $table->foreign('empresa_id')->references('id')->on('empresas')->cascadeOnDelete();
            });
        }

        $empresas = DB::table('empresas')->select('id')->get();
        $base = [
            'ATIVO' => 'ATIVO',
            'ASSISTENCIA' => 'ASSISTENCIA',
            'DEFEITO' => 'DEFEITO',
            'EMPRESTADO' => 'EMPRESTADO',
        ];

        foreach ($empresas as $empresa) {
            foreach ($base as $key => $descricao) {
                $exists = DB::table('estoque_status_cadastros')
                    ->where('empresa_id', $empresa->id)
                    ->where('status_key', $key)
                    ->exists();
                if ($exists) {
                    continue;
                }

                DB::table('estoque_status_cadastros')->insert([
                    'empresa_id' => $empresa->id,
                    'status_key' => $key,
                    'descricao' => $descricao,
                    'is_system' => true,
                    'ativo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('estoque_status_cadastros');
    }
};
