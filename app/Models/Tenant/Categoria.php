<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

   
    protected $table = 'categorias';

    protected $fillable = [
        'nome',
        'slug',
        'icone',
        'cor',
        'ordem',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'ordem' => 'integer',
    ];

    public function produtos()
    {
        return $this->hasMany(Produto::class);
    }

    // Scope para categorias ativas ordenadas
    public function scopeAtivasOrdenadas($query)
    {
        return $query->where('ativo', true)->orderBy('ordem')->orderBy('nome');
    }
}