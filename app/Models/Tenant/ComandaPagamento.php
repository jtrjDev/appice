<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class ComandaPagamento extends Model
{
    protected $fillable = [
        'comanda_id',
        'forma',
        'valor',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
    ];

    public function comanda()
    {
        return $this->belongsTo(Comanda::class);
    }
}