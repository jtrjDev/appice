<?php

namespace App\Livewire\Tenant;

use App\Models\Tenant\Cliente;
use App\Models\Tenant\Produto;
use App\Models\Tenant\Categoria;
use Livewire\Component;

class Dashboard extends Component
{
    public $stats = [];
    public $ultimos_clientes = [];
    public $produtos_destaque = [];

    public function mount()
    {
        $this->stats = [
            'total_clientes' => Cliente::count(),
            'total_produtos' => Produto::count(),
            'total_categorias' => Categoria::count(),
            'vendas_hoje' => 0,
        ];

        $this->ultimos_clientes = Cliente::latest()->take(5)->get();
        $this->produtos_destaque = Produto::where('destaque', true)->take(5)->get();
    }

    // Método para atualizar dados em tempo real (se necessário)
    public function refreshStats()
    {
        $this->stats['total_clientes'] = Cliente::count();
        $this->stats['total_produtos'] = Produto::count();
        $this->stats['total_categorias'] = Categoria::count();
        
        $this->ultimos_clientes = Cliente::latest()->take(5)->get();
        $this->produtos_destaque = Produto::where('destaque', true)->take(5)->get();
        
        $this->dispatch('stats-updated');
    }

    public function render()
    {
        return view('livewire.tenant.dashboard')->layout('layouts.tenant');
    }
}