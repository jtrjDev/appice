<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/tenant/xxxx_add_focus_token_to_configuracoes_table.php
public function up()
{
    Schema::table('configuracoes', function (Blueprint $table) {
        $table->string('focus_token')->nullable()->after('ambiente_nf');
    });
}

public function down()
{
    Schema::table('configuracoes', function (Blueprint $table) {
        $table->dropColumn('focus_token');
    });
}
};
