<?php

namespace App\Livewire\Tenant\Vendas;

use App\Models\Tenant\Pedido;
use App\Models\Tenant\Cliente;
use App\Models\Tenant\Caixa;
use Livewire\Component;
use Livewire\WithPagination;
use App\Jobs\EmitirNotaFiscal; // <-- ADICIONE ESTA LINHA
use Livewire\Attributes\Computed;
use App\Models\Tenant\NotaFiscal;


class Index extends Component
{
    use WithPagination;

     // Modal NF
   public $mostrarModalNF = false;
public $pedidoSelecionado = null;

public $cpfCnpjNF = '';
public $nomeClienteNF = '';

public $inscricaoEstadualNF = '';
public $telefoneNF = '';
public $enderecoNF = '';
public $numeroNF = '';
public $bairroNF = '';
public $cidadeNF = '';
public $ufNF = 'PR';
public $cepNF = '';

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
    public string $statusNF = '';

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

public function updatingStatusNF(): void 
{ 
    $this->resetPage(); 
}
 public function abrirModalNF($pedidoId)
{
    $this->resetValidation();

    $this->pedidoSelecionado = Pedido::with(['cliente', 'notaFiscal'])->findOrFail($pedidoId);

    if ($this->pedidoSelecionado->notaFiscal && $this->pedidoSelecionado->notaFiscal->status === 'autorizada') {
        session()->flash('error', 'Este pedido já possui nota fiscal autorizada.');
        return;
    }

    if ($this->pedidoSelecionado->notaFiscal && $this->pedidoSelecionado->notaFiscal->status === 'processando') {
        session()->flash('error', 'A nota deste pedido ainda está em processamento.');
        return;
    }

    $cliente = $this->pedidoSelecionado->cliente;

    $this->cpfCnpjNF = $cliente?->cpf_cnpj ?? '';
    $this->nomeClienteNF = $cliente?->nome ?? '';

    $this->inscricaoEstadualNF = $cliente?->inscricao_estadual ?? '';
    $this->telefoneNF = $cliente?->telefone ?? '';
    $this->enderecoNF = $cliente?->endereco ?? '';
    $this->numeroNF = $cliente?->numero ?? '';
    $this->bairroNF = $cliente?->bairro ?? '';
    $this->cidadeNF = $cliente?->cidade ?? '';
    $this->ufNF = $cliente?->uf ?? 'PR';
    $this->cepNF = $cliente?->cep ?? '';

    $this->mostrarModalNF = true;
}
    public function fecharModalNF()
    {
        $this->mostrarModalNF = false;
        $this->pedidoSelecionado = null;
        $this->reset(['cpfCnpjNF', 'nomeClienteNF']);
    }

    public function emitirNotaComDocumento()
{
    if (!$this->pedidoSelecionado) {
        session()->flash('error', 'Nenhum pedido selecionado para emissão.');
        return;
    }

    $pedido = Pedido::with('notaFiscal')->findOrFail($this->pedidoSelecionado->id);

    if ($pedido->notaFiscal && $pedido->notaFiscal->status === 'autorizada') {
        session()->flash('error', 'Este pedido já possui nota fiscal autorizada.');
        $this->fecharModalNF();
        return;
    }

    if ($pedido->notaFiscal && $pedido->notaFiscal->status === 'processando') {
        session()->flash('error', 'A nota deste pedido ainda está em processamento.');
        $this->fecharModalNF();
        return;
    }

    $cpfCnpj = preg_replace('/[^0-9]/', '', $this->cpfCnpjNF ?? '');

    $isCpf = strlen($cpfCnpj) === 11;
    $isCnpj = strlen($cpfCnpj) === 14;

    if (!$isCpf && !$isCnpj) {
        $this->addError('cpfCnpjNF', 'Informe um CPF ou CNPJ válido.');
        return;
    }

    $tipoDocumento = $isCnpj ? 'CNPJ' : 'CPF';
    $modelo = $isCnpj ? 'nfe' : 'nfce';
    $referencia = ($isCnpj ? 'NFE_' : 'NFC_') . $pedido->id;

    $regras = [
        'cpfCnpjNF' => 'required|string|min:11|max:18',
        'nomeClienteNF' => 'required|string|min:3',
    ];

    $mensagens = [
        'cpfCnpjNF.required' => 'Informe o CPF ou CNPJ.',
        'nomeClienteNF.required' => 'Informe o nome do cliente.',
        'nomeClienteNF.min' => 'O nome precisa ter pelo menos 3 caracteres.',
    ];

    if ($isCnpj) {
        $regras = array_merge($regras, [
            'telefoneNF' => 'required|string|min:10',
            'enderecoNF' => 'required|string|min:2',
            'numeroNF' => 'required|string|min:1',
            'bairroNF' => 'required|string|min:2',
            'cidadeNF' => 'required|string|min:2',
            'ufNF' => 'required|string|size:2',
            'cepNF' => 'required|string|min:8',
        ]);

        $mensagens = array_merge($mensagens, [
            'telefoneNF.required' => 'Informe o telefone do destinatário.',
            'enderecoNF.required' => 'Informe o endereço do destinatário.',
            'numeroNF.required' => 'Informe o número.',
            'bairroNF.required' => 'Informe o bairro.',
            'cidadeNF.required' => 'Informe o município.',
            'ufNF.required' => 'Informe a UF.',
            'ufNF.size' => 'A UF deve ter 2 letras.',
            'cepNF.required' => 'Informe o CEP.',
        ]);
    }

    $this->validate($regras, $mensagens);

    $telefone = preg_replace('/[^0-9]/', '', $this->telefoneNF ?? '');
    $cep = preg_replace('/[^0-9]/', '', $this->cepNF ?? '');

    if ($isCnpj && strlen($telefone) < 10) {
        $this->addError('telefoneNF', 'Informe um telefone válido com DDD.');
        return;
    }

    if ($isCnpj && strlen($cep) !== 8) {
        $this->addError('cepNF', 'Informe um CEP válido com 8 dígitos.');
        return;
    }

    $dadosCliente = [
        'nome' => $this->nomeClienteNF,
        'tipo_documento' => $tipoDocumento,
        'ativo' => true,
    ];

    if ($isCnpj) {
        $dadosCliente = array_merge($dadosCliente, [
            'inscricao_estadual' => $this->inscricaoEstadualNF ?: null,
            'telefone' => $telefone,
            'endereco' => $this->enderecoNF,
            'numero' => $this->numeroNF,
            'bairro' => $this->bairroNF,
            'cidade' => $this->cidadeNF,
            'uf' => strtoupper($this->ufNF ?: 'PR'),
            'cep' => $cep,
        ]);
    }

    $cliente = Cliente::updateOrCreate(
        ['cpf_cnpj' => $cpfCnpj],
        $dadosCliente
    );

    $pedido->cliente_id = $cliente->id;
    $pedido->save();

    NotaFiscal::updateOrCreate(
        ['referencia' => $referencia],
        [
            'pedido_id' => $pedido->id,
            'modelo' => $modelo,
            'status' => 'processando',
            'mensagem_erro' => null,
        ]
    );

    EmitirNotaFiscal::dispatch($this->pedidoSelecionado->id, tenant()->id);

    session()->flash('success', "Nota fiscal solicitada para o pedido #{$pedido->numero_pedido}.");

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
        EmitirNotaFiscal::dispatch($this->pedidoSelecionado->id, tenant()->id);
        
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
            'statusNF',
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
            ->with(['itens', 'cliente', 'caixa','notaFiscal'])
            ->when($this->busca, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('numero_pedido', 'like', '%' . $this->busca . '%')
                        ->orWhere('mesa', 'like', '%' . $this->busca . '%')
                        ->orWhereHas('cliente', fn($c) => $c->where('nome', 'like', '%' . $this->busca . '%'));
                });
            })
            ->when($this->statusNF === 'emitida', function ($q) {
    $q->whereHas('notaFiscal', function ($nota) {
        $nota->where('status', 'autorizada');
    });
})
->when($this->statusNF === 'pendente', function ($q) {
    $q->whereDoesntHave('notaFiscal');
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