<?php

namespace App\Livewire\Admin\Tenants;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\Central\User as CentralUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Create extends Component
{
    public $tenant_id = '';
    public $name = '';
    public $email = '';
    public $plan_id = '';
    public $status = 'active';
    public $trial_days = 30;
    
    public $admin_name = '';
    public $admin_email = '';
    public $admin_password = '';
    public $admin_password_confirmation = '';

    protected $rules = [
        'tenant_id' => 'required|string|min:3|max:50|regex:/^[a-z0-9-]+$/|unique:tenants,id',
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'plan_id' => 'required|exists:plans,id',
        'status' => 'required|in:active,inactive,trial',
        'trial_days' => 'nullable|integer|min:0|max:365',
        'admin_name' => 'required|string|max:255',
        'admin_email' => 'required|email|max:255|unique:users,email',
        'admin_password' => 'required|string|min:8|confirmed',
    ];

    protected $messages = [
        'tenant_id.regex' => 'O ID deve conter apenas letras minúsculas, números e hífens.',
        'tenant_id.unique' => 'Este ID de tenant já está em uso.',
        'admin_email.unique' => 'Este e-mail já está em uso no sistema.',
        'admin_password.confirmed' => 'A confirmação da senha não corresponde.',
    ];

    public function mount()
    {
        $this->status = 'active';
        $this->trial_days = 30;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();

        $tenant = null;
        
        try {
            // 1. Criar o tenant (isso cria o banco de dados automaticamente)
            $tenant = Tenant::create([
                'id' => $this->tenant_id,
                'name' => $this->name,
                'email' => $this->email,
                'plan_id' => $this->plan_id,
                'status' => $this->status,
                'trial_ends_at' => $this->trial_days > 0 ? now()->addDays($this->trial_days) : null,
            ]);

            // 2. Criar usuário no banco CENTRAL
            $centralUser = CentralUser::create([
                'tenant_id' => $tenant->id,
                'name' => $this->admin_name,
                'email' => $this->admin_email,
                'password' => Hash::make($this->admin_password),
                'is_super_admin' => false,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // 3. Criar usuário DENTRO do banco do tenant
            $tenant->run(function () {
                \App\Models\Tenant\User::create([
                    'name' => $this->admin_name,
                    'email' => $this->admin_email,
                    'password' => Hash::make($this->admin_password),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);
            });

            // Sucesso!
            session()->flash('success', "Tenant '{$this->name}' criado com sucesso! 
                Usuário: {$this->admin_email} | Senha: {$this->admin_password}");
            
            return redirect()->route('admin.tenants.index');

        } catch (\Exception $e) {
            // Log do erro detalhado
          
            
            // Se o tenant foi criado mas algo falhou depois, tentamos limpar
            if ($tenant && $tenant->exists) {
                try {
                    // Tenta deletar o usuário central
                    CentralUser::where('tenant_id', $tenant->id)->delete();
                    
                    // Tenta deletar o tenant (e seu banco)
                    $tenant->delete();
                    
                  
                } catch (\Exception $cleanupError) {
                    
                }
            }
            
            // Mensagem amigável para o usuário
            $errorMessage = 'Erro ao criar tenant. ';
            
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $errorMessage .= 'Já existe um registro com este identificador.';
            } else {
                $errorMessage .= $e->getMessage();
            }
            
            session()->flash('error', $errorMessage);
        }
    }

    public function render()
    {
        $plans = Plan::where('is_active', true)->get();
        
        return view('livewire.admin.tenants.create', [
            'plans' => $plans,
        ])->layout('layouts.admin');
    }
}