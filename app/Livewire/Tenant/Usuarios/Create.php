<?php

namespace App\Livewire\Tenant\Usuarios;

use App\Models\Tenant\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $role = 'operador';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|string|min:6|confirmed',
        'role' => 'required|in:admin,gerente,caixa,garcom,operador',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'email.unique' => 'Este e-mail já está em uso.',
        'password.confirmed' => 'As senhas não coincidem.',
        'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
    ];

   public function mount()
{
    // Buscar usuário do tenant diretamente
    $user = \App\Models\Tenant\User::where('email', auth()->user()->email)->first();
    
    if (!$user) {
        abort(403, 'Usuário não encontrado.');
    }
    
    // Permite acesso apenas para admin e gerente (operador não pode criar)
    if (!in_array($user->role, ['admin', 'gerente'])) {
        abort(403, 'Você não tem permissão para criar usuários.');
    }
}

    public function save()
    {
        $this->validate();

        try {
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => $this->role,
                'is_active' => $this->is_active,
            ]);

            session()->flash('success', 'Usuário criado com sucesso!');
            return redirect()->route('tenant.usuarios.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao criar usuário: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.tenant.usuarios.create')->layout('layouts.tenant');
    }
}