<?php

namespace App\Livewire\Tenant\Clientes;

use App\Models\Tenant\Cliente;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 15;
    public $confirmingDelete = false;
    public $clienteToDelete = null;

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
        $this->clienteToDelete = $id;
        $this->confirmingDelete = true;
    }

    public function delete()
    {
        $cliente = Cliente::findOrFail($this->clienteToDelete);
        $cliente->delete();

        $this->confirmingDelete = false;
        $this->clienteToDelete = null;
        
        session()->flash('success', 'Cliente excluído com sucesso!');
    }

    public function render()
    {
        $clientes = Cliente::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nome', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('cpf_cnpj', 'like', '%' . $this->search . '%')
                      ->orWhere('telefone', 'like', '%' . $this->search . '%')
                      ->orWhere('celular', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.tenant.clientes.index', [
            'clientes' => $clientes,
        ])->layout('layouts.tenant');
    }
}