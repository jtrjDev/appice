<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('caixas', function (Blueprint $table) {
            if (!Schema::hasColumn('caixas', 'total_sangrias')) {
                $table->decimal('total_sangrias', 10, 2)->default(0)->after('total_pix');
            }
            if (!Schema::hasColumn('caixas', 'total_suprimentos')) {
                $table->decimal('total_suprimentos', 10, 2)->default(0)->after('total_sangrias');
            }
        });
    }

    public function down()
    {
        Schema::table('caixas', function (Blueprint $table) {
            $table->dropColumn(['total_sangrias', 'total_suprimentos']);
        });
    }
};