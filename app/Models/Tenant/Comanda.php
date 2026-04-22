<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class Comanda extends Model
{
    protected $fillable = [
        'caixa_id',
        'mesa',
        'status',
        'total',
        'total_pago',
        'observacao',
        'fechada_em',
    ];

    protected $casts = [
        'total'      => 'decimal:2',
        'total_pago' => 'decimal:2',
        'fechada_em' => 'datetime',
    ];

    public function itens()
    {
        return $this->hasMany(ComandaItem::class);
    }

    public function pagamentos()
    {
        return $this->hasMany(ComandaPagamento::class);
    }

    public function caixa()
    {
        return $this->belongsTo(Caixa::class);
    }

    public function getTotalRestanteAttribute(): float
    {
        return round((float) $this->total - (float) $this->total_pago, 2);
    }

    public function isAberta(): bool
    {
        return $this->status === 'aberta';
    }

    public static function buscarMesa(string $mesa): ?self
    {
        return static::where('mesa', $mesa)
            ->where('status', 'aberta')
            ->with(['itens', 'pagamentos'])
            ->first();
    }
}