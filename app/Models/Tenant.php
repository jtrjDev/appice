<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    /**
     * Colunas customizadas que adicionamos na tabela tenants.
     * Importante: o stancl/tenancy guarda por padrão um campo `data` (JSON)
     * para atributos extras. Listando aqui, ele trata essas colunas como
     * colunas reais da tabela.
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'plan_id',
            'status',
            'trial_ends_at',
        ];
    }

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Usuários do banco CENTRAL que estão vinculados a este tenant.
     * (Não são os usuários internos do tenant — esses ficam no banco do tenant.)
     */
    public function centralUsers(): HasMany
    {
        return $this->hasMany(\App\Models\Central\User::class, 'tenant_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}