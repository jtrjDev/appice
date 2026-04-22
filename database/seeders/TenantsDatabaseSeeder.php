<?php

namespace Database\Seeders;

use App\Models\Central\User as CentralUser;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantsDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $plan = Plan::where('slug', 'pro')->first();

        if (!$plan) {
            $this->command->error('Plano "pro" não encontrado. Rode primeiro: php artisan db:seed --class=CentralDatabaseSeeder');
            return;
        }

        // --- Tenant DEMO ---
        $tenantId = 'demo';

        if (Tenant::find($tenantId)) {
            $this->command->warn("Tenant '{$tenantId}' já existe. Pulando criação.");
            return;
        }

        $this->command->info("Criando tenant '{$tenantId}'...");
        $this->command->info("→ Isso vai criar o database tenant_demo automaticamente.");
        $this->command->info("→ Depois, as migrations de database/migrations/tenant/ rodam nele.");

        /** @var Tenant $tenant */
        $tenant = Tenant::create([
            'id'             => $tenantId,
            'name'           => 'Empresa Demo LTDA',
            'email'          => 'contato@empresademo.test',
            'plan_id'        => $plan->id,
            'status'         => 'active',
            'trial_ends_at'  => now()->addDays(30),
        ]);

        $this->command->info("✔ Tenant criado: {$tenant->id} ({$tenant->name})");
        $this->command->info("✔ Database criado: {$tenant->database()->getName()}");

        // --- Usuário "ponte" no banco CENTRAL ---
        // Esse usuário é quem vai logar pelo portal central.
        // Quando ele logar, o middleware tenant.auth vai identificar
        // o tenant pelo tenant_id dele e inicializar a tenancy.
        CentralUser::updateOrCreate(
            ['email' => 'admin@empresademo.test'],
            [
                'tenant_id'       => $tenant->id,
                'name'            => 'Admin Empresa Demo',
                'password'        => Hash::make('password'),
                'is_super_admin'  => false,
                'is_active'       => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info("✔ Usuário central criado para login:");
        $this->command->info("   E-mail: admin@empresademo.test");
        $this->command->info("   Senha:  password");

        // --- Usuário DENTRO do banco do tenant ---
        // Este usuário existe no database tenant_demo.
        // Vamos criá-lo executando no contexto do tenant.
        $tenant->run(function () {
            \App\Models\Tenant\User::updateOrCreate(
                ['email' => 'admin@empresademo.test'],
                [
                    'name'            => 'Admin Empresa Demo',
                    'password'        => Hash::make('password'),
                    'is_active'       => true,
                    'email_verified_at' => now(),
                ]
            );
        });

        $this->command->info("✔ Usuário criado DENTRO do banco do tenant.");
        $this->command->info("");
        $this->command->info("🎉 Pronto! Faça login em /login com admin@empresademo.test / password");
    }
}