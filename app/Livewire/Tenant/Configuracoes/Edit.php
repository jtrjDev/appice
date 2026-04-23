<?php

namespace App\Livewire\Tenant\Configuracoes;

use App\Models\Tenant\Configuracao;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Traits\WithTenancy;

class Edit extends Component
{
    use WithFileUploads, WithTenancy;

    public $configuracaoId;
    
    // Dados da Empresa
    public $razao_social = '';
    public $nome_fantasia = '';
    public $cpf_cnpj = '';
    public $inscricao_estadual = '';
    public $inscricao_municipal = '';
    public $rg = '';
    
    // Endereço
    public $cep = '';
    public $endereco = '';
    public $numero = '';
    public $complemento = '';
    public $bairro = '';
    public $cidade = '';
    public $estado = '';
    
    // Contato
    public $telefone = '';
    public $whatsapp = '';
    public $email_empresa = '';
    public $site = '';
    
    // Logo
    public $logo;
    public $logoPreview;
    public $logoAtual;
    
    // Configurações NF
    public $ultimo_numero_nf = '';
    public $numero_serie_nf = '';
    public $ambiente_nf = 'homologacao';
    public $emitir_nf_automatico = false; // NOVO CAMPO
    
    // Certificado Digital
    public $certificado;
    public $certificado_senha = '';
    public $certificado_validade = '';
    public $certificadoAtual;
    
    // Configurações Cupom
    public $cabecalho_cupom = '';
    public $rodape_cupom = '';
    public $exibir_logo_cupom = true;
    public $tema_cupom = 'padrao';
    
    // Configurações Fiscais
    public $regime_tributario = 'simples_nacional';
    public $codigo_atividade = '';
    public $codigo_municipio = '';
    public $codigo_pais = '1058';
    
    // Webhooks
    public $webhook_nfe = '';
    public $webhook_nfse = '';

    // Aba ativa
    public $activeTab = 'empresa';

    protected $rules = [
        // Empresa
        'razao_social' => 'required|string|max:255',
        'nome_fantasia' => 'nullable|string|max:255',
        'cpf_cnpj' => 'required|string|max:18',
        'inscricao_estadual' => 'nullable|string|max:20',
        'inscricao_municipal' => 'nullable|string|max:20',
        'rg' => 'nullable|string|max:20',
        
        // Endereço
        'cep' => 'required|string|max:10',
        'endereco' => 'required|string|max:255',
        'numero' => 'required|string|max:20',
        'complemento' => 'nullable|string|max:255',
        'bairro' => 'required|string|max:255',
        'cidade' => 'required|string|max:255',
        'estado' => 'required|string|size:2',
        
        // Contato
        'telefone' => 'nullable|string|max:20',
        'whatsapp' => 'required|string|max:20',
        'email_empresa' => 'required|email|max:255',
        'site' => 'nullable|url|max:255',
        
        // NF
        'ultimo_numero_nf' => 'nullable|string|max:20',
        'numero_serie_nf' => 'nullable|string|max:10',
        'ambiente_nf' => 'required|in:homologacao,producao',
        'emitir_nf_automatico' => 'boolean',
        
        // Certificado
        'certificado_senha' => 'nullable|string|max:255',
        
        // Cupom
        'cabecalho_cupom' => 'nullable|string',
        'rodape_cupom' => 'nullable|string',
        'exibir_logo_cupom' => 'boolean',
        'tema_cupom' => 'required|string|max:50',
        
        // Fiscal
        'regime_tributario' => 'required|in:simples_nacional,lucro_presumido,lucro_real,mei',
        'codigo_atividade' => 'nullable|string|max:20',
        'codigo_municipio' => 'nullable|string|max:20',
        'codigo_pais' => 'nullable|string|max:10',
        
        // Webhook
        'webhook_nfe' => 'nullable|url|max:255',
        'webhook_nfse' => 'nullable|url|max:255',
    ];

    protected $messages = [
        'razao_social.required' => 'A razão social é obrigatória.',
        'cpf_cnpj.required' => 'O CNPJ/CPF é obrigatório.',
        'cep.required' => 'O CEP é obrigatório.',
        'whatsapp.required' => 'O WhatsApp é obrigatório.',
        'email_empresa.required' => 'O e-mail da empresa é obrigatório.',
    ];

    public function mount()
    {
        // Pegar o ID da URL (igual ao cliente)
        $this->configuracaoId = request()->route('configuracao');
        
        // Buscar a configuração dentro do tenant
        $configuracao = Configuracao::find($this->configuracaoId);
        
        if (!$configuracao) {
            session()->flash('error', 'Configuração não encontrada.');
            return redirect()->route('tenant.configuracoes.index');
        }
        
        // Carregar dados da empresa
        $this->razao_social = $configuracao->razao_social;
        $this->nome_fantasia = $configuracao->nome_fantasia;
        $this->cpf_cnpj = $configuracao->cpf_cnpj;
        $this->inscricao_estadual = $configuracao->inscricao_estadual;
        $this->inscricao_municipal = $configuracao->inscricao_municipal;
        $this->rg = $configuracao->rg;
        
        // Endereço
        $this->cep = $configuracao->cep;
        $this->endereco = $configuracao->endereco;
        $this->numero = $configuracao->numero;
        $this->complemento = $configuracao->complemento;
        $this->bairro = $configuracao->bairro;
        $this->cidade = $configuracao->cidade;
        $this->estado = $configuracao->estado;
        
        // Contato
        $this->telefone = $configuracao->telefone;
        $this->whatsapp = $configuracao->whatsapp;
        $this->email_empresa = $configuracao->email_empresa;
        $this->site = $configuracao->site;
        
        // Logo
        $this->logoAtual = $configuracao->logo;
        
        // NF
        $this->ultimo_numero_nf = $configuracao->ultimo_numero_nf;
        $this->numero_serie_nf = $configuracao->numero_serie_nf;
        $this->ambiente_nf = $configuracao->ambiente_nf;
        $this->emitir_nf_automatico = $configuracao->emitir_nf_automatico ?? false;
        
        // Certificado
        $this->certificadoAtual = $configuracao->certificado_path;
        $this->certificado_senha = $configuracao->certificado_senha;
        $this->certificado_validade = $configuracao->certificado_validade?->format('Y-m-d');
        
        // Cupom
        $this->cabecalho_cupom = $configuracao->cabecalho_cupom;
        $this->rodape_cupom = $configuracao->rodape_cupom;
        $this->exibir_logo_cupom = $configuracao->exibir_logo_cupom;
        $this->tema_cupom = $configuracao->tema_cupom;
        
        // Fiscal
        $this->regime_tributario = $configuracao->regime_tributario;
        $this->codigo_atividade = $configuracao->codigo_atividade;
        $this->codigo_municipio = $configuracao->codigo_municipio;
        $this->codigo_pais = $configuracao->codigo_pais;
        
        // Webhooks
        $this->webhook_nfe = $configuracao->webhook_nfe;
        $this->webhook_nfse = $configuracao->webhook_nfse;
    }

    public function updatedCep()
    {
        $this->buscarCep();
    }

    public function buscarCep()
    {
        $cep = preg_replace('/[^0-9]/', '', $this->cep);
        
        if (strlen($cep) != 8) {
            return;
        }

        try {
            $response = file_get_contents("https://viacep.com.br/ws/{$cep}/json/");
            $data = json_decode($response, true);
            
            if (!isset($data['erro'])) {
                $this->endereco = $data['logradouro'] ?? '';
                $this->bairro = $data['bairro'] ?? '';
                $this->cidade = $data['localidade'] ?? '';
                $this->estado = $data['uf'] ?? '';
                $this->dispatch('cep-carregado');
            }
        } catch (\Exception $e) {
            // Erro ao buscar CEP
        }
    }

    public function updatedLogo()
    {
        $this->validate([
            'logo' => 'image|max:2048',
        ]);
        
        $this->logoPreview = $this->logo->temporaryUrl();
    }

    public function update()
    {
        $this->validate();

        try {
            $data = [
                'razao_social' => $this->razao_social,
                'nome_fantasia' => $this->nome_fantasia,
                'cpf_cnpj' => preg_replace('/[^0-9]/', '', $this->cpf_cnpj),
                'inscricao_estadual' => $this->inscricao_estadual,
                'inscricao_municipal' => $this->inscricao_municipal,
                'rg' => $this->rg,
                'cep' => preg_replace('/[^0-9]/', '', $this->cep),
                'endereco' => $this->endereco,
                'numero' => $this->numero,
                'complemento' => $this->complemento,
                'bairro' => $this->bairro,
                'cidade' => $this->cidade,
                'estado' => $this->estado,
                'telefone' => preg_replace('/[^0-9]/', '', $this->telefone),
                'whatsapp' => preg_replace('/[^0-9]/', '', $this->whatsapp),
                'email_empresa' => $this->email_empresa,
                'site' => $this->site,
                'ultimo_numero_nf' => $this->ultimo_numero_nf,
                'numero_serie_nf' => $this->numero_serie_nf,
                'ambiente_nf' => $this->ambiente_nf,
                'emitir_nf_automatico' => $this->emitir_nf_automatico,
                'certificado_senha' => $this->certificado_senha,
                'cabecalho_cupom' => $this->cabecalho_cupom,
                'rodape_cupom' => $this->rodape_cupom,
                'exibir_logo_cupom' => $this->exibir_logo_cupom,
                'tema_cupom' => $this->tema_cupom,
                'regime_tributario' => $this->regime_tributario,
                'codigo_atividade' => $this->codigo_atividade,
                'codigo_municipio' => $this->codigo_municipio,
                'codigo_pais' => $this->codigo_pais,
                'webhook_nfe' => $this->webhook_nfe,
                'webhook_nfse' => $this->webhook_nfse,
            ];

            // Upload do novo logo
            if ($this->logo) {
                if ($this->logoAtual && Storage::disk('public')->exists($this->logoAtual)) {
                    Storage::disk('public')->delete($this->logoAtual);
                }
                $path = $this->logo->store('logos', 'public');
                $data['logo'] = $path;
            }

            // Upload do novo certificado
            if ($this->certificado) {
                if ($this->certificadoAtual && Storage::disk('private')->exists($this->certificadoAtual)) {
                    Storage::disk('private')->delete($this->certificadoAtual);
                }
                $path = $this->certificado->store('certificados', 'private');
                $data['certificado_path'] = $path;
                if ($this->certificado_validade) {
                    $data['certificado_validade'] = $this->certificado_validade;
                }
            }

            $configuracao = Configuracao::findOrFail($this->configuracaoId);
            $configuracao->update($data);

            session()->flash('success', 'Configuração atualizada com sucesso!');
            return redirect()->route('tenant.configuracoes.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao atualizar: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.tenant.configuracoes.edit')->layout('layouts.tenant');
    }
}