<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caixas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->comment('Operador que abriu');
            $table->decimal('saldo_inicial', 10, 2)->default(0);
            $table->decimal('saldo_final', 10, 2)->nullable();
            $table->decimal('total_dinheiro', 10, 2)->default(0);
            $table->decimal('total_credito', 10, 2)->default(0);
            $table->decimal('total_debito', 10, 2)->default(0);
            $table->decimal('total_pix', 10, 2)->default(0);
            $table->decimal('total_vendas', 10, 2)->default(0);
            $table->integer('quantidade_vendas')->default(0);
            $table->enum('status', ['aberto', 'fechado'])->default('aberto');
            $table->timestamp('aberto_em')->useCurrent();
            $table->timestamp('fechado_em')->nullable();
            $table->text('observacao')->nullable();
            $table->timestamps();
        });

        // Adiciona caixa_id na tabela de pedidos
        Schema::table('pedidos', function (Blueprint $table) {
            $table->foreignId('caixa_id')->nullable()->after('id')->constrained('caixas');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['caixa_id']);
            $table->dropColumn('caixa_id');
        });

        Schema::dropIfExists('caixas');
    }
};