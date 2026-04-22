<?php

namespace App\Livewire\Tenant\Configuracoes;

use App\Models\Tenant\Configuracao;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 15;
    public $confirmingDelete = false;
    public $configToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->configToDelete = $id;
        $this->confirmingDelete = true;
    }

    public function delete()
    {
        $config = Configuracao::findOrFail($this->configToDelete);
        $config->delete();

        $this->confirmingDelete = false;
        $this->configToDelete = null;
        
        session()->flash('success', 'Configuração excluída com sucesso!');
    }

    public function render()
    {
        $configuracoes = Configuracao::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('razao_social', 'like', '%' . $this->search . '%')
                      ->orWhere('nome_fantasia', 'like', '%' . $this->search . '%')
                      ->orWhere('cpf_cnpj', 'like', '%' . $this->search . '%')
                      ->orWhere('email_empresa', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.tenant.configuracoes.index', [
            'configuracoes' => $configuracoes,
        ])->layout('layouts.tenant');
    }
}