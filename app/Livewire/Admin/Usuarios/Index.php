<?php

namespace App\Livewire\Admin\Usuarios;

use App\Models\Central\User as CentralUser;
use App\Models\Tenant;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 15;
    public $filterTenant = '';
    public $confirmingDelete = false;
    public $usuarioToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterTenant' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterTenant()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->usuarioToDelete = $id;
        $this->confirmingDelete = true;
    }

    public function delete()
    {
        $usuario = CentralUser::findOrFail($this->usuarioToDelete);
        
        // Não deixar excluir o próprio usuário logado
        if ($usuario->id == auth()->id()) {
            session()->flash('error', 'Você não pode excluir seu próprio usuário!');
            $this->confirmingDelete = false;
            return;
        }
        
        // Se o usuário tem tenant, também remover do tenant
        if ($usuario->tenant_id) {
            $tenant = Tenant::find($usuario->tenant_id);
            if ($tenant) {
                $tenant->run(function () use ($usuario) {
                    \App\Models\Tenant\User::where('email', $usuario->email)->delete();
                });
            }
        }
        
        $usuario->delete();

        $this->confirmingDelete = false;
        $this->usuarioToDelete = null;
        
        session()->flash('success', 'Usuário excluído com sucesso!');
    }

    public function render()
    {
        $usuarios = CentralUser::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterTenant, function ($query) {
                if ($this->filterTenant === 'super') {
                    $query->whereNull('tenant_id');
                } else {
                    $query->where('tenant_id', $this->filterTenant);
                }
            })
            ->latest()
            ->paginate($this->perPage);

        $tenants = Tenant::all();

        return view('livewire.admin.usuarios.index', [
            'usuarios' => $usuarios,
            'tenants' => $tenants,
        ])->layout('layouts.admin');
    }
}