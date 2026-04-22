<?php

namespace Database\Seeders;

use App\Models\Central\User;
use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CentralDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Planos iniciais ---
        $starter = Plan::updateOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter',
                'description' => 'Plano inicial para pequenas equipes.',
                'price' => 49.90,
                'billing_period' => 'monthly',
                'max_users' => 3,
                'features' => ['dashboard', 'suporte_email'],
                'is_active' => true,
                'trial_days' => 14,
            ]
        );

        $pro = Plan::updateOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Pro',
                'description' => 'Para equipes em crescimento.',
                'price' => 149.90,
                'billing_period' => 'monthly',
                'max_users' => 15,
                'features' => ['dashboard', 'relatorios', 'suporte_email', 'suporte_chat'],
                'is_active' => true,
                'trial_days' => 14,
            ]
        );

        $enterprise = Plan::updateOrCreate(
            ['slug' => 'enterprise'],
            [
                'name' => 'Enterprise',
                'description' => 'Para grandes organizações.',
                'price' => 499.90,
                'billing_period' => 'monthly',
                'max_users' => 100,
                'features' => ['dashboard', 'relatorios', 'api', 'suporte_prioritario', 'sla'],
                'is_active' => true,
                'trial_days' => 30,
            ]
        );

        // --- Superadmin ---
        User::updateOrCreate(
            ['email' => 'superadmin@saas.test'],
            [
                'tenant_id' => null,
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'is_super_admin' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✔ Planos criados: Starter, Pro, Enterprise');
        $this->command->info('✔ Superadmin criado:');
        $this->command->info('   E-mail: superadmin@saas.test');
        $this->command->info('   Senha:  password');
    }
}