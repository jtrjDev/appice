<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('produtos', function (Blueprint $table) {
            // Tipo de venda: unidade, peso, fracionado
            $table->enum('tipo_venda', ['unidade', 'peso', 'fracionado'])->default('unidade');
            // Permite meio produto (ex: meia pizza)
            $table->boolean('permite_meio')->default(false);
            // Valor do meio produto (se aplicável)
            $table->decimal('preco_meio', 10, 2)->nullable();
            // Tamanhos disponíveis
            $table->json('tamanhos')->nullable(); // [{"nome":"P","preco":30}, {"nome":"M","preco":40}]
            // Adicionais
            $table->json('adicionais')->nullable(); // [{"nome":"Queijo extra","preco":5}]
        });
    }

    public function down()
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropColumn(['tipo_venda', 'permite_meio', 'preco_meio', 'tamanhos', 'adicionais']);
        });
    }
};