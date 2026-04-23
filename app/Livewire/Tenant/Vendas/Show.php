<?php

namespace App\Livewire\Tenant\Vendas;

use App\Models\Tenant\Pedido;
use Livewire\Component;

class Show extends Component
{
    public int $id;
    public Pedido $pedido;

    public function mount(int $id): void
    {
        $this->id = $id;

        $this->pedido = Pedido::with(['itens', 'cliente', 'caixa'])->findOrFail($id);
    }

    public function emitirCupom()
    {
        return redirect()->route('tenant.vendas.cupom', ['id' => $this->pedido->id]);
    }

    public function render()
    {
        return view('livewire.tenant.vendas.show')
            ->layout('layouts.tenant');
    }
}