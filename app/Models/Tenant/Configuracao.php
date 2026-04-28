<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class Configuracao extends Model
{
    

    protected $table = 'configuracoes';

    protected $fillable = [
        'razao_social', 'nome_fantasia', 'cpf_cnpj', 'inscricao_estadual',
        'inscricao_municipal', 'rg', 'cep', 'endereco', 'numero', 'complemento',
        'bairro', 'cidade', 'estado', 'telefone', 'whatsapp', 'email_empresa',
        'site', 'logo', 'ultimo_numero_nf', 'numero_serie_nf', 'ambiente_nf',
        'certificado_path', 'certificado_senha', 'certificado_validade',
        'cabecalho_cupom', 'rodape_cupom', 'exibir_logo_cupom', 'tema_cupom',
        'regime_tributario', 'codigo_atividade', 'codigo_municipio', 'codigo_pais',
        'webhook_nfe', 'emitir_nf_automatico', 'webhook_nfse', 'focus_token',
        //Novos campos
        'tipo_negocio', 'permite_fracionamento', 'permite_meia_porcao',
        'tipo_venda_padrao', 'unidade_medida_padrao'
    ];

    protected $casts = [
        'exibir_logo_cupom' => 'boolean',
        'certificado_validade' => 'date',
        'permite_fracionamento' => 'boolean',
        'permite_meia_porcao' => 'boolean',
    ];


    // Retorna o ícone padrão baseado no tipo de negócio
    public function getIconePadraoAttribute()
    {
        return match ($this->tipo_negocio) {
            'sorveteria' => '🍦',
            'pizzaria' => '🍕',
            'lanchonete' => '🍔',
            'restaurante' => '🍽️',
            'acai' => '🥣',
            'hamburgueria' => '🍔',
            default => '🏪'
        };
    }

    // Retorna as configurações de PDV baseado no tipo de negócio
    public function getPdvConfigAttribute()
{
    return match($this->tipo_negocio) {
        'sorveteria' => [
            'tipo_venda_padrao' => 'peso',
            'unidade_padrao' => 'KG',
            'permite_meio' => false,
            'exibir_peso' => true,
            'exibir_tamanhos' => true,
            'exibir_adicionais' => false,
            'categorias_padrao' => ['Sorvetes', 'Casquinhas', 'Milkshakes', 'Açaí', 'Picolés']
        ],
        'pizzaria' => [
            'tipo_venda_padrao' => 'fracionado',
            'unidade_padrao' => 'UN',
            'permite_meio' => true,
            'exibir_tamanhos' => true,
            'exibir_adicionais' => true,
            'categorias_padrao' => ['Pizzas', 'Bebidas', 'Porções', 'Sobremesas']
        ],
        'lanchonete' => [
            'tipo_venda_padrao' => 'unidade',
            'unidade_padrao' => 'UN',
            'permite_meio' => false,
            'exibir_tamanhos' => false,
            'exibir_adicionais' => true,
            'categorias_padrao' => ['Lanches', 'Bebidas', 'Porções', 'Sobremesas']
        ],
        'acai' => [
            'tipo_venda_padrao' => 'unidade',
            'unidade_padrao' => 'UN',
            'permite_meio' => true,
            'exibir_tamanhos' => true,
            'exibir_adicionais' => true,
            'categorias_padrao' => ['Açaí', 'Sorvetes', 'Frutos', 'Adicionais', 'Bebidas']
        ],
        default => [
            'tipo_venda_padrao' => 'unidade',
            'unidade_padrao' => 'UN',
            'permite_meio' => false,
            'exibir_tamanhos' => false,
            'exibir_adicionais' => false,
            'categorias_padrao' => ['Produtos']
        ]
    };
}


    // Accessors
    public function getCpfCnpjFormatadoAttribute()
    {
        if (!$this->cpf_cnpj) return null;
        
        if (strlen($this->cpf_cnpj) == 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->cpf_cnpj);
        }
        
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $this->cpf_cnpj);
    }

    public function getTelefoneFormatadoAttribute()
    {
        if (!$this->telefone) return null;
        return preg_replace('/(\d{2})(\d{4,5})(\d{4})/', '($1) $2-$3', $this->telefone);
    }

    public function getWhatsappFormatadoAttribute()
    {
        if (!$this->whatsapp) return null;
        return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $this->whatsapp);
    }

    public function getCepFormatadoAttribute()
    {
        if (!$this->cep) return null;
        return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $this->cep);
    }
}