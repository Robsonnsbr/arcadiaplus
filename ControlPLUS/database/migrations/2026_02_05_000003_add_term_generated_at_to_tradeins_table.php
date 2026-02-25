<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('tradeins', 'term_generated_at')) {
            Schema::table('tradeins', function (Blueprint $table) {
                $table->timestamp('term_generated_at')->nullable()->after('aceite_em');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('tradeins', 'term_generated_at')) {
            Schema::table('tradeins', function (Blueprint $table) {
                $table->dropColumn('term_generated_at');
            });
        }
    }
};
