<?php

namespace App\Livewire\Admin\Tenants;

use App\Models\Plan;
use App\Models\Tenant;
use Livewire\Component;

class Edit extends Component
{
    public Tenant $tenant;
    public $name;
    public $email;
    public $plan_id;
    public $status;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'plan_id' => 'required|exists:plans,id',
        'status' => 'required|in:active,inactive,trial',
    ];

    public function mount(Tenant $tenant)
    {
        $this->tenant = $tenant;
        $this->name = $tenant->name;
        $this->email = $tenant->email;
        $this->plan_id = $tenant->plan_id;
        $this->status = $tenant->status;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->validate();

        $this->tenant->update([
            'name' => $this->name,
            'email' => $this->email,
            'plan_id' => $this->plan_id,
            'status' => $this->status,
        ]);

        session()->flash('success', 'Tenant atualizado com sucesso!');
        
        return redirect()->route('admin.tenants.index');
    }

    public function render()
    {
        $plans = Plan::where('is_active', true)->get();
        
        return view('livewire.admin.tenants.edit', [
            'plans' => $plans,
        ])->layout('layouts.admin');
    }
}