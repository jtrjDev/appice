<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Caixa;
use App\Models\Tenant\Configuracao;
use Illuminate\Http\Request;

class CaixaController extends Controller
{
    public function relatorio($id, Request $request)
    {
        $caixa = Caixa::with('operador')->findOrFail($id);
        $config = Configuracao::first();
        $autoPrint = $request->query('print', false);
        
        return view('tenant.caixa.relatorio', compact('caixa', 'config', 'autoPrint'));
    }
}