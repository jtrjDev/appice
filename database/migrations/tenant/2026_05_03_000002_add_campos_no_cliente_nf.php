<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (!Schema::hasColumn('clientes', 'inscricao_estadual')) {
                $table->string('inscricao_estadual')->nullable()->after('cpf_cnpj');
            }
            
            if (!Schema::hasColumn('clientes', 'endereco')) {
                $table->string('endereco')->nullable()->after('inscricao_estadual');
            }
            
            if (!Schema::hasColumn('clientes', 'numero')) {
                $table->string('numero')->nullable()->after('endereco');
            }
            
            if (!Schema::hasColumn('clientes', 'bairro')) {
                $table->string('bairro')->nullable()->after('numero');
            }
            
            if (!Schema::hasColumn('clientes', 'cidade')) {
                $table->string('cidade')->nullable()->after('bairro');
            }
            
            if (!Schema::hasColumn('clientes', 'uf')) {
                $table->string('uf', 2)->nullable()->after('cidade');
            }
            
            if (!Schema::hasColumn('clientes', 'cep')) {
                $table->string('cep', 9)->nullable()->after('uf');
            }
        });
    }

    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn([
                'inscricao_estadual', 'endereco', 'numero', 
                'bairro', 'cidade', 'uf', 'cep'
            ]);
        });
    }
};