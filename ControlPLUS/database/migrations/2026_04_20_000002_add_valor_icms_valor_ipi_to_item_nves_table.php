<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_nves', function (Blueprint $table) {
            $table->decimal('valor_icms', 12, 2)->default(0)->after('perc_icms');
            $table->decimal('valor_ipi', 12, 2)->default(0)->after('perc_ipi');
        });
    }

    public function down(): void
    {
        Schema::table('item_nves', function (Blueprint $table) {
            $table->dropColumn(['valor_icms', 'valor_ipi']);
        });
    }
};
