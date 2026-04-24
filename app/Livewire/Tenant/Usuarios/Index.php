<?php

namespace App\Livewire\Tenant\Usuarios;

use App\Models\Tenant\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 15;
    public $confirmingDelete = false;
    public $usuarioToDelete = null;

  

    public function updatedSearch()
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
        $usuario = User::findOrFail($this->usuarioToDelete);
        
        // Não deixar excluir o próprio usuário
        if ($usuario->id == Auth::id()) {
            session()->flash('error', 'Você não pode excluir seu próprio usuário!');
            $this->confirmingDelete = false;
            return;
        }
        
        $usuario->delete();

        $this->confirmingDelete = false;
        $this->usuarioToDelete = null;
        
        session()->flash('success', 'Usuário excluído com sucesso!');
    }

    public function render()
{
    $user = \App\Models\Tenant\User::where('email', auth()->user()->email)->first();
    
    if (!$user || !in_array($user->role, ['admin', 'gerente', 'operador'])) {
        abort(403, 'Acesso negado. Contate o administrador.');
    }
    
    $usuarios = User::query()
        ->when($this->search, function ($query) {
            // ...
        })
        ->paginate($this->perPage);
    
    return view('livewire.tenant.usuarios.index', [
        'usuarios' => $usuarios,
    ])->layout('layouts.tenant');
}
}