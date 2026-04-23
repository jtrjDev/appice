<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class NotaFiscal extends Model
{
    protected $table = 'notas_fiscais';

    protected $fillable = [
        'pedido_id', 
        'modelo', 
        'referencia', 
        'numero_nota',
        'chave_acesso', 
        'link_xml', 
        'link_pdf', 
        'status',
        'mensagem_erro', 
        'retorno_completo'
    ];

    protected $casts = [
        'retorno_completo' => 'array',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function isAutorizada()
    {
        return $this->status === 'autorizada';
    }

    public function isRejeitada()
    {
        return $this->status === 'rejeitada';
    }
}