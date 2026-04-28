<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('configuracoes', function (Blueprint $table) {
            // Tipo de negócio
            if (!Schema::hasColumn('configuracoes', 'tipo_negocio')) {
                $table->enum('tipo_negocio', [
                    'sorveteria',
                    'pizzaria',
                    'lanchonete',
                    'restaurante',
                    'acai',
                    'hamburgueria'
                ])->default('lanchonete')->after('id');
            }

            // Configurações específicas
            if (!Schema::hasColumn('configuracoes', 'permite_fracionamento')) {
                $table->boolean('permite_fracionamento')->default(false)->after('tipo_negocio');
            }

            if (!Schema::hasColumn('configuracoes', 'permite_meia_porcao')) {
                $table->boolean('permite_meia_porcao')->default(false)->after('permite_fracionamento');
            }

            if (!Schema::hasColumn('configuracoes', 'tipo_venda_padrao')) {
                $table->enum('tipo_venda_padrao', ['unidade', 'peso', 'fracionado'])->default('unidade')->after('permite_meia_porcao');
            }

            if (!Schema::hasColumn('configuracoes', 'unidade_medida_padrao')) {
                $table->string('unidade_medida_padrao', 5)->default('UN')->after('tipo_venda_padrao');
            }
        });
    }

    public function down()
    {
        Schema::table('configuracoes', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_negocio',
                'permite_fracionamento',
                'permite_meia_porcao',
                'tipo_venda_padrao',
                'unidade_medida_padrao'
            ]);
        });
    }
};