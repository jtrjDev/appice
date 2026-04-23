<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/tenant/xxxx_create_notas_fiscais_table.php
public function up()
{
    Schema::create('notas_fiscais', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
        $table->enum('modelo', ['nfe', 'nfce', 'nfse']);
        $table->string('referencia')->unique(); // Seu ID interno para a Focus
        $table->string('numero_nota')->nullable(); // Número oficial da SEFAZ
        $table->string('chave_acesso')->nullable();
        $table->string('link_xml')->nullable();
        $table->string('link_pdf')->nullable();
        $table->string('status'); // processando, autorizada, cancelada, rejeitada
        $table->string('mensagem_erro')->nullable();
        $table->json('retorno_completo')->nullable(); // Guarda o retorno da API
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notas_fiscais');
    }
};
