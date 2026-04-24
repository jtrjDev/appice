<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class Sangria extends Model
{
    protected $table = 'sangrias';

    protected $fillable = [
        'caixa_id', 'valor', 'tipo', 'motivo', 'user_id'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
    ];

    public function caixa()
    {
        return $this->belongsTo(Caixa::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}