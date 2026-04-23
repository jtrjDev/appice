<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\NotaFiscal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Stancl\Tenancy\Facades\Tenancy;

class NotaFiscalController extends Controller
{
    public function downloadPdf($id)
    {
        // Inicializa o tenant
        $tenant = tenant();
        
        if (!$tenant) {
            abort(404, 'Tenant não encontrado');
        }
        
        // Busca a nota dentro do tenant
        $nota = NotaFiscal::find($id);
        
        if (!$nota || !$nota->link_pdf) {
            abort(404, 'PDF não disponível');
        }
        
        // Redireciona para o link do PDF
        return redirect($nota->link_pdf);
    }
    
    public function downloadXml($id)
    {
        $tenant = tenant();
        
        if (!$tenant) {
            abort(404, 'Tenant não encontrado');
        }
        
        $nota = NotaFiscal::find($id);
        
        if (!$nota || !$nota->link_xml) {
            abort(404, 'XML não disponível');
        }
        
        return redirect($nota->link_xml);
    }
}