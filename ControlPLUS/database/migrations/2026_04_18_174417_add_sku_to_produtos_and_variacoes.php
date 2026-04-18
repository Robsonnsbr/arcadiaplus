<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adiciona coluna `sku` em `produtos` e `produto_variacaos`.
 *
 * - NULL por padrão: produtos legados permanecem válidos.
 * - Unicidade por (empresa_id, sku) em produtos: MySQL permite múltiplos NULLs
 *   nesse tipo de índice, portanto o legado não é afetado.
 * - Unicidade por (produto_id, sku) em produto_variacaos.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            if (!Schema::hasColumn('produtos', 'sku')) {
                $table->string('sku', 40)->nullable()->after('referencia');
                $table->unique(['empresa_id', 'sku'], 'produtos_empresa_sku_unique');
            }
        });

        Schema::table('produto_variacaos', function (Blueprint $table) {
            if (!Schema::hasColumn('produto_variacaos', 'sku')) {
                $table->string('sku', 40)->nullable()->after('referencia');
                $table->unique(['produto_id', 'sku'], 'produto_variacaos_produto_sku_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            if (Schema::hasColumn('produtos', 'sku')) {
                $table->dropUnique('produtos_empresa_sku_unique');
                $table->dropColumn('sku');
            }
        });

        Schema::table('produto_variacaos', function (Blueprint $table) {
            if (Schema::hasColumn('produto_variacaos', 'sku')) {
                $table->dropUnique('produto_variacaos_produto_sku_unique');
                $table->dropColumn('sku');
            }
        });
    }
};
