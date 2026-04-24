<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Primeiro, alterar a coluna role para aceitar os novos valores
        Schema::table('users', function (Blueprint $table) {
            // MySQL não suporta MODIFY diretamente, precisamos fazer manualmente
        });
        
        // Para MySQL, executamos SQL diretamente
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'gerente', 'caixa', 'garcom', 'operador') DEFAULT 'operador'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('operador') DEFAULT 'operador'");
    }
};