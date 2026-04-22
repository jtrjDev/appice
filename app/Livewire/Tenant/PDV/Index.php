<?php

namespace App\Livewire\Tenant\PDV;

use App\Models\Tenant\Categoria;
use App\Models\Tenant\Produto;
use App\Models\Tenant\Cliente;
use App\Models\Tenant\Caixa;
use App\Models\Tenant\Pedido;
use App\Models\Tenant\Comanda;
use App\Models\Tenant\ComandaItem;
use App\Models\Tenant\ComandaPagamento;
use App\Models\Tenant\PedidoItem;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Computed;

class Index extends Component
{
    // Filtros
    public ?int $categoriaSelecionada = null;
    public string $busca = '';
    public string $codigoProduto = '';
    // Adiciona a propriedade
    public float $quantidadeInput = 1;

    // Carrinho
    public array $carrinho = [];

    // Comanda
    public ?int $comandaId = null;
    public bool $modoComanda = false;

    // Venda
    public string $mesa = '';
    public string $comanda = '';
    public ?int $clienteId = null;
    public string $observacao = '';

    // Pagamento
    public bool $mostrarPagamento = false;
    public array $pagamentos = [];
    public float $valorPendente = 0;
    public float $valorPagamento = 0;
    public string $formaPagamento = 'dinheiro';
    public ?int $tenantUserId = null;

    // ─────────────────────────────────────────
    // Boot / Mount
    // ─────────────────────────────────────────

    // No mount() adiciona:
    public function mount(?string $mesa = null): void
    {
        $this->tenantUserId = \App\Models\Tenant\User::where('email', auth()->user()->email)->value('id');
        $this->carrinho     = session('pdv_carrinho', []);
        $this->pagamentos   = session('pdv_pagamentos', []);

        if ($mesa) {
            $this->mesa = $mesa;
            $this->updatedMesa();
        }
    }

    // ─────────────────────────────────────────
    // Computed properties  (Livewire v3)
    // ─────────────────────────────────────────

    #[Computed]
    public function totalCarrinho(): float
    {
        return (float) array_sum(array_column($this->carrinho, 'subtotal'));
    }

    #[Computed]
    public function caixaAberto(): ?Caixa
    {
        return Caixa::caixaAberto();
    }

    #[Computed]
    public function totalItens(): float
    {
        return (float) array_sum(array_column($this->carrinho, 'quantidade'));
    }

    // ─────────────────────────────────────────
    // Código de barras / ID
    // ─────────────────────────────────────────

    public function updatedCodigoProduto(): void
    {
        $codigo = trim($this->codigoProduto);

        if (strlen($codigo) < 1) return;

        $produto = Produto::where('codigo', $codigo)
            ->orWhere('id', $codigo)
            ->first();

        if ($produto) {
            $this->adicionarProduto($produto->id);
            $this->codigoProduto = '';
        } else {
            $this->dispatch('pdv-aviso', mensagem: 'Produto não encontrado: ' . $codigo);
        }
    }

    // ─────────────────────────────────────────
    // Categorias
    // ─────────────────────────────────────────

    public function selecionarCategoria(?int $categoriaId): void
    {
        $this->categoriaSelecionada = $categoriaId;
    }

    // ─────────────────────────────────────────
    // Carrinho
    // ─────────────────────────────────────────

    public function adicionarProduto(int $produtoId, ?float $quantidade = null): void
    {
        $produto = Produto::find($produtoId);
        $quantidade = $quantidade ?? $this->quantidadeInput;

        if (! $produto) {
            $this->dispatch('pdv-aviso', mensagem: 'Produto não encontrado.');
            return;
        }

        $preco    = (float) $produto->preco_atual;
        $quantidade = max(0.01, $quantidade);

        $chave = (string) $produtoId;

        if (isset($this->carrinho[$chave])) {
            $this->carrinho[$chave]['quantidade'] += $quantidade;
            $this->carrinho[$chave]['subtotal']    = round(
                $this->carrinho[$chave]['preco'] * $this->carrinho[$chave]['quantidade'],
                2
            );
        } else {
            $this->carrinho[$chave] = [
                'id'              => $produto->id,
                'nome'            => $produto->nome,
                'preco'           => $preco,
                'preco_formatado' => 'R$ ' . number_format($preco, 2, ',', '.'),
                'quantidade'      => $quantidade,
                'subtotal'        => round($preco * $quantidade, 2),
            ];
        }

        $this->salvarCarrinho();
        // Reseta quantidade para 1 após adicionar
        $this->quantidadeInput = 1;
    }

    public function removerProduto(int $produtoId): void
    {
        $chave = (string) $produtoId;
        unset($this->carrinho[$chave]);
        $this->salvarCarrinho();
    }

    public function atualizarQuantidade(int $produtoId, mixed $quantidade): void
    {
        $chave      = (string) $produtoId;
        $quantidade = (float) $quantidade;

        if (! isset($this->carrinho[$chave])) return;

        if ($quantidade <= 0) {
            $this->removerProduto($produtoId);
            return;
        }

        $this->carrinho[$chave]['quantidade'] = $quantidade;
        $this->carrinho[$chave]['subtotal']   = round(
            $this->carrinho[$chave]['preco'] * $quantidade,
            2
        );

        $this->salvarCarrinho();
    }

    public function limparCarrinho(): void
    {
        $this->carrinho = [];
        session()->forget('pdv_carrinho');
    }

    private function salvarCarrinho(): void
    {
        session()->put('pdv_carrinho', $this->carrinho);
    }

    // ─────────────────────────────────────────
    // Pagamento
    // ─────────────────────────────────────────

    public function abrirPagamento(): void
    {
        if (empty($this->carrinho)) {
            $this->dispatch('pdv-aviso', mensagem: 'Carrinho vazio!');
            return;
        }

        // Modo comanda — salva primeiro
        if ($this->modoComanda) {
            $this->salvarComanda();
            $comanda = Comanda::find($this->comandaId);
            $this->valorPendente  = $comanda ? $comanda->total_restante : $this->totalCarrinho;
        } else {
            $this->valorPendente = $this->totalCarrinho;
        }

        $this->pagamentos     = [];
        $this->valorPagamento = round($this->valorPendente, 2);
        $this->formaPagamento = 'dinheiro';
        $this->mostrarPagamento = true;
    }

    public function buscarPorCodigo(): void
    {
        $codigo = trim($this->codigoProduto);

        if (strlen($codigo) < 1) return;

        $produto = Produto::where('codigo', $codigo)
            ->orWhere('id', $codigo)
            ->first();

        if ($produto) {
            $this->adicionarProduto($produto->id, $this->quantidadeInput);
            $this->codigoProduto = '';
            $this->quantidadeInput = 1;
            // Foca de volta no código
            $this->dispatch('focar-codigo');
        } else {
            $this->dispatch('pdv-aviso', mensagem: 'Produto não encontrado: ' . $codigo);
        }
    }

    public function fecharModalPagamento(): void
    {
        $this->mostrarPagamento = false;
    }

    public function adicionarPagamento(): void
    {
        $valor = (float) $this->valorPagamento;

        if ($valor <= 0) {
            $this->dispatch('pdv-aviso', mensagem: 'Informe um valor válido.');
            return;
        }

        // Modo comanda — pagamento parcial
        if ($this->modoComanda && $this->comandaId) {
            $this->pagarParcialComanda();
            return;
        }

        // Modo balcão — comportamento original
        $troco = 0;

        if ($this->formaPagamento === 'dinheiro' && $valor > $this->valorPendente) {
            $troco        = round($valor - $this->valorPendente, 2);
            $valorEfetivo = $this->valorPendente;
        } else {
            $valorEfetivo = min($valor, $this->valorPendente);
        }

        $this->pagamentos[] = [
            'forma' => $this->formaPagamento,
            'valor' => round($valorEfetivo, 2),
            'troco' => $troco,
        ];

        $this->valorPendente = round($this->valorPendente - $valorEfetivo, 2);

        if ($this->valorPendente <= 0) {
            $this->finalizarVenda();
            return;
        }

        $this->valorPagamento = $this->valorPendente;
    }

    public function removerPagamento(int $index): void
    {
        if (! isset($this->pagamentos[$index])) return;

        $this->valorPendente += $this->pagamentos[$index]['valor'];
        $this->valorPendente  = round($this->valorPendente, 2);

        array_splice($this->pagamentos, $index, 1);
    }

    public function finalizarVenda(): void
    {
        // Verifica se tem caixa aberto
        $caixa = Caixa::caixaAberto();

        if (!$caixa) {
            $this->dispatch('pdv-aviso', mensagem: 'Nenhum caixa aberto! Abra o caixa antes de vender.');
            $this->mostrarPagamento = false;
            return;
        }

        DB::beginTransaction();

        try {
            $subtotal = $this->totalCarrinho;

            // Cria o pedido
            $pedido = Pedido::create([
                'caixa_id'      => $caixa->id,
                'numero_pedido' => Pedido::gerarNumero(),
                'cliente_id'    => $this->clienteId ?: null,
                'tipo'          => 'balcao',
                'mesa'          => $this->mesa ?: null,
                'subtotal'      => $subtotal,
                'taxa_entrega'  => 0,
                'desconto'      => 0,
                'total'         => $subtotal,
                'status'        => 'entregue',
                'pagamentos'    => $this->pagamentos,
                // No finalizarVenda() troca para:
                'atendente_id' => $this->tenantUserId ?? 1,
            ]);

            // Cria os itens
            foreach ($this->carrinho as $item) {
                PedidoItem::create([
                    'pedido_id'      => $pedido->id,
                    'produto_id'     => $item['id'],
                    'produto_nome'   => $item['nome'],
                    'quantidade'     => $item['quantidade'],
                    'preco_unitario' => $item['preco'],
                    'subtotal'       => $item['subtotal'],
                ]);
            }

            // Atualiza totais do caixa
            $totaisPagamentos = collect($this->pagamentos)->groupBy('forma')->map(fn($g) => $g->sum('valor'));

            $caixa->increment('total_vendas', $subtotal);
            $caixa->increment('quantidade_vendas');
            $caixa->increment('total_dinheiro', $totaisPagamentos->get('dinheiro', 0));
            $caixa->increment('total_credito',  $totaisPagamentos->get('cartao_credito', 0));
            $caixa->increment('total_debito',   $totaisPagamentos->get('cartao_debito', 0));
            $caixa->increment('total_pix',      $totaisPagamentos->get('pix', 0));

            DB::commit();

            $this->mostrarPagamento = false;
            $this->limparCarrinho();
            $this->reset(['mesa', 'comanda', 'clienteId', 'observacao', 'pagamentos', 'valorPendente']);

            $this->dispatch('pdv-sucesso', mensagem: "Pedido #{$pedido->numero_pedido} finalizado!");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('pdv-aviso', mensagem: 'Erro ao finalizar venda: ' . $e->getMessage());
        }
    }


    // ─────────────────────────────────────────
    // Comanda / Mesa
    // ─────────────────────────────────────────

    public function updatedMesa(): void
    {
        $mesa = trim($this->mesa);

        if (strlen($mesa) < 1) {
            $this->modoComanda = false;
            $this->comandaId   = null;
            $this->carrinho    = [];
            $this->salvarCarrinho();
            return;
        }

        $comanda = Comanda::buscarMesa($mesa);

        if ($comanda) {
            $this->comandaId   = $comanda->id;
            $this->modoComanda = true;
            $this->carrinho    = [];

            foreach ($comanda->itens as $item) {
                $chave = (string) $item->produto_id;
                $this->carrinho[$chave] = [
                    'id'              => $item->produto_id,
                    'nome'            => $item->produto_nome,
                    'preco'           => (float) $item->preco_unitario,
                    'preco_formatado' => 'R$ ' . number_format($item->preco_unitario, 2, ',', '.'),
                    'quantidade'      => (float) $item->quantidade,
                    'subtotal'        => (float) $item->subtotal,
                ];
            }

            $this->salvarCarrinho();
            $this->dispatch('pdv-sucesso', mensagem: 'Mesa ' . $mesa . ' carregada!');
        } else {
            $this->modoComanda = true;
            $this->comandaId   = null;
            $this->carrinho    = [];
            $this->salvarCarrinho();
        }
    }

    public function salvarComanda(): void
{
    if (empty($this->carrinho)) return;

    $caixa = Caixa::caixaAberto();
    $total = $this->totalCarrinho;

    if ($this->comandaId) {
        // Atualiza comanda existente
        $comanda = Comanda::find($this->comandaId);
        $comanda->itens()->delete();
    } else {
        // Verifica se já existe comanda aberta para essa mesa
        $comanda = Comanda::buscarMesa($this->mesa);

        if (!$comanda) {
            $comanda = Comanda::create([
                'caixa_id'   => $caixa?->id,
                'mesa'       => $this->mesa,
                'status'     => 'aberta',
                'total'      => $total,
                'total_pago' => 0,
            ]);
        } else {
            $comanda->itens()->delete();
        }

        $this->comandaId = $comanda->id;
    }

    foreach ($this->carrinho as $item) {
        ComandaItem::create([
            'comanda_id'     => $comanda->id,
            'produto_id'     => $item['id'],
            'produto_nome'   => $item['nome'],
            'quantidade'     => $item['quantidade'],
            'preco_unitario' => $item['preco'],
            'subtotal'       => $item['subtotal'],
        ]);
    }

    $comanda->update(['total' => $total]);

    $this->limparCarrinho();
    $this->mesa       = '';
    $this->modoComanda = false;
    $this->comandaId  = null;

    $this->dispatch('pdv-sucesso', mensagem: 'Mesa ' . $comanda->mesa . ' salva!');
}

    public function pagarParcialComanda(): void
    {
        if (!$this->comandaId) {
            $this->salvarComanda();
        }

        $comanda = Comanda::find($this->comandaId);

        if (!$comanda) return;

        $valorPagamento = (float) $this->valorPagamento;

        if ($valorPagamento <= 0 || $valorPagamento > $comanda->total_restante) {
            $this->dispatch('pdv-aviso', mensagem: 'Valor inválido!');
            return;
        }

        // Registra pagamento parcial
        ComandaPagamento::create([
            'comanda_id' => $comanda->id,
            'forma'      => $this->formaPagamento,
            'valor'      => $valorPagamento,
        ]);

        $comanda->increment('total_pago', $valorPagamento);
        $comanda->refresh();

        // Se quitou tudo fecha a comanda
        if ($comanda->total_restante <= 0) {
            $this->fecharComanda($comanda);
            return;
        }

        $this->valorPendente  = $comanda->total_restante;
        $this->valorPagamento = $comanda->total_restante;
        $this->dispatch('pdv-sucesso', mensagem: 'Pagamento lançado! Restante: R$ ' . number_format($comanda->total_restante, 2, ',', '.'));
    }

    private function fecharComanda(Comanda $comanda): void
    {
        $comanda->update([
            'status'     => 'fechada',
            'fechada_em' => now(),
        ]);

        // Gera pedido no caixa
        $caixa = Caixa::caixaAberto();

        if ($caixa) {
            $pagamentos = $comanda->pagamentos->map(fn($p) => [
                'forma' => $p->forma,
                'valor' => (float) $p->valor,
                'troco' => 0,
            ])->toArray();

            $pedido = \App\Models\Tenant\Pedido::create([
                'caixa_id'      => $caixa->id,
                'numero_pedido' => \App\Models\Tenant\Pedido::gerarNumero(),
                'tipo'          => 'mesa',
                'mesa'          => $comanda->mesa,
                'subtotal'      => $comanda->total,
                'taxa_entrega'  => 0,
                'desconto'      => 0,
                'total'         => $comanda->total,
                'status'        => 'entregue',
                'pagamentos'    => $pagamentos,
                'atendente_id'  => $this->tenantUserId ?? 1,
            ]);

            foreach ($comanda->itens as $item) {
                \App\Models\Tenant\PedidoItem::create([
                    'pedido_id'      => $pedido->id,
                    'produto_id'     => $item->produto_id,
                    'produto_nome'   => $item->produto_nome,
                    'quantidade'     => $item->quantidade,
                    'preco_unitario' => $item->preco_unitario,
                    'subtotal'       => $item->subtotal,
                ]);
            }

            // Atualiza totais do caixa
            $totais = $comanda->pagamentos->groupBy('forma')->map(fn($g) => $g->sum('valor'));
            $caixa->increment('total_vendas', (float) $comanda->total);
            $caixa->increment('quantidade_vendas');
            $caixa->increment('total_dinheiro', $totais->get('dinheiro', 0));
            $caixa->increment('total_credito',  $totais->get('cartao_credito', 0));
            $caixa->increment('total_debito',   $totais->get('cartao_debito', 0));
            $caixa->increment('total_pix',      $totais->get('pix', 0));
        }

        $this->mostrarPagamento = false;
        $this->limparCarrinho();
        $this->reset(['mesa', 'comandaId', 'modoComanda', 'pagamentos', 'valorPendente']);
        $this->dispatch('pdv-sucesso', mensagem: 'Mesa ' . $comanda->mesa . ' fechada!');
    }




    // ─────────────────────────────────────────
    // Render
    // ─────────────────────────────────────────





    public function render()
    {
        $categorias = Categoria::query()
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        $produtos = Produto::query()
            ->where('ativo', true)
            ->when(
                $this->categoriaSelecionada,
                fn($q) =>
                $q->where('categoria_id', $this->categoriaSelecionada)
            )
            ->when(
                $this->busca,
                fn($q) =>
                $q->where(function ($q2) {
                    $q2->where('nome', 'like', '%' . $this->busca . '%')
                        ->orWhere('codigo', 'like', '%' . $this->busca . '%');
                })
            )
            ->orderBy('nome')
            ->get();

        $clientes = Cliente::query()
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        return view('livewire.tenant.pdv.index', compact('categorias', 'produtos', 'clientes'))
            ->layout('layouts.tenant');
    }
}
