<?php

namespace App\Livewire\Traits;

trait WithTenancy
{
    protected function ensureTenancy()
    {
        if (auth()->check() && auth()->user()->tenant_id && !tenancy()->initialized) {
            $tenant = \App\Models\Tenant::find(auth()->user()->tenant_id);
            if ($tenant) {
                tenancy()->initialize($tenant);
            }
        }
    }
    
    public function boot()
    {
        $this->ensureTenancy();
    }
}