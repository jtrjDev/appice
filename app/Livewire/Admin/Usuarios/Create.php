<?php

namespace App\Livewire\Admin\Usuarios;

use App\Models\Central\User as CentralUser;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Create extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $tenant_id = '';
    public $is_super_admin = false;
    public $is_active = true;

   protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email', // mudou de central_users para users
        'password' => 'required|string|min:8|confirmed',
        'tenant_id' => 'nullable|exists:tenants,id',
        'is_super_admin' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'email.unique' => 'Este e-mail já está em uso.',
        'password.confirmed' => 'As senhas não coincidem.',
        'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
    ];

    public function save()
    {
        $this->validate();

        try {
            // Criar usuário no banco central
            $usuario = CentralUser::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'tenant_id' => $this->tenant_id ?: null,
                'is_super_admin' => $this->is_super_admin,
                'is_active' => $this->is_active,
                'email_verified_at' => now(),
            ]);

            // Mensagem de sucesso
            session()->flash('success', 'Usuário criado com sucesso!');
            return redirect()->route('admin.usuarios.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao criar usuário: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $tenants = Tenant::all();
        
        return view('livewire.admin.usuarios.create', [
            'tenants' => $tenants,
        ])->layout('layouts.admin');
    }
}