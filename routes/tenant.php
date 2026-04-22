<?php

declare(strict_types=1);

use App\Livewire\Tenant\Dashboard;
use App\Livewire\Tenant\Clientes\Index as ClientesIndex;
use App\Livewire\Tenant\Clientes\Create as ClientesCreate;
use App\Livewire\Tenant\Clientes\Edit as ClientesEdit;
use App\Livewire\Tenant\PDV\Index as PDVIndex;
use App\Livewire\Tenant\PDV\Caixa as PDVCaixa;
use Illuminate\Support\Facades\Route;
use App\Livewire\Tenant\PDV\Mesas as PDVMesas;

use Illuminate\Support\Facades\DB;

Route::middleware(['web', 'auth:web', 'tenant.auth'])
    ->prefix('app')
    ->name('tenant.')
    ->group(function () {
        // Debug: mostrar qual banco está ativo
       Route::get('/', Dashboard::class)->name('dashboard');
Route::get('/pdv', PDVIndex::class)->name('pdv');
Route::get('/caixa', PDVCaixa::class)->name('caixa');
Route::get('/clientes', ClientesIndex::class)->name('clientes.index');
Route::get('/clientes/create', ClientesCreate::class)->name('clientes.create');
Route::get('/clientes/{cliente}/edit', ClientesEdit::class)->name('clientes.edit');

// dentro do grupo:
Route::get('/mesas', PDVMesas::class)->name('mesas');
    });