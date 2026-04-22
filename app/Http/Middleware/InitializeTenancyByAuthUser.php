<?php

namespace App\Http\Middleware;

use App\Models\Central\User;
use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenancyByAuthUser
{
    public function handle(Request $request, Closure $next): Response
    {
        // Se não está autenticado, deixa passar
        if (!Auth::guard('web')->check()) {
            return $next($request);
        }

        /** @var User $user */
        $user = Auth::guard('web')->user();

        // Superadmin: não inicializa tenancy
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Usuário comum precisa ter tenant_id
        if (!$user->hasTenant()) {
            Auth::guard('web')->logout();
            abort(403, 'Usuário sem tenant associado.');
        }

        /** @var Tenant|null $tenant */
        $tenant = Tenant::find($user->tenant_id);

        if (!$tenant) {
            Auth::guard('web')->logout();
            abort(404, "Tenant {$user->tenant_id} não encontrado.");
        }

        // Bloqueia acesso a tenants suspensos/cancelados
        if (!$tenant->isActive()) {
            Auth::guard('web')->logout();
            abort(403, 'Este tenant está inativo. Entre em contato com o suporte.');
        }

        // 🔑 SEMPRE inicializa o tenancy (mesmo se já estiver inicializado)
        // Isso garante que nas requisições AJAX também funcione
        if (tenancy()->initialized) {
            tenancy()->end();
        }
        
        tenancy()->initialize($tenant);

        return $next($request);
    }
}