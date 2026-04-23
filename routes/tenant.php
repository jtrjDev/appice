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
use App\Livewire\Tenant\Configuracoes\Index as ConfiguracoesIndex;
use App\Livewire\Tenant\Configuracoes\Create as ConfiguracoesCreate;
use App\Livewire\Tenant\Configuracoes\Edit as ConfiguracoesEdit;
use App\Livewire\Tenant\Produtos\Index as ProdutosIndex;
use App\Livewire\Tenant\Produtos\Create as ProdutosCreate;
use App\Livewire\Tenant\Produtos\Edit as ProdutosEdit;
use App\Livewire\Tenant\Vendas\Index as VendasIndex;
use App\Livewire\Tenant\Vendas\Show as VendasShow;
use App\Http\Controllers\Tenant\VendaCupomController;

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

// Adicione no grupo de rotas:
Route::get('/configuracoes', ConfiguracoesIndex::class)->name('configuracoes.index');
Route::get('/configuracoes/create', ConfiguracoesCreate::class)->name('configuracoes.create');
Route::get('/configuracoes/{configuracao}/edit', ConfiguracoesEdit::class)->name('configuracoes.edit');

// Adicione dentro do grupo de rotas:
Route::get('/produtos', ProdutosIndex::class)->name('produtos.index');
Route::get('/produtos/create', ProdutosCreate::class)->name('produtos.create');
Route::get('/produtos/{produto}/edit', ProdutosEdit::class)->name('produtos.edit');

Route::get('/vendas', VendasIndex::class)->name('vendas');
Route::get('/vendas/{id}', VendasShow::class)->name('vendas.show');
Route::get('/vendas/{id}/cupom', [VendaCupomController::class, 'show'])->name('vendas.cupom');
Route::get('/vendas/{id}/cupom/pdf', [VendaCupomController::class, 'pdf'])->name('vendas.cupom.pdf');
// dentro do grupo:
Route::get('/mesas', PDVMesas::class)->name('mesas');
    });