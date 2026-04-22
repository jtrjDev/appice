<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produto extends Model
{
    use SoftDeletes;
   

    protected $table = 'produtos';
    
    protected $fillable = [
        'nome', 'slug', 'codigo', 'categoria_id', 'preco', 'preco_promocional',
        'tipo_venda', 'permite_meio', 'preco_meio', 'tamanhos', 'adicionais',
        'descricao', 'imagem', 'estoque', 'ativo', 'destaque'
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'preco_promocional' => 'decimal:2',
        'preco_meio' => 'decimal:2',
        'tamanhos' => 'array',
        'adicionais' => 'array',
        'ativo' => 'boolean',
        'destaque' => 'boolean',
        'permite_meio' => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function getPrecoAtualAttribute()
    {
        return $this->preco_promocional ?? $this->preco;
    }
       // Scope para produtos ativos
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    // Scope para produtos em destaque
    public function scopeDestaques($query)
    {
        return $query->where('destaque', true);
    }

    public function getPrecoFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->preco, 2, ',', '.');
    }
        public function getPrecoAtualFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->preco_atual, 2, ',', '.');
    }
    
    // Calcula preço baseado na quantidade e tipo
    public function calcularPreco($quantidade, $tamanho = null, $meio = false)
    {
        $precoBase = $this->preco_atual;
        
        if ($meio && $this->permite_meio && $this->preco_meio) {
            return $this->preco_meio * $quantidade;
        }
        
        if ($tamanho && $this->tamanhos) {
            $tamanhoEncontrado = collect($this->tamanhos)->firstWhere('nome', $tamanho);
            if ($tamanhoEncontrado) {
                $precoBase = $tamanhoEncontrado['preco'];
            }
        }
        
        if ($this->tipo_venda == 'peso') {
            return $precoBase * $quantidade;
        }
        
        return $precoBase * $quantidade;
    }
}