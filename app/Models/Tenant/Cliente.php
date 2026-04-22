<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    // NÃO defina $connection - deixe o Laravel usar a conexão padrão
    // O tenancy vai trocar a conexão automaticamente
    
    protected $table = 'clientes';

    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'celular',
        'cpf_cnpj',
        'data_nascimento',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'observacoes',
        'ativo',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'ativo' => 'boolean',
    ];

    public function getTelefoneFormatadoAttribute()
    {
        if (!$this->telefone) return null;
        return preg_replace('/(\d{2})(\d{4,5})(\d{4})/', '($1) $2-$3', $this->telefone);
    }

    public function getCpfCnpjFormatadoAttribute()
    {
        if (!$this->cpf_cnpj) return null;
        
        if (strlen($this->cpf_cnpj) == 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->cpf_cnpj);
        }
        
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $this->cpf_cnpj);
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }
}