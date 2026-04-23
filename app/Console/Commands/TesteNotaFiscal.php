<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant\Cliente;
use App\Models\Tenant\Pedido;
use App\Models\Tenant\User;
use App\Jobs\EmitirNotaFiscal;

class TesteNotaFiscal extends Command
{
    protected $signature = 'teste:nota';
    protected $description = 'Testa emissão de nota fiscal';

    public function handle()
    {
        // Inicializa o tenant
        $tenant = \App\Models\Tenant::find('sorvete-novo');
        tenancy()->initialize($tenant);

        // Busca um usuário do tenant para ser o atendente
        $atendente = User::first();
        
        if (!$atendente) {
            $this->error("Nenhum usuário encontrado no tenant. Execute primeiro: php artisan tenants:seed --tenant=sorvete-novo");
            return;
        }

        $cliente = Cliente::first();

        $pedido = Pedido::create([
            'numero_pedido' => 'TEST_' . time(),
            'cliente_id' => $cliente->id,
            'tipo' => 'balcao',
            'subtotal' => 100.00,
            'total' => 100.00,
            'status' => 'pendente',
            'pagamentos' => [['forma' => 'dinheiro', 'valor' => 100.00]],
            'atendente_id' => $atendente->id, // Adiciona o atendente
        ]);

        $this->info("Pedido criado: ID {$pedido->id}");

        // Dispara a nota
        EmitirNotaFiscal::dispatchSync($pedido->id, 'sorvete-novo');

        $this->info("Nota fiscal emitida!");
    }
}