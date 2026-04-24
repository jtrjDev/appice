<?php

namespace App\Livewire\Tenant\Caixa;

use App\Models\Tenant\Caixa;
use App\Models\Tenant\Configuracao;
use Livewire\Component;

class Relatorio extends Component
{
    public $caixa;
    public $config;
    public $autoPrint = false;

    public function mount($id)
    {
        $this->caixa = Caixa::with('operador')->findOrFail($id);
        $this->config = Configuracao::first();
        $this->autoPrint = request()->query('print', false);
    }

    public function render()
    {
        return view('livewire.tenant.caixa.relatorio');
    }
}