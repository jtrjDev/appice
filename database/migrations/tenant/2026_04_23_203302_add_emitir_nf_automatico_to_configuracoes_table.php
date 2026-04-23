<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('configuracoes', function (Blueprint $table) {
            if (!Schema::hasColumn('configuracoes', 'emitir_nf_automatico')) {
                $table->boolean('emitir_nf_automatico')->default(false)->after('ambiente_nf');
            }
        });
    }

    public function down()
    {
        Schema::table('configuracoes', function (Blueprint $table) {
            if (Schema::hasColumn('configuracoes', 'emitir_nf_automatico')) {
                $table->dropColumn('emitir_nf_automatico');
            }
        });
    }
};