<?php

namespace App\Livewire\Admin\Usuarios;

use App\Models\Central\User as CentralUser;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Edit extends Component
{
    public $usuarioId;
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $tenant_id = '';
    public $is_super_admin = false;
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'password' => 'nullable|string|min:8|confirmed',
        'tenant_id' => 'nullable|exists:tenants,id',
        'is_super_admin' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'password.confirmed' => 'As senhas não coincidem.',
        'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
    ];

    public function mount(CentralUser $usuario)
    {
        $this->usuarioId = $usuario->id;
        $this->name = $usuario->name;
        $this->email = $usuario->email;
        $this->tenant_id = $usuario->tenant_id;
        $this->is_super_admin = $usuario->is_super_admin;
        $this->is_active = $usuario->is_active;
    }

    public function update()
    {
        $rules = $this->rules;
        
        // Se o email mudou, verificar unicidade
        $usuario = CentralUser::find($this->usuarioId);
       // No método update(), altere:
        if ($this->email != $usuario->email) {
            $rules['email'] = 'required|email|max:255|unique:users,email'; // mudou de central_users para users
        }
        
        $this->validate($rules);

        try {
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'tenant_id' => $this->tenant_id ?: null,
                'is_super_admin' => $this->is_super_admin,
                'is_active' => $this->is_active,
            ];
            
            // Se a senha foi preenchida, atualizar
            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }
            
            $usuario->update($data);
            
            // Se o tenant mudou, atualizar também no tenant
            if ($usuario->tenant_id) {
                $tenant = Tenant::find($usuario->tenant_id);
                if ($tenant) {
                    $tenant->run(function () use ($usuario) {
                        \App\Models\Tenant\User::updateOrCreate(
                            ['email' => $usuario->email],
                            [
                                'name' => $usuario->name,
                                'is_active' => $usuario->is_active,
                            ]
                        );
                    });
                }
            }

            session()->flash('success', 'Usuário atualizado com sucesso!');
            return redirect()->route('admin.usuarios.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao atualizar usuário: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $tenants = Tenant::all();
        
        return view('livewire.admin.usuarios.edit', [
            'tenants' => $tenants,
        ])->layout('layouts.admin');
    }
}