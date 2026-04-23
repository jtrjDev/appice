<?php

namespace App\Livewire\Tenant\Vendas;

use App\Models\Tenant\Pedido;
use App\Models\Tenant\Cliente;
use App\Models\Tenant\Caixa;
use Livewire\Component;
use Livewire\WithPagination;
use App\Jobs\EmitirNotaFiscal; // <-- ADICIONE ESTA LINHA
use Livewire\Attributes\Computed;


class Index extends Component
{
    use WithPagination;

     // Modal NF
    public $mostrarModalNF = false;
    public $pedidoSelecionado = null;
    public $cpfCnpjNF = '';
    public $nomeClienteNF = '';
    public $tipoDocumentoNF = 'CPF';

    public string $busca = '';
    public string $dataInicio = '';
    public string $dataFim = '';
    public string $formaPagamento = '';
    public string $tipo = '';
    public string $mesa = '';
    public string $caixaId = '';
    public string $status = '';

    public bool $mostrarCupom = false;
    public ?int $pedidoCupomId = null;
    public $pedidoCupom = null;

    public function updatingBusca(): void { $this->resetPage(); }
    public function updatingDataInicio(): void { $this->resetPage(); }
    public function updatingDataFim(): void { $this->resetPage(); }
    public function updatingFormaPagamento(): void { $this->resetPage(); }
    public function updatingTipo(): void { $this->resetPage(); }
    public function updatingMesa(): void { $this->resetPage(); }
    public function updatingCaixaId(): void { $this->resetPage(); }
    public function updatingStatus(): void { $this->resetPage(); }

    

    protected $rules = [
        'cpfCnpjNF' => 'required|string|min:11|max:18',
        'nomeClienteNF' => 'required|string|min:3',
    ];


public function testarModal()
{
    $this->mostrarModalNF = true;
    session()->flash('success', 'Teste: modal deveria abrir');
}

 public function abrirModalNF($pedidoId)
{
    // Removeu o dd
    $this->pedidoSelecionado = Pedido::find($pedidoId);
    $this->cpfCnpjNF = '';
    $this->nomeClienteNF = '';
    $this->tipoDocumentoNF = 'CPF';
    $this->mostrarModalNF = true;
    
    // Flash para confirmar que o método foi executado
    session()->flash('success', 'Modal deveria abrir para o pedido ' . $pedidoId);
}
    public function fecharModalNF()
    {
        $this->mostrarModalNF = false;
        $this->pedidoSelecionado = null;
        $this->reset(['cpfCnpjNF', 'nomeClienteNF']);
    }

    public function emitirNotaComDocumento()
    {
        $this->validate();

        $cpfCnpj = preg_replace('/[^0-9]/', '', $this->cpfCnpjNF);
        
        // Verificar se é CPF (11 dígitos) ou CNPJ (14 dígitos)
        $tipo = strlen($cpfCnpj) == 11 ? 'cpf' : 'cnpj';
        $modelo = $tipo == 'cpf' ? 'nfce' : 'nfe';
        
        // Criar cliente logo com os dados informados
        $cliente = Cliente::updateOrCreate(
            ['cpf_cnpj' => $cpfCnpj],
            [
                'nome' => $this->nomeClienteNF,
                'ativo' => true,
            ]
        );
        
        // Associar cliente ao pedido
        $this->pedidoSelecionado->cliente_id = $cliente->id;
        $this->pedidoSelecionado->save();
        
        // Dispara a job
        EmitirNotaFiscal::dispatch($this->pedidoSelecionado->id, tenant()->id);
        
        session()->flash('success', "Nota fiscal ($modelo) solicitada para {$this->nomeClienteNF}!");
        
        $this->fecharModalNF();
    }


public function abrirCupom(int $id): void
{
    $this->pedidoCupomId = $id;
    $this->pedidoCupom = Pedido::with(['itens', 'cliente'])->findOrFail($id);
    $this->mostrarCupom = true;
}

public function fecharCupom(): void
{
    $this->mostrarCupom = false;
    $this->pedidoCupomId = null;
    $this->pedidoCupom = null;
}

public function emitirNota($pedidoId)
{
    try {
        $pedido = Pedido::find($pedidoId);
        
        if (!$pedido->cliente || !$pedido->cliente->cpf_cnpj) {
            $this->dispatch('pdv-aviso', mensagem: 'Cliente sem CPF/CNPJ cadastrado. Edite o cliente e adicione o documento.');
            return;
        }
        
        // Dispara a job
        EmitirNotaFiscal::dispatch($pedidoId, tenant()->id);
        
        $this->dispatch('pdv-sucesso', mensagem: 'Nota fiscal solicitada com sucesso!');
        
        // Opcional: recarregar a página para mostrar a nota
        // $this->dispatch('refresh-component');
        
    } catch (\Exception $e) {
        $this->dispatch('pdv-aviso', mensagem: 'Erro: ' . $e->getMessage());
    }
}

    public function limparFiltros(): void
    {
        $this->reset([
            'busca',
            'dataInicio',
            'dataFim',
            'formaPagamento',
            'tipo',
            'mesa',
            'caixaId',
            'status',
        ]);

        $this->resetPage();
    }

    #[Computed]
    public function caixas()
    {
        return Caixa::query()
            ->orderByDesc('id')
            ->get();
    }

    #[Computed]
    public function pedidos()
    {
        return Pedido::query()
            ->with(['itens', 'cliente', 'caixa'])
            ->when($this->busca, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('numero_pedido', 'like', '%' . $this->busca . '%')
                        ->orWhere('mesa', 'like', '%' . $this->busca . '%')
                        ->orWhereHas('cliente', fn($c) => $c->where('nome', 'like', '%' . $this->busca . '%'));
                });
            })
            ->when($this->dataInicio, fn($q) => $q->whereDate('created_at', '>=', $this->dataInicio))
            ->when($this->dataFim, fn($q) => $q->whereDate('created_at', '<=', $this->dataFim))
            ->when($this->tipo, fn($q) => $q->where('tipo', $this->tipo))
            ->when($this->mesa, fn($q) => $q->where('mesa', 'like', '%' . $this->mesa . '%'))
            ->when($this->caixaId, fn($q) => $q->where('caixa_id', $this->caixaId))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->formaPagamento, function ($q) {
                $forma = $this->formaPagamento;
                $q->whereJsonContains('pagamentos', [['forma' => $forma]]);
            })
            ->orderByDesc('id')
            ->paginate(15);
    }

    public function verVenda(int $id)
    {
        return redirect()->route('tenant.vendas.show', ['id' => $id]);
    }

    public function emitirCupom(int $id)
    {
        return redirect()->route('tenant.vendas.cupom', ['id' => $id]);
    }

    public function render()
    {
        return view('livewire.tenant.vendas.index')
            ->layout('layouts.tenant');
    }
}