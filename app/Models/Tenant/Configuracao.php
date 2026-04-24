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
        'webhook_nfe', 'emitir_nf_automatico', 'webhook_nfse'
    ];

    protected $casts = [
        'exibir_logo_cupom' => 'boolean',
        'certificado_validade' => 'date',
    ];

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