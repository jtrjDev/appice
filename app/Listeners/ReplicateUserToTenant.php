<?php

namespace App\Listeners;

use App\Events\UserCreatedInCentral;
use App\Models\Tenant;
use App\Models\Tenant\User as TenantUser;

class ReplicateUserToTenant
{
    public function handle(UserCreatedInCentral $event): void
    {
        $user = $event->user;

        // Superadmin não precisa de réplica
        if ($user->is_super_admin || !$user->tenant_id) {
            return;
        }

        $tenant = Tenant::find($user->tenant_id);

        if (!$tenant) {
            return;
        }

        // Inicializa o banco do tenant
        tenancy()->initialize($tenant);

        // Cria ou atualiza o usuário espelho
        TenantUser::updateOrCreate(
            ['email' => $user->email],
            [
                'name'     => $user->name,
                'email'    => $user->email,
                'password' => $user->password, // já está hasheado
                'role'     => 'admin',          // primeiro usuário é admin
                'is_active' => $user->is_active,
            ]
        );

        // Encerra o contexto do tenant
        tenancy()->end();
    }
}