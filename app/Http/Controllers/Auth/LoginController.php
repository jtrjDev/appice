<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Central\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Exibe o formulário de login.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Processa o login.
     * Após autenticar no guard central, redireciona conforme o tipo do usuário.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::guard('web')->user();

        // Superadmin → painel administrativo central
        if ($user->isSuperAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        // Usuário comum → dashboard do tenant
        // (o middleware tenant.auth vai inicializar a tenancy automaticamente nas próximas requests)
        if ($user->hasTenant()) {
            return redirect()->intended(route('tenant.dashboard'));
        }

        // Se chegou aqui, é um usuário sem tenant e sem superadmin → desloga
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withErrors([
            'email' => 'Usuário sem tenant associado. Entre em contato com o suporte.',
        ]);
    }

    /**
     * Processa o logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        // Se havia tenancy inicializada, encerra
        if (tenancy()->initialized) {
            tenancy()->end();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}