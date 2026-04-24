<?php

namespace App\Livewire\Tenant\Usuarios;

use App\Models\Tenant\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class Edit extends Component
{
    public $usuarioId;
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $role = '';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'password' => 'nullable|string|min:6|confirmed',
        'role' => 'required|in:admin,gerente,caixa,garcom,operador',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'password.confirmed' => 'As senhas não coincidem.',
        'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
    ];

    public function mount()
{
    // Pegar o ID da URL
    $usuarioId = request()->route('usuario');
    
    // Buscar usuário do tenant
    $usuario = \App\Models\Tenant\User::find($usuarioId);
    
    if (!$usuario) {
        session()->flash('error', 'Usuário não encontrado.');
        return redirect()->route('tenant.usuarios.index');
    }
    
    // Verificar permissão
    $user = \App\Models\Tenant\User::where('email', auth()->user()->email)->first();
    
    if (!$user || !in_array($user->role, ['admin', 'gerente'])) {
        abort(403, 'Você não tem permissão para editar usuários.');
    }
    
    $this->usuarioId = $usuario->id;
    $this->name = $usuario->name;
    $this->email = $usuario->email;
    $this->role = $usuario->role;
    $this->is_active = $usuario->is_active;
}

    public function update()
    {
        $rules = $this->rules;
        
        // Se o email mudou, verificar unicidade
        $usuario = User::find($this->usuarioId);
        if ($this->email != $usuario->email) {
            $rules['email'] = 'required|email|max:255|unique:users,email';
        }
        
        $this->validate($rules);

        try {
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
                'is_active' => $this->is_active,
            ];
            
            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }
            
            $usuario->update($data);

            session()->flash('success', 'Usuário atualizado com sucesso!');
            return redirect()->route('tenant.usuarios.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao atualizar usuário: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.tenant.usuarios.edit')->layout('layouts.tenant');
    }
}