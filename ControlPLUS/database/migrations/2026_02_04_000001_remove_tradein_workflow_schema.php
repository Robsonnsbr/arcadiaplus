<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('produtos')) {
            Schema::table('produtos', function (Blueprint $table) {
                if (Schema::hasColumn('produtos', 'status_avaliacao')) {
                    $table->dropColumn('status_avaliacao');
                }
                if (Schema::hasColumn('produtos', 'status_aceite_cliente')) {
                    $table->dropColumn('status_aceite_cliente');
                }
                if (Schema::hasColumn('produtos', 'avaliacao_observacao')) {
                    $table->dropColumn('avaliacao_observacao');
                }
                if (Schema::hasColumn('produtos', 'valor_tradein_avaliado')) {
                    $table->dropColumn('valor_tradein_avaliado');
                }
            });
        }

        if (Schema::hasTable('tradein_credit_balances')) {
            Schema::drop('tradein_credit_balances');
        }

        if (Schema::hasTable('tradein_credit_events')) {
            Schema::drop('tradein_credit_events');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('produtos')) {
            Schema::table('produtos', function (Blueprint $table) {
                if (!Schema::hasColumn('produtos', 'status_avaliacao')) {
                    $table->string('status_avaliacao', 20)->nullable()->index();
                }
                if (!Schema::hasColumn('produtos', 'status_aceite_cliente')) {
                    $table->string('status_aceite_cliente', 20)->nullable()->index();
                }
                if (!Schema::hasColumn('produtos', 'avaliacao_observacao')) {
                    $table->text('avaliacao_observacao')->nullable();
                }
                if (!Schema::hasColumn('produtos', 'valor_tradein_avaliado')) {
                    $table->decimal('valor_tradein_avaliado', 12, 2)->nullable();
                }
            });
        }

        // tradein_credit_balances and tradein_credit_events definitions were not preserved here.
    }
};
