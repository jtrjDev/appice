<?php

namespace App\Livewire\Tenant\Clientes;

use App\Models\Tenant\Cliente;
use Livewire\Component;
use App\Livewire\Traits\WithTenancy;

class Edit extends Component
{
    use WithTenancy;

    public $clienteId;
    public $nome = '';
    public $email = '';
    public $telefone = '';
    public $celular = '';
    public $cpf_cnpj = '';
    public $data_nascimento = '';
    public $cep = '';
    public $logradouro = '';
    public $numero = '';
    public $complemento = '';
    public $bairro = '';
    public $cidade = '';
    public $estado = '';
    public $observacoes = '';
    public $ativo = true;

    protected $rules = [
        'nome' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'telefone' => 'nullable|string|max:20',
        'celular' => 'nullable|string|max:20',
        'cpf_cnpj' => 'nullable|string|max:18',
        'data_nascimento' => 'nullable|date',
        'cep' => 'nullable|string|max:10',
        'logradouro' => 'nullable|string|max:255',
        'numero' => 'nullable|string|max:20',
        'complemento' => 'nullable|string|max:255',
        'bairro' => 'nullable|string|max:255',
        'cidade' => 'nullable|string|max:255',
        'estado' => 'nullable|string|max:2',
        'observacoes' => 'nullable|string',
        'ativo' => 'boolean',
    ];

    public function mount()
    {
        // Pegar o ID da URL
        $this->clienteId = request()->route('cliente');
        
        // Buscar o cliente (o tenancy já está inicializado pelo Trait)
        $cliente = Cliente::find($this->clienteId);
        
        if (!$cliente) {
            session()->flash('error', 'Cliente não encontrado.');
            return redirect()->route('tenant.clientes.index');
        }
        
        $this->nome = $cliente->nome;
        $this->email = $cliente->email;
        $this->telefone = $cliente->telefone;
        $this->celular = $cliente->celular;
        $this->cpf_cnpj = $cliente->cpf_cnpj;
        $this->data_nascimento = $cliente->data_nascimento?->format('Y-m-d');
        $this->cep = $cliente->cep;
        $this->logradouro = $cliente->logradouro;
        $this->numero = $cliente->numero;
        $this->complemento = $cliente->complemento;
        $this->bairro = $cliente->bairro;
        $this->cidade = $cliente->cidade;
        $this->estado = $cliente->estado;
        $this->observacoes = $cliente->observacoes;
        $this->ativo = $cliente->ativo;
    }

    public function update()
    {
        $this->validate();
        
        try {
            $cliente = Cliente::find($this->clienteId);
            
            if (!$cliente) {
                session()->flash('error', 'Cliente não encontrado.');
                return redirect()->route('tenant.clientes.index');
            }
            
            $cliente->update([
                'nome' => $this->nome,
                'email' => $this->email ?: null,
                'telefone' => $this->telefone ?: null,
                'celular' => $this->celular ?: null,
                'cpf_cnpj' => $this->cpf_cnpj ?: null,
                'data_nascimento' => $this->data_nascimento ?: null,
                'cep' => $this->cep ?: null,
                'logradouro' => $this->logradouro ?: null,
                'numero' => $this->numero ?: null,
                'complemento' => $this->complemento ?: null,
                'bairro' => $this->bairro ?: null,
                'cidade' => $this->cidade ?: null,
                'estado' => $this->estado ?: null,
                'observacoes' => $this->observacoes ?: null,
                'ativo' => $this->ativo,
            ]);
            
            session()->flash('success', 'Cliente atualizado com sucesso!');
            return redirect()->route('tenant.clientes.index');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao atualizar: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.tenant.clientes.edit')->layout('layouts.tenant');
    }
}