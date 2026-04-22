<?php

namespace App\Http\Middleware;

use App\Models\Central\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = Auth::guard('web')->user();

        if (!$user || !$user->isSuperAdmin()) {
            abort(403, 'Acesso restrito a superadministradores.');
        }

        return $next($request);
    }
}