<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_trocas', function (Blueprint $table) {
            $table->string('serial_codigo', 255)->nullable()->after('sub_total');
        });
    }

    public function down(): void
    {
        Schema::table('item_trocas', function (Blueprint $table) {
            $table->dropColumn('serial_codigo');
        });
    }
};
