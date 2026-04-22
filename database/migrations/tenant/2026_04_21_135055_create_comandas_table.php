<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comandas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caixa_id')->nullable()->constrained('caixas');
            $table->string('mesa');
            $table->enum('status', ['aberta', 'fechada'])->default('aberta');
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('total_pago', 10, 2)->default(0);
            $table->text('observacao')->nullable();
            $table->timestamp('fechada_em')->nullable();
            $table->timestamps();
        });

        Schema::create('comanda_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comanda_id')->constrained('comandas');
            $table->foreignId('produto_id')->constrained('produtos');
            $table->string('produto_nome');
            $table->decimal('quantidade', 10, 3)->default(1);
            $table->decimal('preco_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->text('observacao')->nullable();
            $table->timestamps();
        });

        Schema::create('comanda_pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comanda_id')->constrained('comandas');
            $table->enum('forma', ['dinheiro', 'cartao_credito', 'cartao_debito', 'pix']);
            $table->decimal('valor', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comanda_pagamentos');
        Schema::dropIfExists('comanda_itens');
        Schema::dropIfExists('comandas');
    }
};