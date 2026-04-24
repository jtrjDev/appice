<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Produto extends Model
{
    use SoftDeletes;

    protected $table = 'produtos';
    
    protected $fillable = [
        'nome', 'slug', 'codigo', 'categoria_id',
        'preco', 'preco_promocional', 'preco_custo',
        'tipo_venda', 'permite_meio', 'preco_meio', 'tamanhos', 'adicionais',
        'estoque', 'estoque_minimo',
        'ncm', 'cest', 'origem', 'aliq_icms', 'aliq_ipi', 'aliq_pis', 'aliq_cofins', 'unidade_medida',
        'descricao', 'imagem', 'icon', 'ativo', 'destaque'
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'preco_promocional' => 'decimal:2',
        'preco_custo' => 'decimal:2',
        'preco_meio' => 'decimal:2',
        'tamanhos' => 'array',
        'adicionais' => 'array',
        'ativo' => 'boolean',
        'destaque' => 'boolean',
        'permite_meio' => 'boolean',
        'estoque' => 'integer',
        'estoque_minimo' => 'integer',
        'aliq_icms' => 'decimal:2',
        'aliq_ipi' => 'decimal:2',
        'aliq_pis' => 'decimal:2',
        'aliq_cofins' => 'decimal:2',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeDestaques($query)
    {
        return $query->where('destaque', true);
    }

    public function scopeComEstoqueBaixo($query)
    {
        return $query->whereColumn('estoque', '<=', 'estoque_minimo');
    }

    // Accessors
    public function getPrecoAtualAttribute()
    {
        return $this->preco_promocional ?? $this->preco;
    }

    public function getPrecoFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->preco, 2, ',', '.');
    }

    public function getPrecoAtualFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->preco_atual, 2, ',', '.');
    }

    public function getPrecoCustoFormatadoAttribute()
    {
        return $this->preco_custo ? 'R$ ' . number_format($this->preco_custo, 2, ',', '.') : null;
    }

    public function getMargemLucroAttribute()
    {
        if (!$this->preco_custo || $this->preco_custo <= 0) return null;
        return round((($this->preco_atual - $this->preco_custo) / $this->preco_custo) * 100, 2);
    }

    // Slug automático
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($produto) {
            $produto->slug = Str::slug($produto->nome);
        });
        
        static::updating(function ($produto) {
            $produto->slug = Str::slug($produto->nome);
        });
    }
}