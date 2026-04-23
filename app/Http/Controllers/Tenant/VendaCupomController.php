<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Pedido;
use Barryvdh\DomPDF\Facade\Pdf;



class VendaCupomController extends Controller
{
    public function show(int $id)
    {
        $pedido = Pedido::with(['itens', 'cliente'])->findOrFail($id);

        return view('tenant.vendas.cupom', compact('pedido'));
    }
    public function pdf(int $id)
{
    $pedido = Pedido::with(['itens', 'cliente'])->findOrFail($id);

    $pdf = Pdf::loadView('tenant.vendas.cupom', compact('pedido'));

    return $pdf->download('cupom-' . $pedido->numero_pedido . '.pdf');
}


}