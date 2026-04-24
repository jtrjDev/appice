<?php

namespace App\Livewire\Tenant\Caixa;

use App\Models\Tenant\Caixa;
use Livewire\Component;
use Livewire\WithPagination;
use App\Livewire\Traits\WithTenancy;

class Historico extends Component
{
    use WithPagination, WithTenancy;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 15;

    public $mostrarRelatorio = false;
    public $relatorioCaixa = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function abrirRelatorio($id)
{
    $this->relatorioCaixa = Caixa::with('operador')->findOrFail($id);
    $this->mostrarRelatorio = true;
}

public function fecharRelatorio()
{
    $this->mostrarRelatorio = false;
    $this->relatorioCaixa = null;
}

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $caixas = Caixa::with('operador')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('id', 'like', '%' . $this->search . '%')
                      ->orWhereHas('operador', function ($op) {
                          $op->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.tenant.caixa.historico', [
            'caixas' => $caixas,
        ])->layout('layouts.tenant');
    }
}