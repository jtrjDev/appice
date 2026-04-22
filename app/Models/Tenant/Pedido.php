<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;


class Pedido extends Model
{

    protected $fillable = [
        'caixa_id',
        'numero_pedido',
        'cliente_id',
        'tipo',
        'mesa',
        'subtotal',
        'taxa_entrega',
        'desconto',
        'total',
        'status',
        'observacoes',
        'pagamentos',
        'atendente_id',
    ];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'taxa_entrega' => 'decimal:2',
        'desconto'     => 'decimal:2',
        'total'        => 'decimal:2',
        'pagamentos'   => 'array',
    ];

    public function caixa()
    {
        return $this->belongsTo(Caixa::class);
    }

    public function itens()
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }


    public static function gerarNumero(): string
    {
        $ultimo = static::latest('id')->value('numero_pedido');
        $numero = $ultimo ? ((int) filter_var($ultimo, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;
        return str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
}
