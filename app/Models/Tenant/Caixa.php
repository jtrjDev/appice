<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class Caixa extends Model
{
    protected $fillable = [
        'user_id',
        'saldo_inicial',
        'saldo_final',
        'total_dinheiro',
        'total_credito',
        'total_debito',
        'total_pix',
        'total_vendas',
        'quantidade_vendas',
        'status',
        'aberto_em',
        'fechado_em',
        'observacao',
    ];

    protected $casts = [
        'saldo_inicial'     => 'decimal:2',
        'saldo_final'       => 'decimal:2',
        'total_dinheiro'    => 'decimal:2',
        'total_credito'     => 'decimal:2',
        'total_debito'      => 'decimal:2',
        'total_pix'         => 'decimal:2',
        'total_vendas'      => 'decimal:2',
        'aberto_em'         => 'datetime',
        'fechado_em'        => 'datetime',
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function operador()
    {
        return $this->belongsTo(\App\Models\Tenant\User::class, 'user_id');
    }

    public function isAberto(): bool
    {
        return $this->status === 'aberto';
    }

    public static function caixaAberto(): ?self
    {
        return static::where('status', 'aberto')->latest()->first();
    }
}