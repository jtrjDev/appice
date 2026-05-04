<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('produtos', function (Blueprint $table) {
            // Verifica se a coluna não existe antes de adicionar
            if (!Schema::hasColumn('produtos', 'cfop')) {
                $table->string('cfop')->nullable()->after('ncm');
            }
            
            if (!Schema::hasColumn('produtos', 'cst_icms')) {
                $table->string('cst_icms')->nullable()->default('102')->after('aliq_cofins');
            }
            
            if (!Schema::hasColumn('produtos', 'cst_pis')) {
                $table->string('cst_pis')->nullable()->default('07')->after('cst_icms');
            }
            
            if (!Schema::hasColumn('produtos', 'cst_cofins')) {
                $table->string('cst_cofins')->nullable()->default('07')->after('cst_pis');
            }
        });
    }

    public function down()
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropColumn(['cfop', 'cst_icms', 'cst_pis', 'cst_cofins']);
        });
    }
};