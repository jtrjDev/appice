<?php

namespace App\Livewire\Tenant\Notas;

use App\Models\Tenant\NotaFiscal;
use Livewire\Component;
use App\Livewire\Traits\WithTenancy;

class Show extends Component
{
    use WithTenancy;

    public $notaId;
    public $nota;

    protected $rules = [];

    public function mount()
    {
        // Pegar o ID da URL
        $this->notaId = request()->route('nota');
        
        // Buscar a nota (o tenancy já está inicializado pelo Trait)
        $this->nota = NotaFiscal::with('pedido')->find($this->notaId);
        
        if (!$this->nota) {
            session()->flash('error', 'Nota fiscal não encontrada.');
            return redirect()->route('tenant.notas.index');
        }
    }

    public function reimprimirCupom()
    {
        // Redireciona para a página de impressão
        return redirect()->route('tenant.vendas.cupom', $this->nota->pedido_id);
    }

    public function render()
    {
        return view('livewire.tenant.notas.show')->layout('layouts.tenant');
    }
}