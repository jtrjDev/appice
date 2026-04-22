<?php

namespace App\Livewire\Admin\Tenants;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\Central\User as CentralUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 15;
    public $confirmingTenantDeletion = false;
    public $tenantToDelete = null;
    
    // Filtros
    public $statusFilter = '';
    public $planFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'planFilter' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedPlanFilter()
    {
        $this->resetPage();
    }

    public function confirmDelete($tenantId)
    {
        $this->tenantToDelete = $tenantId;
        $this->confirmingTenantDeletion = true;
    }

    public function deleteTenant()
    {
        try {
            DB::beginTransaction();
            
            $tenant = Tenant::findOrFail($this->tenantToDelete);
            
            // Deletar usuários centrais primeiro
            CentralUser::where('tenant_id', $tenant->id)->delete();
            
            // Deletar o tenant (isso deleta o database)
            $tenant->delete();
            
            DB::commit();
            
            $this->dispatch('tenant-deleted', message: 'Tenant excluído com sucesso!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error', message: 'Erro ao excluir tenant: ' . $e->getMessage());
        }
        
        $this->confirmingTenantDeletion = false;
        $this->tenantToDelete = null;
    }

    public function render()
    {
        $tenants = Tenant::query()
            ->with('plan')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('id', 'like', '%' . $this->search . '%')
                      ->orWhere('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->planFilter, function ($query) {
                $query->where('plan_id', $this->planFilter);
            })
            ->latest()
            ->paginate($this->perPage);

        $plans = Plan::where('is_active', true)->get();
        
        $statusOptions = [
            'active' => 'Ativo',
            'inactive' => 'Inativo',
            'trial' => 'Trial'
        ];

        return view('livewire.admin.tenants.index', [
            'tenants' => $tenants,
            'plans' => $plans,
            'statusOptions' => $statusOptions,
        ])->layout('layouts.admin');
    }
}