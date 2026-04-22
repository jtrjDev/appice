<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('configuracoes', function (Blueprint $table) {
            $table->id();
            
            // Dados da empresa
            $table->string('razao_social');
            $table->string('nome_fantasia')->nullable();
            $table->string('cpf_cnpj', 18)->unique();
            $table->string('inscricao_estadual')->nullable();
            $table->string('inscricao_municipal')->nullable();
            $table->string('rg')->nullable();
            
            // Endereço
            $table->string('cep', 10);
            $table->string('endereco');
            $table->string('numero', 20);
            $table->string('complemento')->nullable();
            $table->string('bairro');
            $table->string('cidade');
            $table->string('estado', 2);
            
            // Contato
            $table->string('telefone', 20)->nullable();
            $table->string('whatsapp', 20);
            $table->string('email_empresa');
            $table->string('site')->nullable();
            
            // Logo
            $table->string('logo')->nullable();
            
            // Configurações de NF
            $table->string('ultimo_numero_nf')->nullable();
            $table->string('numero_serie_nf')->nullable();
            $table->enum('ambiente_nf', ['homologacao', 'producao'])->default('homologacao');
            
            // Certificado Digital (armazenar como arquivo)
            $table->string('certificado_path')->nullable();
            $table->string('certificado_senha')->nullable();
            $table->date('certificado_validade')->nullable();
            
            // Configurações do Cupom Fiscal / PDV
            $table->text('cabecalho_cupom')->nullable();
            $table->text('rodape_cupom')->nullable();
            $table->boolean('exibir_logo_cupom')->default(true);
            $table->string('tema_cupom')->default('padrao');
            
            // Configurações fiscais
            $table->enum('regime_tributario', ['simples_nacional', 'lucro_presumido', 'lucro_real', 'mei'])->default('simples_nacional');
            $table->string('codigo_atividade')->nullable(); // CNAE
            $table->string('codigo_municipio')->nullable(); // IBGE
            $table->string('codigo_pais')->default('1058'); // Brasil
            
            // Webhooks
            $table->string('webhook_nfe')->nullable();
            $table->string('webhook_nfse')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('configuracoes');
    }
};