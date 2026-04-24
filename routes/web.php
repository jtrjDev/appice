<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Livewire\Admin\Tenants\Index as TenantsIndex;
use App\Livewire\Admin\Tenants\Create as TenantsCreate;
use App\Livewire\Admin\Tenants\Edit as TenantsEdit;
use App\Livewire\Admin\Usuarios\Index as UsuariosIndex;
use App\Livewire\Admin\Usuarios\Create as UsuariosCreate;
use App\Livewire\Admin\Usuarios\Edit as UsuariosEdit;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas públicas
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| Autenticação (central)
|--------------------------------------------------------------------------
*/

Route::middleware('guest:web')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth:web')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Painel do Superadmin (central, sem tenancy)
|--------------------------------------------------------------------------
| Só superadmins entram aqui. O middleware 'superadmin' garante isso.
*/

Route::middleware(['auth:web', 'superadmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // CRUD de Tenants com Livewire
        Route::get('/tenants', TenantsIndex::class)->name('tenants.index');
        Route::get('/tenants/create', TenantsCreate::class)->name('tenants.create');
        Route::get('/tenants/{tenant}/edit', TenantsEdit::class)->name('tenants.edit');
        
        // Usuários - ADICIONE ESTAS ROTAS
        Route::get('/usuarios', UsuariosIndex::class)->name('usuarios.index');
        Route::get('/usuarios/create', UsuariosCreate::class)->name('usuarios.create');
        Route::get('/usuarios/{usuario}/edit', UsuariosEdit::class)->name('usuarios.edit');

        // Futuras rotas de Plans e Users
        // Route::get('/plans', PlansIndex::class)->name('plans.index');
        // Route::get('/users', UsersIndex::class)->name('users.index');
    });

/*
|--------------------------------------------------------------------------
| Área do Tenant (carrega tenancy via middleware tenant.auth)
|--------------------------------------------------------------------------
| As rotas internas do tenant ficam em routes/tenant.php para organização.
*/
require __DIR__ . '/tenant.php';
