<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('produtos', function (Blueprint $table) {
            // Verificar e adicionar apenas as colunas que faltam
            if (!Schema::hasColumn('produtos', 'estoque_minimo')) {
                $table->integer('estoque_minimo')->default(0)->after('estoque');
            }
            
            if (!Schema::hasColumn('produtos', 'tipo_venda')) {
                $table->enum('tipo_venda', ['unidade', 'peso', 'fracionado'])->default('unidade')->after('preco_custo');
            }
            
            if (!Schema::hasColumn('produtos', 'permite_meio')) {
                $table->boolean('permite_meio')->default(false)->after('tipo_venda');
            }
            
            if (!Schema::hasColumn('produtos', 'preco_meio')) {
                $table->decimal('preco_meio', 10, 2)->nullable()->after('permite_meio');
            }
            
            if (!Schema::hasColumn('produtos', 'tamanhos')) {
                $table->json('tamanhos')->nullable()->after('preco_meio');
            }
            
            if (!Schema::hasColumn('produtos', 'adicionais')) {
                $table->json('adicionais')->nullable()->after('tamanhos');
            }
            
            if (!Schema::hasColumn('produtos', 'ncm')) {
                $table->string('ncm', 8)->nullable()->after('adicionais');
            }
            
            if (!Schema::hasColumn('produtos', 'cest')) {
                $table->string('cest', 7)->nullable()->after('ncm');
            }
            
            if (!Schema::hasColumn('produtos', 'origem')) {
                $table->string('origem', 1)->default('0')->after('cest');
            }
            
            if (!Schema::hasColumn('produtos', 'aliq_icms')) {
                $table->decimal('aliq_icms', 5, 2)->nullable()->after('origem');
            }
            
            if (!Schema::hasColumn('produtos', 'aliq_ipi')) {
                $table->decimal('aliq_ipi', 5, 2)->nullable()->after('aliq_icms');
            }
            
            if (!Schema::hasColumn('produtos', 'aliq_pis')) {
                $table->decimal('aliq_pis', 5, 2)->nullable()->after('aliq_ipi');
            }
            
            if (!Schema::hasColumn('produtos', 'aliq_cofins')) {
                $table->decimal('aliq_cofins', 5, 2)->nullable()->after('aliq_pis');
            }
            
            if (!Schema::hasColumn('produtos', 'unidade_medida')) {
                $table->string('unidade_medida', 5)->default('UN')->after('aliq_cofins');
            }
        });
    }

    public function down()
    {
        Schema::table('produtos', function (Blueprint $table) {
            $columns = [
                'estoque_minimo', 'tipo_venda', 'permite_meio', 'preco_meio',
                'tamanhos', 'adicionais', 'ncm', 'cest', 'origem', 'aliq_icms', 'aliq_ipi',
                'aliq_pis', 'aliq_cofins', 'unidade_medida'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('produtos', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};