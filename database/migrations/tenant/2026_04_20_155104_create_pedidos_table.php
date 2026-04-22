<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_pedido')->unique();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes');
            $table->enum('tipo', ['balcao', 'entrega', 'mesa'])->default('balcao');
            $table->string('mesa')->nullable();
            $table->string('endereco')->nullable();
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('telefone')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('taxa_entrega', 10, 2)->default(0);
            $table->decimal('desconto', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('status', ['pendente', 'preparando', 'saiu_entrega', 'entregue', 'cancelado'])->default('pendente');
            $table->text('observacoes')->nullable();
            $table->json('pagamentos')->nullable();
            $table->foreignId('atendente_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pedidos');
    }
};