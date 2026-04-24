<?php

namespace App\Livewire\Tenant\PDV;

use App\Models\Tenant\Categoria;
use App\Models\Tenant\Produto;
use App\Models\Tenant\Cliente;
use App\Models\Tenant\Caixa;
use App\Models\Tenant\Pedido;
use App\Models\Tenant\Comanda;
use App\Models\Tenant\Configuracao;
use App\Models\Tenant\ComandaItem;
use App\Models\Tenant\ComandaPagamento;
use App\Models\Tenant\PedidoItem;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Url;
use App\Livewire\Traits\WithToast;
use Livewire\Attributes\Computed;
use App\Jobs\EmitirNotaFiscal;

class Index extends Component
{
    use WithToast;

    // Propriedades para o modal de NF
    public $mostrarModalNF = false;
    public $cpfCnpjNF = '';
    public $nomeClienteNF = '';
    public $tipoDocumentoNF = 'CPF';
    public $pedidoTempId = null;

    protected $rules = [
        'cpfCnpjNF' => 'required|string|min:11|max:18',
        'nomeClienteNF' => 'required|string|min:3',
    ];

    // Filtros
    public ?int $categoriaSelecionada = null;
    public string $busca = '';
    public string $codigoProduto = '';
    public float $quantidadeInput = 1;

    // Carrinho
    public array $carrinho = [];

    // Comanda
    public ?int $comandaId = null;
    public bool $modoComanda = false;

    // Venda
    #[Url]
    public ?string $mesa = null;
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

    public function mount(): void
    {
        $this->tenantUserId = \App\Models\Tenant\User::where('email', auth()->user()->email)->value('id');
        $this->carrinho = session('pdv_carrinho', []);
        $this->pagamentos = [];
        $this->valorPendente = 0;
        $this->valorPagamento = 0;
        $this->formaPagamento = 'dinheiro';
        $this->mostrarModalNF = false;

        if ($this->mesa) {
            $this->updatedMesa();
        }

        $this->recalcularPendente();
    }

    private function recalcularPendente(bool $ajustarValorPagamento = true): void
    {
        $totalPagoLocal = round((float) collect($this->pagamentos)->sum('valor'), 2);
        $totalPagoComanda = 0;

        if ($this->modoComanda && $this->comandaId) {
            $comanda = Comanda::find($this->comandaId);
            $totalPagoComanda = (float) ($comanda?->total_pago ?? 0);
        }

        $this->valorPendente = max(
            0,
            round($this->totalCarrinho - $totalPagoComanda - $totalPagoLocal, 2)
        );

        if ($ajustarValorPagamento) {
            $this->valorPagamento = $this->valorPendente > 0 ? $this->valorPendente : 0;
        }
    }

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

    public function selecionarCategoria(?int $categoriaId): void
    {
        $this->categoriaSelecionada = $categoriaId;
    }

    public function adicionarProduto(int $produtoId, ?float $quantidade = null): void
    {
        $produto = Produto::find($produtoId);
        $quantidade = $quantidade ?? $this->quantidadeInput;

        if (! $produto) {
            $this->toastError('Produto não encontrado!');
            return;
        }

        $preco = (float) $produto->preco_atual;
        $quantidade = max(0.01, (float) $quantidade);
        $chave = (string) $produtoId;

        if (isset($this->carrinho[$chave])) {
            $this->carrinho[$chave]['quantidade'] += $quantidade;
            $this->carrinho[$chave]['subtotal'] = round(
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
        $this->quantidadeInput = 1;
        $this->recalcularPendente();
        $this->toastSuccess("Produto {$produto->nome} adicionado!");
    }

    public function removerProduto(int $produtoId): void
    {
        $chave = (string) $produtoId;
        unset($this->carrinho[$chave]);

        $this->salvarCarrinho();
        $this->recalcularPendente();
    }

    public function atualizarQuantidade(int $produtoId, mixed $quantidade): void
    {
        $chave = (string) $produtoId;
        $quantidade = (float) $quantidade;

        if (! isset($this->carrinho[$chave])) {
            return;
        }

        if ($quantidade <= 0) {
            $this->removerProduto($produtoId);
            return;
        }

        $this->carrinho[$chave]['quantidade'] = $quantidade;
        $this->carrinho[$chave]['subtotal'] = round(
            $this->carrinho[$chave]['preco'] * $quantidade,
            2
        );

        $this->salvarCarrinho();
        $this->recalcularPendente();
    }

    public function limparCarrinho(): void
    {
        $this->carrinho = [];
        $this->pagamentos = [];
        $this->valorPendente = 0;
        $this->valorPagamento = 0;

        session()->forget('pdv_carrinho');
        $this->toastInfo('Carrinho limpo!');
    }

    private function salvarCarrinho(): void
    {
        session()->put('pdv_carrinho', $this->carrinho);
    }

    public function abrirPagamento(): void
    {
        if (empty($this->carrinho)) {
            $this->toastWarning('Carrinho vazio! Adicione produtos primeiro.');
            return;
        }

        if ($this->modoComanda) {
            $this->salvarComanda(false);

            $comanda = Comanda::find($this->comandaId);
            $this->valorPendente = $comanda ? (float) $comanda->total_restante : $this->totalCarrinho;
            $this->valorPagamento = $this->valorPendente;
        } else {
            $this->recalcularPendente();
        }

        $this->mostrarPagamento = true;
    }

    public function buscarPorCodigo(): void
    {
        $codigo = trim($this->codigoProduto);

        if (strlen($codigo) < 1) {
            return;
        }

        $produto = Produto::where('codigo', $codigo)
            ->orWhere('id', $codigo)
            ->first();

        if ($produto) {
            $this->adicionarProduto($produto->id, $this->quantidadeInput);
            $this->codigoProduto = '';
            $this->quantidadeInput = 1;
            $this->dispatch('focar-codigo');
        } else {
            $this->toastWarning("Produto não encontrado: {$codigo}");
        }
    }

    public function fecharModalPagamento(): void
    {
        $this->mostrarPagamento = false;
    }

    public function acaoF5(): void
    {
        if ($this->modoComanda) {
            $this->adicionarPagamento();
            return;
        }

        $this->recalcularPendente(false);

        if ($this->valorPendente > 0) {
            $this->adicionarPagamento();
        } else {
            $this->finalizarVenda();
        }
    }

    public function adicionarPagamento(): void
    {
        if (empty($this->carrinho)) {
            $this->toastWarning('Insira ao menos um item no carrinho!');
            return;
        }

        if (!$this->comandaId && $this->mesa) {
            $comanda = Comanda::buscarMesa($this->mesa);
            if ($comanda) {
                $this->comandaId = $comanda->id;
                $this->modoComanda = true;
            }
        }

        $this->recalcularPendente(false);

        $valor = round((float) $this->valorPagamento, 2);

        if ($valor <= 0) {
            $this->toastWarning('Informe um valor para pagamento!');
            return;
        }

        if ($this->modoComanda && $this->comandaId) {
            $this->pagarParcialComanda();
            $this->recalcularPendente();
            return;
        }

        if ($this->valorPendente <= 0) {
            $this->toastInfo('Valores inseridos, agora finalize a venda.');
            return;
        }

        $troco = 0;

        if ($this->formaPagamento === 'dinheiro' && $valor > $this->valorPendente) {
            $troco = round($valor - $this->valorPendente, 2);
            $valorEfetivo = $this->valorPendente;
        } else {
            $valorEfetivo = min($valor, $this->valorPendente);
        }

        $this->pagamentos[] = [
            'forma' => $this->formaPagamento,
            'valor' => round($valorEfetivo, 2),
            'troco' => $troco,
        ];

        $this->recalcularPendente();

        if ($this->valorPendente > 0) {
            $this->toastSuccess("Pagamento adicionado! Restante: R$ " . number_format($this->valorPendente, 2, ',', '.'));
        } else {
            $this->toastSuccess('Pagamento completo! Agora clique em Finalizar Venda.');
        }
    }

    public function removerPagamento(int $index): void
    {
        if (! isset($this->pagamentos[$index])) {
            return;
        }

        array_splice($this->pagamentos, $index, 1);
        $this->recalcularPendente();
        $this->toastInfo('Pagamento removido.');
    }

    public function finalizarVenda(): void
    {
        if (empty($this->carrinho)) {
            $this->toastWarning('Carrinho vazio!');
            return;
        }

        $this->recalcularPendente(false);

        if (!$this->modoComanda && $this->valorPendente > 0) {
            $this->toastWarning('Ainda falta R$ ' . number_format($this->valorPendente, 2, ',', '.') . ' para concluir a venda.');
            return;
        }

        $caixa = Caixa::caixaAberto();

        if (! $caixa) {
            $this->toastError('Nenhum caixa aberto! Abra o caixa antes de vender.');
            $this->mostrarPagamento = false;
            return;
        }

        DB::beginTransaction();

        try {
            $subtotal = $this->totalCarrinho;

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
                'atendente_id'  => $this->tenantUserId ?? 1,
            ]);

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

            $totaisPagamentos = collect($this->pagamentos)
                ->groupBy('forma')
                ->map(fn($grupo) => $grupo->sum('valor'));

            $caixa->increment('total_vendas', $subtotal);
            $caixa->increment('quantidade_vendas');
            $caixa->increment('total_dinheiro', $totaisPagamentos->get('dinheiro', 0));
            $caixa->increment('total_credito', $totaisPagamentos->get('cartao_credito', 0));
            $caixa->increment('total_debito', $totaisPagamentos->get('cartao_debito', 0));
            $caixa->increment('total_pix', $totaisPagamentos->get('pix', 0));
            
            DB::commit();

            // Limpar carrinho e modal de pagamento
            $this->mostrarPagamento = false;
            $this->limparCarrinho();
            $this->mesa = '';
            $this->comanda = '';
            $this->observacao = '';
            $this->modoComanda = false;
            $this->comandaId = null;
            $this->formaPagamento = 'dinheiro';

            // Verifica se deve perguntar sobre emitir nota fiscal
            $config = Configuracao::first();
            
            // Se já tem cliente com CPF/CNPJ, emite automaticamente
            if ($pedido->cliente_id && $pedido->cliente && $pedido->cliente->cpf_cnpj) {
                EmitirNotaFiscal::dispatch($pedido->id, tenant()->id);
                $this->toastSuccess("Pedido #{$pedido->numero_pedido} finalizado! NF solicitada.");
            } 
            // Se não tem cliente ou não tem CPF, pergunta se quer emitir
            elseif ($config && $config->emitir_nf_automatico) {
                $this->pedidoTempId = $pedido->id;
                $this->mostrarModalNF = true;
                $this->toastInfo("Deseja emitir nota fiscal? Preencha os dados.");
            } 
            // Não emite nota
            else {
                $this->toastSuccess("Pedido #{$pedido->numero_pedido} finalizado!");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->toastError('Erro ao finalizar venda: ' . $e->getMessage());
        }
    }

    public function emitirNotaDaVenda()
    {
        $this->validate([
            'cpfCnpjNF' => 'required|string|min:11|max:18',
            'nomeClienteNF' => 'required|string|min:3',
        ]);
        
        $cpfCnpj = preg_replace('/[^0-9]/', '', $this->cpfCnpjNF);
        
        // Criar ou buscar cliente
        $cliente = Cliente::updateOrCreate(
            ['cpf_cnpj' => $cpfCnpj],
            ['nome' => $this->nomeClienteNF, 'ativo' => true]
        );
        
        // Associar ao pedido
        $pedido = Pedido::find($this->pedidoTempId);
        $pedido->cliente_id = $cliente->id;
        $pedido->save();
        
        // Disparar emissão da NF
        EmitirNotaFiscal::dispatch($pedido->id, tenant()->id);
        
        $this->mostrarModalNF = false;
        $this->pedidoTempId = null;
        $this->reset(['cpfCnpjNF', 'nomeClienteNF']);
        
        $this->toastSuccess("Pedido #{$pedido->numero_pedido} finalizado com NF solicitada!");
    }

    public function finalizarSemNF()
    {
        $this->mostrarModalNF = false;
        $this->pedidoTempId = null;
        $this->reset(['cpfCnpjNF', 'nomeClienteNF']);
        $this->toastSuccess("Pedido finalizado sem nota fiscal!");
    }

    public function updatedMesa(): void
    {
        $mesa = trim($this->mesa);
        if (strlen($mesa) < 1) {
            $this->modoComanda = false;
            $this->comandaId = null;
            $this->carrinho = [];
            $this->pagamentos = [];
            $this->salvarCarrinho();
            $this->recalcularPendente();
            return;
        }

        $comanda = Comanda::buscarMesa($mesa);

        if ($comanda) {
            $this->comandaId = $comanda->id;
            $this->modoComanda = true;
            $this->carrinho = [];
            $this->pagamentos = [];

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
            $this->recalcularPendente();
            $this->toastSuccess("Mesa {$mesa} carregada com sucesso!");
        } else {
            $this->modoComanda = true;
            $this->comandaId = null;
            $this->carrinho = [];
            $this->pagamentos = [];
            $this->salvarCarrinho();
            $this->recalcularPendente();
            $this->toastInfo("Nova mesa {$mesa} criada. Adicione os itens.");
        }
    }

    public function salvarComanda(bool $limparAposalvar = true): void
    {
        if (empty($this->carrinho)) {
            $this->toastWarning('Carrinho vazio! Adicione itens para salvar a mesa.');
            return;
        }

        $caixa = Caixa::caixaAberto();
        $total = $this->totalCarrinho;

        if ($this->comandaId) {
            $comanda = Comanda::find($this->comandaId);
            $comanda->itens()->delete();
        } else {
            $comanda = Comanda::buscarMesa($this->mesa);

            if (! $comanda) {
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

        if ($limparAposalvar) {
            $mesaSalva = $comanda->mesa;

            $this->limparCarrinho();
            $this->mesa = '';
            $this->modoComanda = false;
            $this->comandaId = null;
            $this->formaPagamento = 'dinheiro';

            $this->toastSuccess("Mesa {$mesaSalva} salva! Continue depois.");
        } else {
            $this->recalcularPendente();
            $this->toastSuccess("Itens adicionados à mesa {$comanda->mesa}.");
        }
    }

    public function pagarParcialComanda(): void
    {
        if (! $this->comandaId) {
            $this->salvarComanda(false);
        }

        $comanda = Comanda::find($this->comandaId);

        if (! $comanda) {
            $this->toastError('Comanda não encontrada.');
            return;
        }

        $valorInformado = round((float) $this->valorPagamento, 2);

        if ($valorInformado <= 0) {
            $this->toastWarning('Informe um valor para pagamento.');
            return;
        }

        $restante = (float) $comanda->total_restante;
        $troco = 0;

        if ($this->formaPagamento === 'dinheiro' && $valorInformado > $restante) {
            $troco = round($valorInformado - $restante, 2);
            $valorEfetivo = $restante;
        } else {
            $valorEfetivo = min($valorInformado, $restante);
        }

        if ($valorEfetivo <= 0) {
            $this->toastWarning('Valor inválido!');
            return;
        }

        ComandaPagamento::create([
            'comanda_id' => $comanda->id,
            'forma'      => $this->formaPagamento,
            'valor'      => $valorEfetivo,
        ]);

        $comanda->increment('total_pago', $valorEfetivo);
        $comanda->refresh();

        if ($comanda->total_restante <= 0) {
            $this->fecharComanda($comanda);
            return;
        }

        $this->valorPendente = (float) $comanda->total_restante;
        $this->valorPagamento = (float) $comanda->total_restante;

        $mensagem = "Pagamento lançado! Restante: R$ " . number_format($comanda->total_restante, 2, ',', '.');

        if ($troco > 0) {
            $mensagem .= " | Troco: R$ " . number_format($troco, 2, ',', '.');
        }

        $this->toastSuccess($mensagem);
    }

    private function fecharComanda(Comanda $comanda): void
    {
        DB::beginTransaction();

        try {
            $comanda->update([
                'status'     => 'fechada',
                'fechada_em' => now(),
            ]);

            $caixa = Caixa::caixaAberto();

            if ($caixa) {
                $pagamentos = $comanda->pagamentos->map(fn($p) => [
                    'forma' => $p->forma,
                    'valor' => (float) $p->valor,
                    'troco' => 0,
                ])->toArray();

                $pedido = Pedido::create([
                    'caixa_id'      => $caixa->id,
                    'numero_pedido' => Pedido::gerarNumero(),
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
                    PedidoItem::create([
                        'pedido_id'      => $pedido->id,
                        'produto_id'     => $item->produto_id,
                        'produto_nome'   => $item->produto_nome,
                        'quantidade'     => $item->quantidade,
                        'preco_unitario' => $item->preco_unitario,
                        'subtotal'       => $item->subtotal,
                    ]);
                }

                $totais = $comanda->pagamentos
                    ->groupBy('forma')
                    ->map(fn($grupo) => $grupo->sum('valor'));

                $caixa->increment('total_vendas', (float) $comanda->total);
                $caixa->increment('quantidade_vendas');
                $caixa->increment('total_dinheiro', $totais->get('dinheiro', 0));
                $caixa->increment('total_credito', $totais->get('cartao_credito', 0));
                $caixa->increment('total_debito', $totais->get('cartao_debito', 0));
                $caixa->increment('total_pix', $totais->get('pix', 0));
            }

            DB::commit();

            $mesaFechada = $comanda->mesa;

            $this->mostrarPagamento = false;
            $this->limparCarrinho();
            $this->mesa = '';
            $this->comanda = '';
            $this->comandaId = null;
            $this->modoComanda = false;
            $this->formaPagamento = 'dinheiro';

            $this->toastSuccess("Mesa {$mesaFechada} fechada com sucesso!");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->toastError('Erro ao fechar comanda: ' . $e->getMessage());
        }
    }

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
                fn($q) => $q->where('categoria_id', $this->categoriaSelecionada)
            )
            ->when(
                $this->busca,
                fn($q) => $q->where(function ($q2) {
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