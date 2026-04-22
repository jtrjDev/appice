<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class ComandaItem extends Model
{
    protected $table = 'comanda_itens';
    protected $fillable = [
        'comanda_id',
        'produto_id',
        'produto_nome',
        'quantidade',
        'preco_unitario',
        'subtotal',
        'observacao',
    ];

    protected $casts = [
        'quantidade'     => 'decimal:3',
        'preco_unitario' => 'decimal:2',
        'subtotal'       => 'decimal:2',
    ];

    public function comanda()
    {
        return $this->belongsTo(Comanda::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}