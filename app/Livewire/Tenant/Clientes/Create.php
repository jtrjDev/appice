<?php

namespace App\Livewire\Tenant\Clientes;

use App\Models\Tenant\Cliente;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Livewire\Traits\WithTenancy;

class Create extends Component
{
    use WithTenancy;

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
    ];

    // Método para inicializar o tenancy
   
    public function save()
    {
        // Validar
        $this->validate();

        try {
            $cliente = Cliente::create([
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

            session()->flash('success', "Cliente '{$this->nome}' cadastrado com sucesso!");
            return redirect()->route('tenant.clientes.index');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                $this->addError('email', 'Este e-mail já está cadastrado.');
                $this->addError('cpf_cnpj', 'Este CPF/CNPJ já está cadastrado.');
            } else {
                session()->flash('error', 'Erro ao salvar: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erro: ' . $e->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.tenant.clientes.create')->layout('layouts.tenant');
    }
}
