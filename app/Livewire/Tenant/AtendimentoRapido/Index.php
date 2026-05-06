<?php

namespace App\Livewire\Tenant\AtendimentoRapido;

use App\Livewire\Traits\WithToast;
use App\Models\Tenant\Caixa;
use App\Models\Tenant\Cliente;
use App\Models\Tenant\Pedido;
use App\Models\Tenant\PedidoItem;
use App\Models\Tenant\Produto;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    use WithToast;

    public string $tipoPedido = '';

    public bool $mostrarModalTelefone = false;

    public string $telefoneBusca = '';

    public ?int $clienteId = null;
    public string $nomeCliente = '';
    public string $telefoneCliente = '';
    public string $enderecoCliente = '';
    public string $numeroCliente = '';
    public string $complementoCliente = '';
    public string $bairroCliente = '';
    public string $cidadeCliente = '';
    public string $ufCliente = 'PR';

    public string $mesa = '';
    public ?int $pedidoAbertoId = null;

    public string $codigoProduto = '';
    public float $quantidadeInput = 1;

    public array $carrinho = [];
    public array $ultimosPedidos = [];

    public string $observacao = '';

    public ?int $tenantUserId = null;

    public function mount(): void
    {
        $this->tenantUserId = \App\Models\Tenant\User::where('email', auth()->user()->email)->value('id');
        $this->limparTela(false);
    }

    public function updatedMesa(): void
    {
        if ($this->tipoPedido !== 'mesa') {
            return;
        }

        $mesa = trim($this->mesa);

        if ($mesa === '') {
            $this->pedidoAbertoId = null;
            $this->carrinho = [];
            return;
        }

        $pedido = Pedido::with('itens')
                ->where('tipo', 'mesa')
                ->where('mesa', $mesa)
                ->whereIn('status', ['pendente', 'preparando'])
                ->latest('id')
                ->first();

        if (!$pedido) {
            $this->pedidoAbertoId = null;
            $this->carrinho = [];
            $this->observacao = '';
            $this->toastInfo("Nova mesa {$mesa} iniciada.");
            $this->dispatch('focar-codigo-atendimento');
            return;
        }

        $this->pedidoAbertoId = $pedido->id;
        $this->observacao = $pedido->observacoes ?? '';
        $this->carrinho = [];

        foreach ($pedido->itens as $item) {
            $this->carrinho[(string) $item->produto_id]=[
                'produto_id'        => $item->produto->id,
                'produto_nome'      => $item->produto_nome,
                'quantidade'        => $item->quantidade,
                'preco_unitario'    => (float) $item->preco_unitario,
                'subtotal'          => (float) $item->subtotal,
                'observacao'        => $item->observacao,
            ];
        }

        $this->toastSuccess("Mesa {$mesa} carregada.");
        $this->dispatch('focar-codigo-atendimento');
    }

    public function selecionarTipoPedido(string $tipo): void
    {
        if (!in_array($tipo, ['balcao', 'entrega', 'mesa'])) {
            return;
        }

        $this->tipoPedido = $tipo;

        $this->reset([
            'clienteId',
            'nomeCliente',
            'telefoneCliente',
            'telefoneBusca',
            'enderecoCliente',
            'numeroCliente',
            'complementoCliente',
            'bairroCliente',
            'cidadeCliente',
            'mesa',
            'ultimosPedidos',
        ]);

        $this->ufCliente = 'PR';

        if ($tipo === 'entrega') {
            $this->mostrarModalTelefone = true;
            return;
        }

        $this->mostrarModalTelefone = false;
        $this->dispatch('focar-codigo-atendimento');
    }

    public function fecharModalTelefone(): void
    {
        $this->mostrarModalTelefone = false;
        $this->dispatch('focar-codigo-atendimento');
    }

    public function buscarClientePorTelefone(): void
    {
        $telefone = $this->somenteNumeros($this->telefoneBusca);

        if (strlen($telefone) < 8) {
            $this->addError('telefoneBusca', 'Informe um telefone válido.');
            return;
        }

        $this->resetErrorBag('telefoneBusca');

        $cliente = Cliente::query()
            ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(telefone, '(', ''), ')', ''), '-', ''), ' ', '') = ?", [$telefone])
            ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(celular, '(', ''), ')', ''), '-', ''), ' ', '') = ?", [$telefone])
            ->first();

        if ($cliente) {
            $this->carregarCliente($cliente);
            $this->toastSuccess('Cliente carregado.');
        } else {
            $this->clienteId = null;
            $this->telefoneCliente = $telefone;
            $this->nomeCliente = '';
            $this->enderecoCliente = '';
            $this->numeroCliente = '';
            $this->complementoCliente = '';
            $this->bairroCliente = '';
            $this->cidadeCliente = '';
            $this->ufCliente = 'PR';
            $this->ultimosPedidos = [];

            $this->toastInfo('Cliente não encontrado. Preencha os dados para cadastrar.');
        }

        $this->carregarUltimosPedidosPorTelefone($telefone);

        $this->mostrarModalTelefone = false;
        $this->dispatch('focar-codigo-atendimento');
    }

    private function carregarCliente(Cliente $cliente): void
    {
        $this->clienteId = $cliente->id;
        $this->nomeCliente = $cliente->nome ?? '';
        $this->telefoneCliente = $cliente->telefone ?: ($cliente->celular ?? '');
        $this->enderecoCliente = $cliente->endereco ?: ($cliente->logradouro ?? '');
        $this->numeroCliente = $cliente->numero ?? '';
        $this->complementoCliente = $cliente->complemento ?? '';
        $this->bairroCliente = $cliente->bairro ?? '';
        $this->cidadeCliente = $cliente->cidade ?? '';
        $this->ufCliente = $cliente->uf ?? 'PR';
    }

    private function carregarUltimosPedidosPorTelefone(string $telefone): void
    {
        $pedidos = Pedido::query()
            ->with(['itens', 'cliente'])
            ->where(function ($query) use ($telefone) {
                $query->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(telefone, '(', ''), ')', ''), '-', ''), ' ', '') = ?", [$telefone])
                    ->orWhereHas('cliente', function ($cliente) use ($telefone) {
                        $cliente->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(telefone, '(', ''), ')', ''), '-', ''), ' ', '') = ?", [$telefone])
                            ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(celular, '(', ''), ')', ''), '-', ''), ' ', '') = ?", [$telefone]);
                    });
            })
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $this->ultimosPedidos = $pedidos->map(function ($pedido) {
            return [
                'id' => $pedido->id,
                'numero_pedido' => $pedido->numero_pedido,
                'tipo' => $pedido->tipo,
                'total' => (float) $pedido->total,
                'created_at' => optional($pedido->created_at)->format('d/m/Y H:i'),
                'itens' => $pedido->itens->map(fn ($item) => [
                    'nome' => $item->produto_nome,
                    'quantidade' => (float) $item->quantidade,
                ])->toArray(),
            ];
        })->toArray();
    }

    public function adicionarProdutoPorCodigo(): void
    {
        $codigo = trim($this->codigoProduto);

        if ($codigo === '') {
            $this->dispatch('focar-codigo-atendimento');
            return;
        }

        $produto = Produto::query()
            ->where('codigo', $codigo)
            ->where('ativo', true)
            ->first();

        if (!$produto && ctype_digit($codigo)) {
            $produto = Produto::query()
                ->where('id', (int) $codigo)
                ->where('ativo', true)
                ->first();
        }

        if (!$produto) {
            $this->toastWarning("Produto não encontrado: {$codigo}");
            $this->codigoProduto = '';
            $this->quantidadeInput = 1;
            $this->dispatch('resetar-quantidade-atendimento');
            $this->dispatch('focar-codigo-atendimento');
            return;
        }

        $quantidade = max(0.01, (float) $this->quantidadeInput);
        $preco = (float) ($produto->preco_promocional ?: $produto->preco);
        $subtotal = round($quantidade * $preco, 2);

        $chave = (string) $produto->id;

        if (isset($this->carrinho[$chave])) {
            $this->carrinho[$chave]['quantidade'] = round($this->carrinho[$chave]['quantidade'] + $quantidade, 3);
            $this->carrinho[$chave]['subtotal'] = round($this->carrinho[$chave]['quantidade'] * $this->carrinho[$chave]['preco_unitario'], 2);
        } else {
            $this->carrinho[$chave] = [
                'produto_id' => $produto->id,
                'produto_nome' => $produto->nome,
                'quantidade' => $quantidade,
                'preco_unitario' => $preco,
                'subtotal' => $subtotal,
                'observacao' => null,
            ];
        }

        $this->codigoProduto = '';
        $this->quantidadeInput = 1;

        $this->toastSuccess("Produto {$produto->nome} adicionado.");
        $this->dispatch('resetar-quantidade-atendimento');
        $this->dispatch('focar-codigo-atendimento');
    }

    public function removerItem(string $chave): void
    {
        unset($this->carrinho[$chave]);

        $this->toastInfo('Item removido.');
        $this->dispatch('focar-codigo-atendimento');
    }

    public function atualizarQuantidade(string $chave, mixed $quantidade): void
    {
        if (!isset($this->carrinho[$chave])) {
            return;
        }

        $quantidade = (float) $quantidade;

        if ($quantidade <= 0) {
            $this->removerItem($chave);
            return;
        }

        $this->carrinho[$chave]['quantidade'] = round($quantidade, 3);
        $this->carrinho[$chave]['subtotal'] = round(
            $this->carrinho[$chave]['quantidade'] * $this->carrinho[$chave]['preco_unitario'],
            2
        );

        $this->dispatch('focar-codigo-atendimento');
    }

    public function repetirPedido(int $pedidoId): void
    {
        $pedido = Pedido::with('itens')->findOrFail($pedidoId);

        foreach ($pedido->itens as $item) {
            $produto = Produto::find($item->produto_id);

            if (!$produto || !$produto->ativo) {
                continue;
            }

            $chave = (string) $produto->id;
            $quantidade = (float) $item->quantidade;
            $preco = (float) ($produto->preco_promocional ?: $produto->preco);

            if (isset($this->carrinho[$chave])) {
                $this->carrinho[$chave]['quantidade'] += $quantidade;
                $this->carrinho[$chave]['subtotal'] = round($this->carrinho[$chave]['quantidade'] * $this->carrinho[$chave]['preco_unitario'], 2);
            } else {
                $this->carrinho[$chave] = [
                    'produto_id' => $produto->id,
                    'produto_nome' => $produto->nome,
                    'quantidade' => $quantidade,
                    'preco_unitario' => $preco,
                    'subtotal' => round($quantidade * $preco, 2),
                    'observacao' => $item->observacao,
                ];
            }
        }

        $this->toastSuccess('Itens do último pedido adicionados.');
        $this->dispatch('focar-codigo-atendimento');
    }

    public function salvarPedido(): void
    {
        if (!$this->tipoPedido) {
            $this->toastWarning('Selecione o tipo do pedido.');
            return;
        }

        if (empty($this->carrinho)) {
            $this->toastWarning('Adicione ao menos um item.');
            return;
        }

        if ($this->tipoPedido === 'entrega') {
            $this->validate([
                'telefoneCliente' => 'required|string|min:8',
                'nomeCliente' => 'required|string|min:3',
                'enderecoCliente' => 'required|string|min:2',
                'numeroCliente' => 'required|string|min:1',
                'bairroCliente' => 'required|string|min:2',
                'cidadeCliente' => 'required|string|min:2',
            ], [
                'telefoneCliente.required' => 'Informe o telefone.',
                'nomeCliente.required' => 'Informe o nome do cliente.',
                'enderecoCliente.required' => 'Informe o endereço.',
                'numeroCliente.required' => 'Informe o número.',
                'bairroCliente.required' => 'Informe o bairro.',
                'cidadeCliente.required' => 'Informe a cidade.',
            ]);
        }

        if ($this->tipoPedido === 'mesa') {
            $this->validate([
                'mesa' => 'required|string|min:1',
            ], [
                'mesa.required' => 'Informe a mesa ou identificação do consumo local.',
            ]);
        }

        DB::beginTransaction();

        try {
            $cliente = $this->resolverCliente();

            $subtotal = $this->totalCarrinho();
            $taxaEntrega = 0;
            $desconto = 0;
            $total = $subtotal + $taxaEntrega - $desconto;

            if ($this->pedidoAbertoId) {
                $pedido = Pedido::findOrFail($this->pedidoAbertoId);

                $pedido->update([
                    'subtotal' => $subtotal,
                    'taxa_entrega' => $taxaEntrega,
                    'desconto' => $desconto,
                    'total' => $total,
                    'observacoes' => $this->observacao ?: null,
                    'status' => $pedido->status ?: 'pendente',
                ]);

                $pedido->itens()->delete();
            } else {
                $pedido = Pedido::create([
                    'caixa_id' => Caixa::caixaAberto()?->id,
                    'numero_pedido' => Pedido::gerarNumero(),
                    'cliente_id' => $cliente?->id,
                    'tipo' => $this->tipoPedido,
                    'mesa' => $this->tipoPedido === 'mesa' ? $this->mesa : null,
                    'endereco' => $this->tipoPedido === 'entrega' ? $this->enderecoCliente : null,
                    'numero' => $this->tipoPedido === 'entrega' ? $this->numeroCliente : null,
                    'complemento' => $this->tipoPedido === 'entrega' ? $this->complementoCliente : null,
                    'bairro' => $this->tipoPedido === 'entrega' ? $this->bairroCliente : null,
                    'cidade' => $this->tipoPedido === 'entrega' ? $this->cidadeCliente : null,
                    'telefone' => $this->telefoneCliente ?: null,
                    'subtotal' => $subtotal,
                    'taxa_entrega' => $taxaEntrega,
                    'desconto' => $desconto,
                    'total' => $total,
                    'status' => 'pendente',
                    'observacoes' => $this->observacao ?: null,
                    'pagamentos' => null,
                    'atendente_id' => $this->tenantUserId ?? 1,
                ]);
            }

            foreach ($this->carrinho as $item) {
                PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'produto_id' => $item['produto_id'],
                    'produto_nome' => $item['produto_nome'],
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $item['preco_unitario'],
                    'subtotal' => $item['subtotal'],
                    'adicionais' => null,
                    'observacao' => $item['observacao'] ?? null,
                ]);
            }

            DB::commit();

            $numero = $pedido->numero_pedido;

            $this->limparTela(false);

            $this->toastSuccess("Pedido #{$numero} criado com sucesso.");
            $this->dispatch('focar-codigo-atendimento');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->toastError('Erro ao salvar pedido: ' . $e->getMessage());
        }
    }

    private function resolverCliente(): ?Cliente
    {
        $telefone = $this->somenteNumeros($this->telefoneCliente ?: $this->telefoneBusca);

        if (!$telefone && !$this->nomeCliente) {
            return null;
        }

        if ($this->clienteId) {
            $cliente = Cliente::find($this->clienteId);
        } else {
            $cliente = Cliente::query()
                ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(telefone, '(', ''), ')', ''), '-', ''), ' ', '') = ?", [$telefone])
                ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(celular, '(', ''), ')', ''), '-', ''), ' ', '') = ?", [$telefone])
                ->first();

            if (!$cliente) {
                $cliente = new Cliente();
            }
        }

        $cliente->nome = $this->nomeCliente ?: ('Cliente ' . $telefone);
        $cliente->telefone = $telefone ?: null;
        $cliente->celular = $telefone ?: null;
        $cliente->endereco = $this->enderecoCliente ?: null;
        $cliente->logradouro = $this->enderecoCliente ?: null;
        $cliente->numero = $this->numeroCliente ?: null;
        $cliente->complemento = $this->complementoCliente ?: null;
        $cliente->bairro = $this->bairroCliente ?: null;
        $cliente->cidade = $this->cidadeCliente ?: null;
        $cliente->uf = $this->ufCliente ?: 'PR';
        $cliente->ativo = true;
        $cliente->save();

        return $cliente;
    }

    public function limparTela(bool $mostrarToast = true): void
    {
        $this->tipoPedido = '';
        $this->mostrarModalTelefone = false;

        $this->telefoneBusca = '';
        $this->clienteId = null;
        $this->nomeCliente = '';
        $this->telefoneCliente = '';
        $this->enderecoCliente = '';
        $this->numeroCliente = '';
        $this->complementoCliente = '';
        $this->bairroCliente = '';
        $this->cidadeCliente = '';
        $this->ufCliente = 'PR';

        $this->mesa = '';

        $this->codigoProduto = '';
        $this->quantidadeInput = 1;

        $this->carrinho = [];
        $this->ultimosPedidos = [];
        $this->observacao = '';

        $this->resetValidation();

        $this->dispatch('resetar-quantidade-atendimento');
        $this->dispatch('focar-codigo-atendimento');

        if ($mostrarToast) {
            $this->toastInfo('Tela limpa.');
        }
    }

    public function totalCarrinho(): float
    {
        return round(array_sum(array_column($this->carrinho, 'subtotal')), 2);
    }

    public function totalItens(): float
    {
        return round(array_sum(array_column($this->carrinho, 'quantidade')), 3);
    }

    public function labelTipoPedido(): string
    {
        return match ($this->tipoPedido) {
            'entrega' => 'Entrega',
            'balcao' => 'Vem buscar',
            'mesa' => 'Consumo local',
            default => 'Não selecionado',
        };
    }

    private function somenteNumeros(?string $valor): string
    {
        return preg_replace('/[^0-9]/', '', $valor ?? '');
    }

    public function render()
    {
        return view('livewire.tenant.atendimento-rapido.index')
            ->layout('layouts.tenant');
    }
}