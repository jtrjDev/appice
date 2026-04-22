<?php

namespace App\Livewire\Tenant\PDV;

use App\Models\Tenant\Comanda;
use Livewire\Component;
use Livewire\Attributes\Computed;

class Mesas extends Component
{
    #[Computed]
    public function mesas()
    {
        return Comanda::where('status', 'aberta')
            ->with(['itens', 'pagamentos'])
            ->orderBy('created_at')
            ->get();
    }

    public function irParaMesa(string $mesa): void
    {
        $this->redirect(route('tenant.pdv', ['mesa' => $mesa]));
    }

    public function render()
    {
        return view('livewire.tenant.pdv.mesas')
            ->layout('layouts.tenant');
    }
}