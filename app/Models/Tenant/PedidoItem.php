<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class PedidoItem extends Model
{
    protected $table = 'pedido_itens';
    protected $fillable = [
        'pedido_id',
        'produto_id',
        'produto_nome',
        'quantidade',
        'preco_unitario',
        'subtotal',
        'adicionais',
        'observacao',
    ];

    protected $casts = [
        'quantidade'     => 'decimal:2',
        'preco_unitario' => 'decimal:2',
        'subtotal'       => 'decimal:2',
        'adicionais'     => 'array',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}